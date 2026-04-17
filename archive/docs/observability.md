# Laravel Observability Stack (LGTM + OTEL)

observability stack based on OpenTelemetry and the Grafana LGTM stack (Loki, Grafana, Tempo, Mimir/Prometheus).

## Architecture

- **Laravel App**: Instrumented with OpenTelemetry SDK.
- **OpenTelemetry Collector**: Receives traces, metrics, and logs from the app and exports them to the backend storage.
- **Prometheus**: Stores metrics from exporters and the OTEL collector.
- **Loki**: Stores logs collected by Promtail and the OTEL collector.
- **Tempo**: Stores distributed traces.
- **Grafana**: Unified visualization and alerting dashboard.

## Exporters

- **Node Exporter**: Host metrics (CPU, Memory, Disk).
- **Postgres Exporter**: Database performance metrics.
- **Redis Exporter**: Redis cache/queue metrics.
- **cAdvisor**: Docker container resource metrics.

## Getting Started

1. **Environment Variables**: Ensure the following exists in your `.env`:

    ```env
    OTEL_SERVICE_NAME=laravel-app
    OTEL_EXPORTER_OTLP_ENDPOINT=http://otel-collector:4318
    OTEL_EXPORTER_OTLP_PROTOCOL=http/protobuf
    ```

2. **Start the Stack**: using Laravel Sail, just run:

    ```bash
    ./vendor/bin/sail up -d
    ```

3. **Access Dashboards**:
    - **Grafana**: [http://grafana.localhost](http://grafana.localhost) (credentials: `.env`)
    - **Prometheus**: [http://prometheus.localhost](http://prometheus.localhost)
    - **Alertmanager**: [http://alertmanager.localhost](http://alertmanager.localhost)

## Observability Features

- **Distributed Traces**: Every HTTP request, Database query, Redis command, and Queue job is traced. View them in Grafana under the "Tempo" datasource.
- **Structured Logs**: Application logs are sent to Loki. Use the "Loki" datasource in Grafana to query logs with labels like `container`, `service_name`, etc.
- **Metrics**: Real-time performance metrics are available in Prometheus and pre-configured dashboards in Grafana.

## Alerting

Pre-configured alerts for:

- High CPU Usage (>80%)
- High Memory Usage (>90%)
- Database Down

Alerts can be managed in Alertmanager or Grafana Alerting.

## 📖 Related Documentation

- [Architecture](architecture.md)
- [Development](development.md)
- [Infrastructure](infrastructure.md)
- [Tools](tools.md)
