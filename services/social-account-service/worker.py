import logging
import sys
from redis import Redis
from shared.worker import Worker
from shared.utils import RateLimiter, RateLimitExceeded, IdempotencyMiddleware

logging.basicConfig(level=logging.INFO, stream=sys.stdout)
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
rate_limiter = RateLimiter(redis_client, max_requests=100, window_seconds=60)
idempotency = IdempotencyMiddleware(redis_client)

def handle_account_link(payload: dict):
    user_id = payload.get("user_id")
    platform = payload.get("platform")
    idem_key = payload.get("idempotency_key")
    
    # Check idempotency
    if not idempotency.check_and_set(idem_key):
        logger.info(f"Skipping duplicate account link: {idem_key}")
        return

    # Check rate limit (simulate external API rate limit per platform)
    if not rate_limiter.is_allowed(f"api:{platform}"):
        logger.warning(f"Rate limit exceeded for platform {platform}")
        raise RateLimitExceeded(f"Rate limit exceeded for {platform}")

    logger.info(f"Successfully linked account {user_id} to {platform}")

if __name__ == "__main__":
    worker = Worker(
        redis_client=redis_client,
        stream_name="jobs:social-account",
        consumer_name="social-account-worker-1"
    )
    worker.register_handler("account_link", handle_account_link)
    worker.run()
