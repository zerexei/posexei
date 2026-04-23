import json
import time
import socket
import logging
from typing import Callable, Dict, Optional
from redis import Redis
from .queue import RedisQueue

logger = logging.getLogger(__name__)

class Worker:
    def __init__(
        self,
        redis_client: Redis,
        stream_name: str,
        group_name: str = "workers",
        consumer_name: Optional[str] = None,
        max_retries: int = 5,
        base_backoff: float = 1.0, # 1s -> 5s -> 25s
        backoff_multiplier: float = 5.0
    ):
        self.redis = redis_client
        self.queue = RedisQueue(redis_client, stream_name, group_name)
        self.consumer_name = consumer_name or socket.gethostname()
        self.max_retries = max_retries
        self.base_backoff = base_backoff
        self.backoff_multiplier = backoff_multiplier
        self.handlers: Dict[str, Callable] = {}
        self.running = False

    def register_handler(self, job_type: str, handler: Callable):
        self.handlers[job_type] = handler

    def calculate_backoff(self, attempt: int) -> float:
        # e.g. 1s -> 5s -> 25s -> 125s (approx 2min)
        return self.base_backoff * (self.backoff_multiplier ** (attempt - 1))

    def _process_message(self, message_id: str, data: Dict[str, bytes]):
        payload_str = data.get(b"payload", b"{}").decode("utf-8")
        try:
            payload = json.loads(payload_str)
        except json.JSONDecodeError:
            logger.error(f"Failed to decode payload for {message_id}: {payload_str}")
            self.queue.ack_job(message_id)
            return

        job_type = payload.get("type")
        if not job_type or job_type not in self.handlers:
            logger.warning(f"No handler found for job type: {job_type}")
            # Consider this a non-retryable failure if no handler exists
            self.queue.dlq_job(payload_str, f"No handler for type {job_type}", 0)
            self.queue.ack_job(message_id)
            return

        attempt = payload.get("attempt_count", 0) + 1
        payload["attempt_count"] = attempt
        payload["last_attempt_timestamp"] = time.time()

        try:
            # Execute the handler
            logger.info(f"Processing job {job_type} (id: {message_id}, attempt: {attempt})")
            self.handlers[job_type](payload)
            logger.info(f"Job {job_type} processed successfully")
            self.queue.ack_job(message_id)
            
        except Exception as e:
            error_msg = str(e)
            logger.error(f"Job {job_type} failed: {error_msg}")
            
            # Failure Classification
            # If exception has an attribute `retryable` set to False, send to DLQ immediately
            is_retryable = getattr(e, "retryable", True)
            
            if not is_retryable or attempt >= self.max_retries:
                logger.warning(f"Sending job to DLQ. Retryable: {is_retryable}, Attempt: {attempt}")
                self.queue.dlq_job(payload_str, error_msg, attempt)
                self.queue.ack_job(message_id)
            else:
                # Schedule retry
                backoff = self.calculate_backoff(attempt)
                logger.info(f"Scheduling retry in {backoff} seconds...")
                # Note: Simple approach is sleeping in the worker (blocks this worker)
                # Production approach: Put in a separate delayed queue.
                # For this implementation, we sleep (which applies backpressure).
                time.sleep(backoff)
                
                # We do not ACK the job so it remains pending, but to avoid it being picked up 
                # immediately by same/another worker if we XACK and XADD, we can XADD with updated attempt
                # and XACK the old one.
                self.queue.ack_job(message_id)
                self.queue.enqueue(payload) # Re-enqueue updated payload


    def run(self):
        self.running = True
        logger.info(f"Worker {self.consumer_name} started listening on {self.queue.stream_name}")
        while self.running:
            try:
                # Read 1 message, block for 5 seconds
                messages = self.queue.read_jobs(self.consumer_name, count=1, block=5000)
                if not messages:
                    continue
                
                for stream, msgs in messages:
                    for message_id, data in msgs:
                        message_id_str = message_id.decode('utf-8')
                        self._process_message(message_id_str, data)

            except Exception as e:
                logger.error(f"Error in worker loop: {e}")
                time.sleep(5)
