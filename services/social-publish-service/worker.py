import logging
import sys
from redis import Redis
from shared.worker import Worker
from shared.utils import IdempotencyMiddleware, FailureSimulator

logging.basicConfig(level=logging.INFO, stream=sys.stdout)
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
idempotency = IdempotencyMiddleware(redis_client)

def handle_publish_post(payload: dict):
    post_id = payload.get("post_id")
    idem_key = payload.get("idempotency_key")
    
    if not idempotency.check_and_set(idem_key):
        logger.info(f"Skipping duplicate publish: {idem_key}")
        return

    logger.info(f"Attempting to publish post {post_id}")
    
    # 50% chance of failure (mix of retryable and non-retryable)
    FailureSimulator.simulate_failure(chance=0.5)

    logger.info(f"Successfully published post {post_id}")

if __name__ == "__main__":
    worker = Worker(
        redis_client=redis_client,
        stream_name="jobs:social-publish",
        consumer_name="social-publish-worker-1"
    )
    worker.register_handler("publish_post", handle_publish_post)
    worker.run()
