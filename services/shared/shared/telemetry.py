"""
Shared OpenTelemetry setup for all Posexei services.

Usage in FastAPI services:
    # main.py is auto-instrumented via CMD opentelemetry-instrument wrapper

Usage in workers:
    from shared.telemetry import setup_telemetry, get_tracer, get_meter
    setup_telemetry("social-post-worker")
    tracer = get_tracer()
"""
import os
import logging
import structlog
from contextlib import contextmanager
from typing import Generator, Optional

def add_otel_context(logger, method_name, event_dict):
    """Processor to inject traceId and spanId into structlog event_dict."""
    try:
        from opentelemetry import trace
        span = trace.get_current_span()
        if span and span.get_span_context().is_valid:
            event_dict["traceId"] = format(span.get_span_context().trace_id, "032x")
            event_dict["spanId"] = format(span.get_span_context().span_id, "016x")
    except ImportError:
        pass
    return event_dict

def setup_logging(service_name: str, level: str = "INFO"):
    """
    Configure structlog to output JSON and inject OTel trace context.
    Also redirects standard logging to structlog.
    """
    logging.basicConfig(
        format="%(message)s",
        level=getattr(logging, level.upper(), logging.INFO),
    )

    structlog.configure(
        processors=[
            structlog.contextvars.merge_contextvars,
            structlog.processors.add_log_level,
            structlog.processors.format_exc_info,
            structlog.processors.TimeStamper(fmt="iso"),
            add_otel_context,
            structlog.processors.JSONRenderer(),
        ],
        logger_factory=structlog.stdlib.LoggerFactory(),
        wrapper_class=structlog.stdlib.BoundLogger,
        cache_logger_on_first_use=True,
    )

logger = structlog.get_logger(__name__)

# ---------------------------------------------------------------------------
# Lazy OTel imports — gracefully no-op if SDK not installed
# ---------------------------------------------------------------------------
try:
    from opentelemetry import trace, metrics
    from opentelemetry.sdk.trace import TracerProvider
    from opentelemetry.sdk.trace.export import BatchSpanProcessor
    from opentelemetry.sdk.metrics import MeterProvider
    from opentelemetry.sdk.metrics.export import PeriodicExportingMetricReader
    from opentelemetry.sdk.resources import Resource, SERVICE_NAME
    from opentelemetry.exporter.otlp.proto.grpc.trace_exporter import OTLPSpanExporter
    from opentelemetry.exporter.otlp.proto.grpc.metric_exporter import OTLPMetricExporter
    _OTEL_AVAILABLE = True
except ImportError:
    _OTEL_AVAILABLE = False
    logger.warning("OpenTelemetry SDK not installed — telemetry is disabled. "
                   "Install opentelemetry-sdk and opentelemetry-exporter-otlp-proto-grpc.")

_tracer: Optional[object] = None
_meter: Optional[object] = None

# Metric instruments (created lazily after setup_telemetry is called)
_jobs_counter = None
_job_duration_histogram = None
_job_retries_counter = None
_dlq_depth_gauge = None


def setup_telemetry(service_name: str) -> None:
    """Bootstrap TracerProvider and MeterProvider, wiring to the OTel Collector via OTLP gRPC."""
    global _tracer, _meter, _jobs_counter, _job_duration_histogram, _job_retries_counter, _dlq_depth_gauge

    if not _OTEL_AVAILABLE:
        return

    endpoint = os.getenv("OTEL_EXPORTER_OTLP_ENDPOINT", "http://otel-collector:4317")

    resource = Resource(attributes={SERVICE_NAME: service_name})

    # --- Traces ---
    span_exporter = OTLPSpanExporter(endpoint=endpoint, insecure=True)
    tracer_provider = TracerProvider(resource=resource)
    tracer_provider.add_span_processor(BatchSpanProcessor(span_exporter))
    trace.set_tracer_provider(tracer_provider)
    _tracer = trace.get_tracer(service_name)

    # --- Metrics ---
    metric_exporter = OTLPMetricExporter(endpoint=endpoint, insecure=True)
    reader = PeriodicExportingMetricReader(metric_exporter, export_interval_millis=15_000)
    meter_provider = MeterProvider(resource=resource, metric_readers=[reader])
    metrics.set_meter_provider(meter_provider)
    _meter = metrics.get_meter(service_name)

    # Pre-create the job system instruments
    _jobs_counter = _meter.create_counter(
        name="posexei.jobs",
        description="Total jobs processed",
        unit="{job}",
    )
    _job_duration_histogram = _meter.create_histogram(
        name="posexei.job.duration",
        description="Job processing duration in seconds",
        unit="s",
    )
    _job_retries_counter = _meter.create_counter(
        name="posexei.job.retries",
        description="Total job retries",
        unit="{retry}",
    )
    _dlq_depth_gauge = _meter.create_up_down_counter(
        name="posexei.dlq.depth",
        description="Number of messages in the Dead Letter Queue",
        unit="{message}",
    )

    logger.info(f"[telemetry] OTel initialized for service={service_name} endpoint={endpoint}")


def get_tracer():
    """Return the global tracer (or a no-op tracer if OTel is unavailable)."""
    if _OTEL_AVAILABLE:
        return trace.get_tracer(__name__)
    return _NoOpTracer()


def get_meter():
    """Return the global meter."""
    return _meter


def record_job_start(job_type: str, attempt: int) -> None:
    if _jobs_counter is not None:
        _jobs_counter.add(0, {"job_type": job_type, "attempt": str(attempt)})


def record_job_success(job_type: str, duration_s: float) -> None:
    if _jobs_counter is not None:
        _jobs_counter.add(1, {"job_type": job_type, "status": "success"})
    if _job_duration_histogram is not None:
        _job_duration_histogram.record(duration_s, {"job_type": job_type, "status": "success"})


def record_job_failure(job_type: str, duration_s: float, retryable: bool) -> None:
    status = "retry" if retryable else "dlq"
    if _jobs_counter is not None:
        _jobs_counter.add(1, {"job_type": job_type, "status": status})
    if _job_duration_histogram is not None:
        _job_duration_histogram.record(duration_s, {"job_type": job_type, "status": status})
    if retryable and _job_retries_counter is not None:
        _job_retries_counter.add(1, {"job_type": job_type})


def record_dlq_depth_change(stream: str, delta: int) -> None:
    if _dlq_depth_gauge is not None:
        _dlq_depth_gauge.add(delta, {"stream": stream})


# ---------------------------------------------------------------------------
# No-op fallback
# ---------------------------------------------------------------------------
class _NoOpSpan:
    def __enter__(self): return self
    def __exit__(self, *_): pass
    def set_attribute(self, *_): pass
    def set_status(self, *_): pass
    def record_exception(self, *_): pass


class _NoOpTracer:
    @contextmanager
    def start_as_current_span(self, name: str, **_kwargs) -> Generator:
        yield _NoOpSpan()
