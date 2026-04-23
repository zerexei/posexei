# Async Job System and Resilience Layer

This walkthrough summarizes the refactoring and extension of the system to implement the asynchronous job system and resilience layer.

## Architecture Highlights

> [!IMPORTANT]
> A unified `shared` module was created containing robust queue abstractions, ensuring consistency and DRT (Don't Repeat Yourself) principles across all microservices.

### 1. Redis Streams & Worker Abstraction
Instead of complex message brokers, we used **Redis Streams** as the backbone for the job queue.
- `services/shared/shared/queue.py`: Wraps `XADD`, `XREADGROUP`, and `XACK` to support reliable consumer groups.
- `services/shared/shared/worker.py`: A robust polling loop that dynamically routes payloads to registered handlers.

### 2. Resilience and Failure Handling
- **Exponential Backoff**: Transient failures are automatically rescheduled using exponential delays (`base_backoff * multiplier^(attempt-1)`).
- **Dead Letter Queue (DLQ)**: Jobs that fail 5 times or throw `NonRetryableError` are permanently routed to `jobs:{service}:dlq` for manual intervention.
- **Idempotency Layer**: Implemented via Redis `SET NX` (`IdempotencyMiddleware`), ensuring duplicate tasks (like double-posting) are skipped.
- **Partial Failure Resumption**: Tracked multi-step pipelines utilizing `StateManager`, ensuring retries resume exactly where they failed.
- **Failure Simulation & Rate Limiting**: Added `FailureSimulator` to inject 500s/latency, and `RateLimiter` utilizing a Token Bucket strategy.

### 3. Monorepo Integration
- **Docker Build Contexts**: Refactored `docker-compose.yaml` to build from the repository root `..`, allowing all backend microservices to seamlessly copy the local `services/shared` library without needing a PyPI registry.
- **Dedicated Worker Containers**: Added `*-worker` services in Docker Compose. These lightweight containers reuse their respective API service images but override the entrypoint to run the async worker loops.

### 4. Service Applications
- **Identity Service**: `create_user` jobs run asynchronously, shielded by idempotency checks.
- **Social Account Service**: `account_link` jobs handle artificial API rate limits smoothly.
- **Social Post Service**: `create_post` jobs execute a multi-step pipeline (Validate → DB Save → Notify), retaining state across arbitrary failure injections.
- **Social Publish Service**: `publish_post` jobs navigate 50% randomized failures, dynamically sorting between retries and the DLQ.

### 5. API Gateway Support
The gateway now provides critical administrative endpoints:
- `GET /jobs/{job_id}`: Checks real-time job status.
- `GET /dlq/{service_name}`: Inspects stranded jobs.
- `POST /dlq/{service_name}/{message_id}/replay`: Safely re-injects repaired jobs into the pipeline.

## Verification

To spin up the updated ecosystem locally:
```bash
cd infrastructure
docker-compose up --build
```
You can interact with the system via `gateway.localhost` (e.g. `POST http://gateway.localhost/users`).
