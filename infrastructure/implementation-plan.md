# Observability Stack Integration Plan

## Overview

Instrument every Python service with the **OpenTelemetry SDK**, then wire all three observability signals (metrics, logs, traces) into a **Grafana** stack backed by **Prometheus** (metrics), **Loki** (logs), and **Tempo** (traces). An **OTel Collector** acts as the central fan-out hub.

```
Service → OTel SDK (OTLP) → OTel Collector ─┬─→ Tempo (traces)
                                             ├─→ Prometheus (metrics) → Grafana
                                             └─→ Loki (logs)
Promtail ─────────────────────────────────────→ Loki
```

## User Review Required

> [!IMPORTANT]
> **Auto-instrumentation**: Services will use `opentelemetry-instrument` as a CMD wrapper — instruments FastAPI, Redis, and httpx automatically with zero Python source changes. Workers call `setup_tracing()` manually since they have no HTTP server.

> [!WARNING]
> **Ports**: Grafana at `http://grafana.localhost` (via Traefik). Prometheus at `localhost:9090`. Grafana login: `admin / admin`.

## Proposed Changes

---

### Infrastructure: New Containers

| Service | Image | Purpose |
|---|---|---|
| `otel-collector` | `otel/opentelemetry-collector-contrib:0.120.0` | Fan-out hub |
| `prometheus` | `prom/prometheus:v3.3.1` | Metric storage |
| `loki` | `grafana/loki:3.4.3` | Log aggregation |
| `promtail` | `grafana/promtail:3.4.3` | Docker log collector |
| `tempo` | `grafana/tempo:2.7.2` | Trace storage |
| `grafana` | `grafana/grafana:11.6.1` | Dashboards |
| `k6` *(optional)* | `grafana/k6:0.56.0` | Load testing |

---

### Infrastructure: Config Files (`infrastructure/observability/`)

#### [NEW] `otel-collector-config.yaml`
- OTLP gRPC `:4317`, HTTP `:4318`
- Exports: traces → Tempo, metrics → Prometheus, logs → Loki

#### [NEW] `prometheus.yml`
- Scrapes OTel Collector + each service `/metrics` at 15s interval

#### [NEW] `loki-config.yaml` + `tempo-config.yaml`
- Single-binary, local filesystem, 72h retention

#### [NEW] `promtail-config.yaml`
- Tails Docker container logs, parses `traceId` for Grafana log→trace links

#### [NEW] `grafana/provisioning/`
- Auto-provisions Prometheus, Loki, Tempo datasources
- Pre-built **Posexei Overview** dashboard: request rate, p95 latency, job duration, DLQ count, error rate

---

### Shared Module (`services/shared/`)

#### [MODIFY] `pyproject.toml` — add OTel dependencies

#### [NEW] `shared/telemetry.py`
- `setup_tracing(service_name)` — TracerProvider with OTLP exporter
- `setup_metrics(service_name)` — MeterProvider
- `instrument_worker_job(job_type, attempt)` — context manager wrapping job spans with `job.type`, `job.id`, `job.attempt` attributes

#### [MODIFY] `shared/worker.py`
- Wrap `_process_message` with an OTel span for full job tracing

---

### All Services

#### [MODIFY] `pyproject.toml` (each)
- Add: `opentelemetry-distro`, `opentelemetry-exporter-otlp`, `prometheus-fastapi-instrumentator`

#### [MODIFY] `Dockerfile` (each FastAPI service)
- Wrap CMD with `opentelemetry-instrument`

#### [MODIFY] `main.py` (each FastAPI service)
- Add 3-line `Instrumentator().instrument(app).expose(app)` for Prometheus `/metrics`

#### [MODIFY] `docker-compose.yaml`
- Add `OTEL_SERVICE_NAME`, `OTEL_EXPORTER_OTLP_ENDPOINT`, `OTEL_PYTHON_LOG_CORRELATION` env vars to all services

---

### k6 (`infrastructure/k6/smoke-test.js`)

