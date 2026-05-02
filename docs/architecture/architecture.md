# Architecture: Posexei Async Job System

## System Overview
The Posexei Async Job System is an event-driven, distributed microservices architecture built on FastAPI. It is designed to handle asynchronous background processing with high reliability, ensuring that jobs are processed exactly once under normal conditions and safely retried or dead-lettered during failures.

The system decouples the HTTP ingress (API Gateway) from the heavy lifting via Redis Streams. Each domain service operates independently, maintaining its own isolated PostgreSQL database and communicating exclusively through structured events.

## Core Components
*   **API Gateway (FastAPI):** Exposes synchronous REST endpoints. Validates incoming requests, generates a unique Job ID, writes the job payload to the appropriate Redis Stream, and immediately returns a `202 Accepted` to the client.
*   **Worker Services (FastAPI):** Stateless processor nodes running in consumer groups. They poll Redis Streams, execute the business logic, and manage partial state updates in PostgreSQL.
*   **Redis Streams:** The central nervous system. Acts as the message broker, providing persistent, append-only logs for job events. 
*   **PostgreSQL:** The persistent store for job state and domain data. Strictly isolated per service; cross-database joins are prohibited.

## Service-to-Service Communication
We utilize an event-driven choreography pattern via Redis Streams. Services do not make synchronous HTTP calls to one another to process jobs.

1.  **Publishing (`XADD`):** When Service A completes a task that Service B needs to act on, it emits an event to Service B's inbound stream (e.g., `jobs:service_b:inbound`).
2.  **Consuming (`XREADGROUP`):** Service B instances operate as a consumer group. Redis ensures that each event in the stream is delivered to exactly one worker in the group.
3.  **Acknowledgment (`XACK`):** Only after the worker has successfully committed the database transaction is the event acknowledged in Redis.

## Idempotency Design
Because network failures can cause Redis to redeliver events (or a consumer might crash before issuing `XACK`), all job processing must be idempotent.

Before processing an event, the worker executes a `SET NX` command in Redis using a compound key: `idempotency:{service}:{event_type}:{job_id}`.
*   If the key is set successfully, the worker proceeds. The key is given a TTL of 24 hours.
*   If the key already exists, the event is treated as a duplicate. The worker immediately acknowledges the message via `XACK` and drops it.

## Retry and Dead Letter Queue (DLQ) Flow
Workers are designed to gracefully handle transient failures (e.g., database connection drops, temporary downstream API outages) and permanent failures (e.g., malformed payloads).

**Exponential Backoff Retry Strategy:**
We allow a maximum of 5 retries. If a worker encounters an exception, it does *not* acknowledge the message. Instead, it catches the exception, calculates the next retry time, and updates the retry count in the job's state (stored in PostgreSQL).

The worker sleeps or delays the processing using an exponential backoff curve:
*   Attempt 1: +1s
*   Attempt 2: +5s
*   Attempt 3: +25s
*   Attempt 4: +125s
*   Attempt 5: +625s

**Dead Letter Routing:**
If a job fails on its 5th retry, or if it encounters a known non-retryable error (e.g., validation failure), it is routed to the DLQ.
1. The worker publishes the original payload, along with the stack trace and metadata, to `jobs:{service}:dlq` via `XADD`.
2. The worker issues `XACK` on the original inbound stream to remove the poison pill.
3. DLQ streams are monitored and can be replayed manually after the underlying bug is fixed.
