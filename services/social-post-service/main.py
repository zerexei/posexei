from fastapi import FastAPI
from pydantic import BaseModel
from typing import Optional, List
import logging
import uuid
from shared.queue import RedisQueue
from redis import Redis

app = FastAPI(title="Social Post Service")
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
queue = RedisQueue(redis_client, stream_name="jobs:social-post")


class PostRequest(BaseModel):
    page_id: str
    provider: str = "facebook"
    message: str
    media_url: Optional[str] = None
    platforms: Optional[List[str]] = None


@app.post("/posts")
def create_post(request: PostRequest):
    idem_key = str(uuid.uuid4())
    job_id = queue.enqueue({
        "type": "publish_to_facebook",
        "page_id": request.page_id,
        "provider": request.provider,
        "message": request.message,
        "media_url": request.media_url,
        "idempotency_key": idem_key,
        "job_id": idem_key,
    })

    # Initialise state so the frontend's very first poll returns "pending" not "unknown"
    redis_client.set(f"job_state:{job_id}", "pending", ex=86400)

    return {"status": "enqueued", "job_id": job_id}


@app.get("/health")
def health():
    return {"status": "ok"}
