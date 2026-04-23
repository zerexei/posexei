from fastapi import FastAPI
import logging
import uuid
from shared.queue import RedisQueue
from redis import Redis

app = FastAPI(title="Social Publish Service")
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
queue = RedisQueue(redis_client, stream_name="jobs:social-publish")

@app.post("/publish")
def publish_post(post_id: str):
    idem_key = str(uuid.uuid4())
    job_id = queue.enqueue({
        "type": "publish_post",
        "post_id": post_id,
        "idempotency_key": idem_key
    })
    return {"status": "enqueued", "job_id": job_id}

@app.get("/health")
def health():
    return {"status": "ok"}
