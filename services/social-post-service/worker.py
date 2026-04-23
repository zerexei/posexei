import logging
import sys
import time
from redis import Redis
from shared.worker import Worker
from shared.utils import StateManager, IdempotencyMiddleware, FailureSimulator

logging.basicConfig(level=logging.INFO, stream=sys.stdout)
logger = logging.getLogger(__name__)

redis_client = Redis(host="redis", port=6379, db=0)
state_manager = StateManager(redis_client)
idempotency = IdempotencyMiddleware(redis_client)

def handle_create_post(payload: dict):
    job_id = payload.get("job_id")
    idem_key = payload.get("idempotency_key")
    
    # Check idempotency first
    if not idempotency.check_and_set(idem_key):
        logger.info(f"Skipping duplicate post creation: {idem_key}")
        return

    last_step = state_manager.get_last_step(job_id)
    
    steps = ["step_1_validate", "step_2_save_db", "step_3_notify"]
    start_index = 0
    if last_step in steps:
        start_index = steps.index(last_step) + 1
        
    for i in range(start_index, len(steps)):
        step = steps[i]
        logger.info(f"Executing {step} for job {job_id}")
        
        # Simulate failure randomly
        FailureSimulator.simulate_failure(chance=0.2)
            
        # If success, save step
        state_manager.save_step(job_id, step)
        
    logger.info(f"Successfully completed all steps for job {job_id}")

if __name__ == "__main__":
    worker = Worker(
        redis_client=redis_client,
        stream_name="jobs:social-post",
        consumer_name="social-post-worker-1"
    )
    worker.register_handler("create_post", handle_create_post)
    worker.run()
