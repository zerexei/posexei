from fastapi import FastAPI
import logging
import uuid
from shared.queue import RedisQueue
from redis import Redis

app = FastAPI(title="Social Post Service")
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
queue = RedisQueue(redis_client, stream_name="jobs:social-post")

@app.post("/posts")
def create_post(content: str):
    # Enqueue a multi-step job
    idem_key = str(uuid.uuid4())
    job_id = queue.enqueue({
        "type": "create_post",
        "content": content,
        "idempotency_key": idem_key,
        "job_id": idem_key
    })
    return {"status": "enqueued", "job_id": job_id}

@app.get("/health")
def health():
    return {"status": "ok"}
