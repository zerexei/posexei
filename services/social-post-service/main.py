from fastapi import FastAPI, Header, HTTPException
from pydantic import BaseModel
from typing import Optional, List
import structlog
import uuid
from shared.queue import RedisQueue
from shared.telemetry import setup_logging, setup_telemetry
from redis import Redis
from prometheus_fastapi_instrumentator import Instrumentator

setup_logging("social-post-service")
setup_telemetry("social-post-service")
logger = structlog.get_logger(__name__)

app = FastAPI(title="Social Post Service")
Instrumentator().instrument(app).expose(app)

redis_client = Redis(host="redis", port=6379, db=0)
queue = RedisQueue(redis_client, stream_name="jobs:social-post")


class PostRequest(BaseModel):
    page_id: str
    provider: str = "facebook"
    message: str
    media_url: Optional[str] = None
    platforms: Optional[List[str]] = None


@app.post("/posts")
def create_post(request: PostRequest, x_idempotency_key: Optional[str] = Header(None)):
    # Backpressure: Prevent queue overload
    try:
        q_len = redis_client.xlen("jobs:social-post")
        if q_len > 10000:
            raise HTTPException(status_code=429, detail="Queue overload, please try again later")
    except Exception as e:
        if isinstance(e, HTTPException):
            raise
        logger.warning(f"Failed to check queue length: {e}")

    idem_key = x_idempotency_key if x_idempotency_key else str(uuid.uuid4())
    job_id = queue.enqueue({
        "type": "create_post",
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
