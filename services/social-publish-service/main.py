from fastapi import FastAPI
from pydantic import BaseModel
from typing import Optional
import logging
import uuid
from shared.queue import RedisQueue
from redis import Redis
from prometheus_fastapi_instrumentator import Instrumentator

app = FastAPI(title="Social Publish Service")
Instrumentator().instrument(app).expose(app)
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
queue = RedisQueue(redis_client, stream_name="jobs:social-publish")


class PublishRequest(BaseModel):
    page_id: str
    provider: str           # "facebook" | "instagram" | etc
    message: str
    media_url: Optional[str] = None
    post_db_id: Optional[str] = None  # reference back to social-post-service record


@app.post("/publish")
def publish_post(req: PublishRequest):
    idem_key = str(uuid.uuid4())
    job_id = queue.enqueue({
        "type": "publish_post",
        "page_id": req.page_id,
        "provider": req.provider,
        "message": req.message,
        "media_url": req.media_url,
        "post_db_id": req.post_db_id,
        "idempotency_key": idem_key,
        "job_id": idem_key,
    })
    redis_client.set(f"job_state:{job_id}", "pending", ex=86400)
    return {"status": "enqueued", "job_id": job_id}


@app.get("/health")
def health():
    return {"status": "ok"}
