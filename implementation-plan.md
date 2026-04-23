# Async Job System and Resilience Layer Implementation

This document outlines the architecture and implementation steps for introducing an asynchronous job queue, failure handling, and retry mechanisms into the existing FastAPI microservices architecture.

## User Review Required

> [!IMPORTANT]  
> **Shared Library Distribution**: To avoid code duplication for the Redis queue, worker abstraction, and idempotency logic, I propose creating a `services/shared` module. To make this work seamlessly with `uv` and Docker, I will update the `docker-compose.yaml` build context for the backend services to the repository root (`..`) and adjust their `Dockerfiles`. This is a standard monorepo pattern but requires modifying existing Dockerfiles. Please confirm if this is acceptable.

> [!WARNING]
> **Worker Deployment**: I will add a new worker container for each service in `docker-compose.yaml`. These workers will reuse their respective service's Docker image but override the command to run the worker process (e.g., `uv run python worker.py`). This keeps the infrastructure footprint minimal while separating API and background job concerns.

## Proposed Changes

### 1. Shared Infrastructure (`services/shared`)
Create a shared Python library that all services will depend on. It will include:
- **Redis Queue Abstraction**: Use Redis Streams (`XADD`, `XREADGROUP`, `XREAD`) to implement a robust job queue with Consumer Groups. This naturally supports message acknowledgment and prevents duplicate processing across multiple workers.
- **Worker Base Class**: A reusable worker abstraction that handles polling, routing jobs to handler functions, executing retries with exponential backoff (1s → 5s → 25s → 2min), and moving failed jobs to the Dead Letter Queue (DLQ).
- **Idempotency Middleware**: A utility to check and store `idempotency_key`s in PostgreSQL or Redis to prevent duplicate processing.
- **Resilience Utilities**:
  - **Rate Limiter**: Token bucket implementation backed by Redis.
  - **Failure Simulator**: A decorator to randomly inject latency, 500 errors, or simulate partial successes in dev mode.
  - **Backpressure**: Logic in the worker to pause consumption or reject low-priority jobs if the queue or active job count exceeds a threshold.
  - **Partial Failure Resumption**: Support for storing job state steps (`step_1`, `step_2`) in Redis, allowing retries to resume from the last successful step.

### 2. Service Integration

We will implement specific use cases in each service to demonstrate the system:

#### [MODIFY] `services/identity-service`
- **Goal**: Async idempotent user provisioning.
- Add `worker.py` with a `create_user` job handler.
- Update API to enqueue a job instead of synchronously creating a user.
- Utilize the idempotency helper backed by the service's PostgreSQL database to prevent duplicate user creation.

#### [MODIFY] `services/social-account-service`
- **Goal**: Account linking jobs with rate limits.
- Add `worker.py` with an `account_link` job handler.
- Integrate the shared Rate Limiting utility. If limits are exceeded, the job is requeued with a delay.

#### [MODIFY] `services/social-post-service`
- **Goal**: Multi-step post creation pipeline.
- Add `worker.py` handling `create_post`.
- Demonstrate partial failure handling by saving progress steps to Redis. If the pipeline fails at step 2, the retry will skip step 1.

#### [MODIFY] `services/social-publish-service`
- **Goal**: Publish jobs with external failure simulation and DLQ routing.
- Add `worker.py` handling `publish_post`.
- Use the Failure Simulator to randomly trigger retryable (e.g., timeouts) and non-retryable (e.g., validation) errors.
- Non-retryable errors or exhausted retries will route jobs to the Redis DLQ.

### 3. Gateway Enhancements (`gateway/app`)
Update the gateway to expose operational endpoints for the async system:
- `GET /jobs/{job_id}`: Retrieve current status of a job.
- `GET /dlq`: Inspect jobs currently residing in the Dead Letter Queue.
- `POST /dlq/{job_id}/replay`: Safely requeue a job from the DLQ back to its original stream, utilizing idempotency checks to prevent dual processing.

### 4. Infrastructure (`infrastructure/docker-compose.yaml`)
- Add worker containers: `identity-worker`, `social-account-worker`, `social-post-worker`, `social-publish-worker`.
- Update build contexts to allow copying the `services/shared` directory into the service containers.

## Verification Plan

### Automated/Local Tests
- Run `docker-compose up --build` to ensure all services, workers, and gateway start correctly.
- Trigger jobs via the Gateway API and observe worker logs to confirm successful execution.
- Observe Redis Streams (via a tool or gateway endpoints) to ensure jobs are enqueued and acknowledged.

### Manual Verification
- **Retries**: Use the failure simulator in `social-publish-service` to force a failure and verify the worker retries 5 times with exponential backoff before sending to DLQ.
- **DLQ**: Inspect the DLQ endpoint on the Gateway to see failed jobs. Use the replay endpoint to requeue them and verify they process successfully (after disabling the failure simulator).
- **Idempotency**: Send the exact same `POST /users` request twice with the same idempotency key and verify the worker skips the second job.
- **Partial Failure**: Induce a failure in step 2 of `social-post-service`. Verify that upon retry, the worker resumes directly at step 2.
