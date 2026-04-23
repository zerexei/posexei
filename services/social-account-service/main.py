from fastapi import FastAPI
import logging
from shared.queue import RedisQueue
from redis import Redis

app = FastAPI(title="Social Account Service")
logger = logging.getLogger(__name__)

# Note: In production, configure Redis properly via env vars
redis_client = Redis(host="redis", port=6379, db=0)
queue = RedisQueue(redis_client, stream_name="jobs:social-account")

@app.post("/accounts/link")
def link_account(user_id: str, platform: str):
    job_id = queue.enqueue({
        "type": "account_link",
        "user_id": user_id,
        "platform": platform,
        "idempotency_key": f"link:{user_id}:{platform}"
    })
    return {"status": "enqueued", "job_id": job_id}

@app.get("/health")
def health():
    return {"status": "ok"}
