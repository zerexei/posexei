import time
import random
import logging
from redis import Redis

logger = logging.getLogger(__name__)

class NonRetryableError(Exception):
    def __init__(self, message="Non-retryable error"):
        self.message = message
        self.retryable = False
        super().__init__(self.message)

class RateLimitExceeded(Exception):
    def __init__(self, message="Rate limit exceeded"):
        self.message = message
        self.retryable = True
        super().__init__(self.message)


class IdempotencyMiddleware:
    def __init__(self, redis_client: Redis, ttl_seconds: int = 86400):
        self.redis = redis_client
        self.ttl = ttl_seconds

    def check_and_set(self, key: str) -> bool:
        """
        Returns True if the key was successfully set (i.e. not processed yet).
        Returns False if the key already exists (i.e. already processed).
        """
        if not key:
            return True # Skip idempotency if no key provided
        
        redis_key = f"idempotency:{key}"
        # Set NX returns True if set, False if exists
        result = self.redis.set(redis_key, "1", nx=True, ex=self.ttl)
        return bool(result)

    def clear(self, key: str):
        """
        Clears the idempotency key so a failed job can be retried.
        """
        if key:
            self.redis.delete(f"idempotency:{key}")


class RateLimiter:
    def __init__(self, redis_client: Redis, max_requests: int, window_seconds: int):
        self.redis = redis_client
        self.max_requests = max_requests
        self.window_seconds = window_seconds

    def is_allowed(self, key: str) -> bool:
        redis_key = f"ratelimit:{key}"
        
        # Simple implementation using INCR and EXPIRE
        current = self.redis.incr(redis_key)
        if current == 1:
            self.redis.expire(redis_key, self.window_seconds)
            
        if current > self.max_requests:
            return False
        return True


class FailureSimulator:
    """Utility to inject failures in development"""
    
    @staticmethod
    def simulate_failure(chance: float = 0.3):
        """Randomly raise errors or sleep based on chance"""
        roll = random.random()
        if roll > chance:
            return
            
        # Determine type of failure
        failure_type = random.choice(["latency", "retryable", "non_retryable"])
        
        if failure_type == "latency":
            sleep_time = random.uniform(1.0, 5.0)
            logger.info(f"[SIMULATOR] Injecting latency: {sleep_time:.2f}s")
            time.sleep(sleep_time)
        elif failure_type == "retryable":
            logger.info("[SIMULATOR] Injecting retryable 500 error")
            raise Exception("Simulated 5xx internal server error")
        elif failure_type == "non_retryable":
            logger.info("[SIMULATOR] Injecting non-retryable validation error")
            raise NonRetryableError("Simulated Validation Error (400)")


class StateManager:
    """Manages state for partial failure handling"""
    def __init__(self, redis_client: Redis, ttl_seconds: int = 86400):
        self.redis = redis_client
        self.ttl = ttl_seconds
        
    def save_step(self, job_id: str, step_name: str):
        redis_key = f"job_state:{job_id}"
        self.redis.set(redis_key, step_name, ex=self.ttl)
        
    def get_last_step(self, job_id: str) -> str:
        redis_key = f"job_state:{job_id}"
        val = self.redis.get(redis_key)
        return val.decode("utf-8") if val else None
