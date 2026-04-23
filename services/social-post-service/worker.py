import logging
import sys
import os
import httpx
from redis import Redis
from shared.worker import Worker
from shared.utils import StateManager, IdempotencyMiddleware, NonRetryableError

logging.basicConfig(level=logging.INFO, stream=sys.stdout)
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
state_manager = StateManager(redis_client)
idempotency = IdempotencyMiddleware(redis_client)

def handle_publish_to_facebook(payload: dict):
    job_id = payload.get("job_id")
    idem_key = payload.get("idempotency_key")
    page_id = payload.get("page_id")
    message = payload.get("message")
    
    # Check idempotency first
    if not idempotency.check_and_set(idem_key):
        logger.info(f"Skipping duplicate post creation: {idem_key}")
        return

    # Update state to processing
    redis_client.set(f"job_state:{job_id}", "processing", ex=86400)

    # Step 1: Validate
    if not page_id or not message:
        redis_client.set(f"job_state:{job_id}", "failed", ex=86400)
        raise NonRetryableError("Invalid payload: page_id and message are required")

    # Step 2: Retrieve Access Token (Mocked from ENV)
    token = os.getenv("FACEBOOK_PAGE_ACCESS_TOKEN", "mocked_token")
    if not token:
        redis_client.set(f"job_state:{job_id}", "failed", ex=86400)
        raise NonRetryableError("Missing Facebook Page Access Token")

    # Step 3: Call Facebook Graph API
    base_url = os.getenv("GRAPH_API_BASE_URL", "https://graph.facebook.com/v19.0")
    url = f"{base_url}/{page_id}/feed"
    
    logger.info(f"Publishing to Facebook Page {page_id}")
    
    try:
        # Mocking for local dev if token is "mocked_token"
        if token == "mocked_token":
            logger.info("Using mocked token, simulating successful Facebook response")
            # Simulate network latency
            import time
            time.sleep(1)
            response_data = {"id": f"{page_id}_{job_id[:8]}"}
        else:
            response = httpx.post(url, data={"message": message, "access_token": token}, timeout=10.0)
            response.raise_for_status()
            response_data = response.json()
            
    except httpx.HTTPStatusError as e:
        status = e.response.status_code
        if status in (400, 401, 403, 404):
            redis_client.set(f"job_state:{job_id}", "failed", ex=86400)
            raise NonRetryableError(f"Facebook API Error (Non-Retryable): {e.response.text}")
        else:
            # 5xx errors are retryable
            raise Exception(f"Facebook API Error (Retryable): {e.response.text}")
    except httpx.RequestError as e:
        raise Exception(f"Network error when calling Facebook: {e}")

    # Step 4: Store result
    fb_post_id = response_data.get("id")
    logger.info(f"Successfully published. Facebook Post ID: {fb_post_id}")
    
    # Store success in state tracker
    redis_client.set(f"job_state:{job_id}", "completed", ex=86400)

if __name__ == "__main__":
    worker = Worker(
        redis_client=redis_client,
        stream_name="jobs:social-post",
        consumer_name="social-post-worker-1"
    )
    worker.register_handler("publish_to_facebook", handle_publish_to_facebook)
    worker.run()
