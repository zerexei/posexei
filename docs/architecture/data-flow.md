# Job Lifecycle Data Flow

This document traces the exact sequence of operations for a standard asynchronous job moving through the Posexei system.

## 1. Ingestion
1. Client sends a `POST /jobs/process` request to the API Gateway.
2. Gateway validates the JSON payload.
3. Gateway generates a UUIDv4 `job_id`.
4. Gateway executes `XADD jobs:worker_a:inbound * job_id <uuid> payload <json>`.
5. Gateway responds `202 Accepted` with `{"job_id": "<uuid>", "status": "pending"}`.

## 2. Consumption
1. A FastAPI worker instance in `worker_a` executes a blocking `XREADGROUP GROUP workers consumer_1 STREAMS jobs:worker_a:inbound >`.
2. Redis delivers the new event to the worker. The event is now in the Pending Entries List (PEL).

## 3. Pre-flight Checks
1. **Idempotency:** Worker attempts `SET NX idempotency:worker_a:process:<job_id> "locked" EX 86400`.
    * If false: The worker issues `XACK` and aborts (duplicate).
2. **Rate Limiting:** Worker checks the token bucket in Redis. If depleted, the worker sleeps for a short duration and retries.

## 4. Processing & State Management (Partial Failure Handling)
To handle partial failures, we update state at granular steps.
1. Worker opens a PostgreSQL transaction.
2. Worker inserts a record into the `job_state` table: `status = 'processing'`.
3. Worker executes the core domain logic (e.g., image resizing, external API call).
    * *Failure Scenario:* If the external API fails, the transaction is rolled back. The worker sleeps based on the retry curve (1s, 5s, 25s, etc.) and raises an exception so the Redis event remains pending.
4. Worker updates `job_state` to `status = 'completed'`.
5. Worker commits the PostgreSQL transaction.

## 5. Acknowledgment & Handoff
1. If the transaction commits successfully, the worker executes `XACK jobs:worker_a:inbound workers <message_id>`. This removes the event from the PEL.
2. (Optional) If downstream processing is required, the worker executes `XADD jobs:worker_b:inbound * ...` before acknowledging the inbound message.
