from fastapi import FastAPI, HTTPException
import json
from routes.v1.identity import identity_router
from redis import Redis

app = FastAPI()
redis_client = Redis(host="redis", port=6379, db=0)

app.include_router(identity_router)


@app.get("/")
def read_root():
    return {"Hello": "Gateway3"}


@app.get("/health")
def read_health():
    return {"status": "ok"}

@app.get("/jobs/{job_id}")
def get_job_status(job_id: str):
    # Retrieve job status (simplified)
    # In a full implementation, you'd check a dedicated status key
    # For now, we'll check if it's in a state tracker
    state = redis_client.get(f"job_state:{job_id}")
    if state:
        return {"job_id": job_id, "status": "processing", "last_step": state.decode("utf-8")}
    return {"job_id": job_id, "status": "unknown_or_completed"}

@app.get("/dlq/{service_name}")
def inspect_dlq(service_name: str, count: int = 10):
    stream_name = f"jobs:{service_name}:dlq"
    # XREAD from 0
    try:
        messages = redis_client.xrange(stream_name, count=count)
    except Exception as e:
        return {"error": str(e)}
        
    results = []
    for msg_id, data in messages:
        results.append({
            "message_id": msg_id.decode("utf-8"),
            "payload": data.get(b"payload", b"").decode("utf-8"),
            "error": data.get(b"error", b"").decode("utf-8"),
            "retry_count": data.get(b"retry_count", b"").decode("utf-8")
        })
    return {"dlq_jobs": results}

@app.post("/dlq/{service_name}/{message_id}/replay")
def replay_dlq_job(service_name: str, message_id: str):
    dlq_stream = f"jobs:{service_name}:dlq"
    target_stream = f"jobs:{service_name}"
    
    # Read the specific message
    messages = redis_client.xrange(dlq_stream, min=message_id, max=message_id, count=1)
    if not messages:
        raise HTTPException(status_code=404, detail="Job not found in DLQ")
        
    _, data = messages[0]
    payload_str = data.get(b"payload", b"").decode("utf-8")
    
    try:
        payload = json.loads(payload_str)
        # Reset attempt count to allow retries
        payload["attempt_count"] = 0
        
        # Enqueue back to main stream
        new_id = redis_client.xadd(target_stream, {"payload": json.dumps(payload), "status": "pending"})
        
        # Remove from DLQ
        redis_client.xdel(dlq_stream, message_id)
        
        return {"status": "requeued", "new_job_id": new_id.decode("utf-8") if isinstance(new_id, bytes) else new_id}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
