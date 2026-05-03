from fastapi import FastAPI
import uuid
import structlog
from shared.queue import RedisQueue
from shared.telemetry import setup_logging, setup_telemetry
from redis import Redis
from prometheus_fastapi_instrumentator import Instrumentator

setup_logging("identity-service")
setup_telemetry("identity-service")
logger = structlog.get_logger(__name__)

app = FastAPI()
Instrumentator().instrument(app).expose(app)

redis_client = Redis(host="redis", port=6379, db=0)
queue = RedisQueue(redis_client, stream_name="jobs:identity")


@app.get("/users")
def get_users():
    return [{"id": 1, "name": "Alice"}]


@app.get("/users/{user_id}")
def get_user(user_id: int):
    return {"id": user_id, "name": "Some User"}


@app.post("/users")
def create_user(user: dict):
    idem_key = str(uuid.uuid4())
    job_id = queue.enqueue({
        "type": "create_user",
        "user": user,
        "idempotency_key": idem_key
    })
    return {"status": "enqueued", "job_id": job_id}