- 10 VUs, 30s, hits `/health`, `/accounts`, `POST /social/posts`
- Thresholds: p95 < 500ms, error rate < 1%

## Verification Plan

### Automated
1. `docker-compose up --build` — all containers healthy
2. `docker-compose run k6 run /scripts/smoke-test.js`

### Manual
1. `http://grafana.localhost` → all 3 datasources green
2. Fire a publish job → trace visible in Tempo
3. Click log line with `traceId` → Tempo sidebar loads

> [!IMPORTANT]
> **Facebook Access Token Approach**: I will mock the retrieval of the Facebook Page Access Token inside the worker (assuming it comes from the DB in a real scenario), but the actual POST request to the Graph API will be fully implemented using `httpx`. The environment variables `FACEBOOK_PAGE_ACCESS_TOKEN` and `GRAPH_API_BASE_URL` will be required. Please confirm this is the desired approach.

> [!WARNING]
> **API Client Setup**: I will install `axios` on the frontend. The API client will be configured to hit the Gateway directly (`http://gateway.localhost`). The Gateway already handles CORS for `app.localhost`, so this cross-origin request will succeed.

## Proposed Changes

### 1. Frontend: React API & Hooks (`apps/client`)
- **API Setup**: Install `axios`. Create `src/api/client.ts` as the central HTTP client pointing to the Gateway.
- **Typings**: Add strict TypeScript models in `src/api/types.ts` for Jobs, Post requests, and Facebook responses.
- **Hooks (`src/hooks`)**:
  - `useJobPolling.ts`: A hook to ping `GET /jobs/{job_id}` at intervals until the status changes from `pending` or `processing` to `completed` or `failed`.
  - `usePublishPost.ts`: A custom hook wrapping the async publishing action and polling logic, exposing a clean state (`isPending`, `isPolling`, `isSuccess`, `error`) to the UI.
- **UI Integration (`src/pages/posts/Create.tsx`)**:
  - Refactor the "Publish Content" button to trigger the new API hooks.
  - While polling, show a processing state (e.g., a "Publishing..." overlay or spinner).
  - On failure (especially DLQ routing), display the error to the user and present a "Retry" button that hits the Gateway's DLQ replay endpoint.

### 2. Backend: API & Gateway (`gateway/app` & `social-post-service`)
- **Gateway Endpoints**:
  - Expose a `POST /social/posts` route that forwards the payload to `social-post-service`. Alternatively, update `social-post-service` to accept the proper JSON schema directly.
- **Service API (`social-post-service/main.py`)**:
  - Update `POST /posts` to accept `{ "page_id": "...", "message": "...", "platforms": [...] }`.
  - Enqueue the job with type `publish_to_facebook`.

### 3. Backend: Worker Implementation (`social-post-service/worker.py`)
- **Facebook Graph Integration**:
  - Implement the `publish_to_facebook` handler.
  - **Step 1**: Validate payload.
  - **Step 2**: Retrieve access token (mocked for now, falling back to ENV).
  - **Step 3**: Use `httpx` to send a POST request to `https://graph.facebook.com/v19.0/{page_id}/feed`.
  - **Step 4**: Parse the Facebook Post ID and update the `job_state:{job_id}` in Redis as `completed` with the ID.
- **Resilience**:
  - The worker naturally leverages the existing exponential backoff and DLQ if the Facebook API goes down or hits rate limits.
  - Map specific Facebook errors (e.g., invalid token) to `NonRetryableError` so they go straight to the DLQ.

## Verification Plan

### Automated/Local Tests
- Run `npm install axios` in `apps/client`.
- Boot the system with `docker-compose up --build`.

### Manual Verification
- **Happy Path**: Use the Frontend Compose UI to type a message and select Facebook. Submit the form, verify the UI goes into a polling state, and eventually resolves.
- **Resilience Path**: Disconnect internet or provide an invalid dummy `FACEBOOK_PAGE_ACCESS_TOKEN` to trigger Facebook Graph API errors. Observe the UI reflecting the retry attempts, eventually showing the DLQ failure state, and exposing the Retry button.
