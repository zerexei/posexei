import structlog
from redis import Redis
from shared.worker import Worker
from shared.utils import IdempotencyMiddleware
from shared.telemetry import setup_telemetry, setup_logging

setup_logging("identity-worker")
logger = structlog.get_logger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
idempotency = IdempotencyMiddleware(redis_client)

def handle_create_user(payload: dict):
    user = payload.get("user")
    idem_key = payload.get("idempotency_key")
    
    if not idempotency.check_and_set(idem_key):
        logger.info(f"Skipping duplicate user creation: {idem_key}")
        return

    logger.info(f"Creating user {user}")
    # Simulating DB write
    logger.info("User created successfully in DB")

if __name__ == "__main__":
    setup_telemetry("identity-worker")
    worker = Worker(
        redis_client=redis_client,
        stream_name="jobs:identity",
        consumer_name="identity-worker-1"
    )
    worker.register_handler("create_user", handle_create_user)
    worker.run()
