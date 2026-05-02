# Idempotency Design

Explains the standard idempotency implementation using Redis `SET NX` combined with TTLs.
Outlines how services prevent duplicate processing of the same event payload during network retries or consumer group rebalancing.
