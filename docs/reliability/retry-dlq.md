# Retry Strategy & Dead Letter Queue (DLQ)

Details the retry policy and Dead Letter Queue mechanism for resilient job processing.
Explains the exponential backoff strategy (1s → 5s → 25s → 125s) and how terminal failures are routed to `jobs:{service}:dlq` for manual intervention.
