from fastapi import FastAPI
from pydantic import BaseModel
import logging
import uuid
from typing import Optional, List
from shared.queue import RedisQueue
from redis import Redis

app = FastAPI(title="Social Post Service")
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
queue = RedisQueue(redis_client, stream_name="jobs:social-post")

class PostRequest(BaseModel):
    page_id: str
    message: str
    media_url: Optional[str] = None
    platforms: List[str]

@app.post("/posts")
def create_post(request: PostRequest):
    idem_key = str(uuid.uuid4())
    job_id = queue.enqueue({
        "type": "publish_to_facebook",
        "page_id": request.page_id,
        "message": request.message,
        "media_url": request.media_url,
        "idempotency_key": idem_key,
        "job_id": idem_key
    })
    
    # Initialize state tracker
    redis_client.set(f"job_state:{job_id}", "pending", ex=86400)
    
    return {"status": "enqueued", "job_id": job_id}

@app.get("/health")
def health():
    return {"status": "ok"}
