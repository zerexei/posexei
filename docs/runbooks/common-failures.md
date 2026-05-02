# Runbook: Common System Failures

## Scenario 1: Stuck Jobs (Growing Pending Entries List)
**Symptom:** The metric `redis_stream_pel_size` is constantly increasing, but the DLQ size is zero and CPU utilization on workers is low.
**Cause:** Workers are pulling messages from the stream but crashing abruptly (e.g., OOMKilled) before they can issue an `XACK` or route the message to the DLQ.
**Resolution:**
1. Check the Docker/Kubernetes logs for `OOMKilled` events on the worker pods.
2. Inspect the specific messages stuck in the PEL using Redis CLI: `XPENDING jobs:{service}:inbound workers`.
3. Use `XCLAIM` to manually reassign a stuck message to a healthy consumer if a worker node died permanently.
4. Increase memory limits on the worker container if a specific payload is causing an OOM.

## Scenario 2: Severe DLQ Growth
**Symptom:** `dlq_messages_total` spikes dramatically.
**Cause:** A downstream dependency is permanently down, a recent deployment introduced a parsing bug, or the database schema is out of sync. Jobs are exhausting their 5 retries (up to 125s backoff) and being dead-lettered.
**Resolution:**
1. Read the error reason from the DLQ payload: `XREAD COUNT 1 STREAMS jobs:{service}:dlq 0-0`. Look for the `error_trace` field.
2. If it's a code bug, deploy a hotfix.
3. Once the system is stable, execute the replay script: `python scripts/replay_dlq.py --service {service}`. This script reads from the DLQ and `XADD`s the payloads back to the inbound stream.

## Scenario 3: Duplicate Job Processing Detected
**Symptom:** Business metrics show a user was charged twice, or a record was created multiple times.
**Cause:** The Redis idempotency key TTL expired (took longer than 24h to process), or the key namespace collided. Alternatively, a network partition caused the database to commit but the `XACK` failed, leading to redelivery without idempotency protection.
**Resolution:**
1. Verify the exact `job_id` and check the `job_state` table in PostgreSQL.
2. Inspect the Redis logs for evictions. If Redis is under memory pressure, it might be evicting idempotency keys prematurely.
3. Verify the `maxmemory-policy` in Redis is set to `volatile-lru` or `allkeys-lru` and ensure the server has adequate RAM.

## Scenario 4: Redis Rate Limit Bucket Exhaustion
**Symptom:** Workers are logging continuous `RateLimitExceeded` warnings and processing latency is artificially high.
**Cause:** The simulated token bucket for a specific service is misconfigured, or an upstream service is dumping an unexpected burst of traffic into the stream.
**Resolution:**
1. Manually increase the token bucket capacity or refill rate in Redis temporarily to flush the backlog.
2. Investigate the upstream service to determine if the burst is legitimate traffic or a retry storm.
