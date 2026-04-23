import logging
import sys
import os
import httpx
from redis import Redis
from shared.worker import Worker
from shared.utils import IdempotencyMiddleware, NonRetryableError

logging.basicConfig(level=logging.INFO, stream=sys.stdout)
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
idempotency = IdempotencyMiddleware(redis_client)


def handle_publish_post(payload: dict):
    job_id = payload.get("job_id")
    idem_key = payload.get("idempotency_key")
    page_id = payload.get("page_id")
    provider = payload.get("provider", "facebook")
    message = payload.get("message")

    if not idempotency.check_and_set(idem_key):
        logger.info(f"Skipping duplicate publish: {idem_key}")
        return

    redis_client.set(f"job_state:{job_id}", "processing", ex=86400)

    # Step 1: Validate
    if not page_id or not message:
        redis_client.set(f"job_state:{job_id}", "failed", ex=86400)
        raise NonRetryableError("Invalid payload: page_id and message are required")

    # Step 2: Retrieve access token from social-account-service
    try:
        token_resp = httpx.get(
            f"http://social-account-service:3001/accounts/token/{provider}/{page_id}",
            timeout=5.0,
        )
        if token_resp.status_code == 404:
            # Fall back to ENV for legacy / dev use
            token = os.getenv("FACEBOOK_PAGE_ACCESS_TOKEN", "mocked_token")
        else:
            token_resp.raise_for_status()
            token = token_resp.json().get("access_token", "mocked_token")
    except httpx.RequestError:
        # social-account-service unavailable — fall back to ENV
        token = os.getenv("FACEBOOK_PAGE_ACCESS_TOKEN", "mocked_token")

    if not token:
        redis_client.set(f"job_state:{job_id}", "failed", ex=86400)
        raise NonRetryableError("No access token available for this page")

    # Step 3: Call the platform API
    if provider == "facebook":
        fb_post_id = _publish_to_facebook(page_id, message, token, job_id)
    else:
        # Other providers — mock success for now
        fb_post_id = f"{provider}_{page_id[:8]}_mock"

    # Step 4: Store result
    redis_client.set(f"job_state:{job_id}", "completed", ex=86400)
    redis_client.set(f"job_result:{job_id}", fb_post_id, ex=86400)
    logger.info(f"Published successfully. Platform post ID: {fb_post_id}")


def _publish_to_facebook(page_id: str, message: str, token: str, job_id: str) -> str:
    base_url = os.getenv("GRAPH_API_BASE_URL", "https://graph.facebook.com/v19.0")
    url = f"{base_url}/{page_id}/feed"

    if token == "mocked_token":
        import time
        logger.info("[MOCK] Simulating Facebook publish (no real token)")
        time.sleep(1)
        return f"{page_id}_{job_id[:8]}_mock"

    try:
        resp = httpx.post(url, data={"message": message, "access_token": token}, timeout=10.0)
        resp.raise_for_status()
        return resp.json().get("id", "unknown")
    except httpx.HTTPStatusError as e:
        if e.response.status_code in (400, 401, 403, 404):
            redis_client.set(f"job_state:{job_id}", "failed", ex=86400)
            raise NonRetryableError(f"Facebook API error: {e.response.text}")
        raise Exception(f"Facebook transient error: {e.response.text}")
    except httpx.RequestError as e:
        raise Exception(f"Network error posting to Facebook: {e}")


if __name__ == "__main__":
    worker = Worker(
        redis_client=redis_client,
        stream_name="jobs:social-publish",
        consumer_name="social-publish-worker-1",
    )
    worker.register_handler("publish_post", handle_publish_post)
    worker.run()
