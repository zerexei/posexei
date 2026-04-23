# Posexei Async Job System

This project implements an asynchronous job processing system with a resilience layer across multiple FastAPI microservices using Redis Streams.

## Architecture Overview

- **Queueing Engine**: Redis Streams (`XADD`, `XREADGROUP`)
- **Web Framework**: FastAPI
- **Persistence**: PostgreSQL (per service)
- **Infrastructure**: Docker Compose

## Resilience Layer Features

### 1. Retry Strategy
Jobs that encounter transient failures (e.g., `TimeoutError`, `RateLimitExceeded`) are automatically retried.
- **Exponential Backoff**: Retries are scheduled with increasing delays: 1s → 5s → 25s → 125s (~2min).
- **Max Retries**: Each job is retried a maximum of 5 times.
- **State Preservation**: The current `attempt_count` is tracked in the job payload. 

### 2. Idempotency Model
To prevent duplicate operations (like charging a user twice or sending duplicate posts), all jobs must include an `idempotency_key`.
- Before executing a handler, the worker checks if the key exists in Redis via the `IdempotencyMiddleware` (`SET NX`).
- If the key exists, the job is skipped and acknowledged immediately.
- Keys are assigned a TTL (e.g., 24 hours) to prevent Redis memory bloat.

### 3. Dead Letter Queue (DLQ) Flow
When jobs fail permanently, they are routed to the DLQ (`jobs:{service_name}:dlq`).
Jobs are sent to the DLQ if:
- They exceed the maximum retry count (5 attempts).
- They raise a non-retryable error (e.g., `NonRetryableError` such as validation failures).
- No handler is found for the job type.

**DLQ Management:**
- **Inspection**: View DLQ jobs via `GET /dlq/{service_name}` on the Gateway.
- **Replay**: Safely requeue jobs from the DLQ back to the main stream via `POST /dlq/{service_name}/{job_id}/replay`.

### 4. Failure Simulation
To test resilience, the `FailureSimulator` utility can inject faults during development.
- **Latency**: Injects random `time.sleep()`.
- **Transient Errors**: Randomly raises standard `Exception` to trigger retries.
- **Permanent Errors**: Randomly raises `NonRetryableError` to trigger DLQ routing.
(See `social-publish-service` for an example of failure simulation).

### 5. Rate Limiting Simulation
Simulates external API rate limits using a Redis-backed Token Bucket algorithm. If limits are exceeded, jobs throw a retryable error and backoff naturally. (See `social-account-service`).

### 6. Partial Failure Handling
For multi-step pipelines, progress is saved incrementally using `StateManager`. If a job fails at step 2, the next retry will resume directly at step 2, skipping step 1. (See `social-post-service`).

## Running Locally

```bash
cd infrastructure
docker-compose up --build
```

### Accessing the Gateway
The gateway routes API calls to the background workers:
- `http://gateway.localhost/health`
- `http://gateway.localhost/users` (POST)
- `http://gateway.localhost/jobs/{job_id}` (GET)
- `http://gateway.localhost/dlq/{service_name}` (GET)
