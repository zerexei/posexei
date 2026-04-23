import { useState, useEffect, useCallback } from 'react';
import { socialApi } from '../api/client';
import type { JobStatusResponse } from '../api/types';

export const useJobPolling = (jobId: string | null) => {
  const [jobStatus, setJobStatus] = useState<JobStatusResponse | null>(null);
  const [isPolling, setIsPolling] = useState(false);

  const startPolling = useCallback(async () => {
    if (!jobId) return;
    
    setIsPolling(true);
    let isComplete = false;
    let attempts = 0;
    
    while (!isComplete && attempts < 60) { // Poll for up to 60 seconds
      try {
        const status = await socialApi.getJobStatus(jobId);
        setJobStatus(status);
        
        if (status.status === 'completed' || status.status === 'failed' || status.status === 'unknown_or_completed') {
          isComplete = true;
          setIsPolling(false);
          break;
        }
      } catch (e) {
        console.error('Failed to poll job status', e);
      }
      
      await new Promise(resolve => setTimeout(resolve, 1000));
      attempts++;
    }
    
    setIsPolling(false);
  }, [jobId]);

  useEffect(() => {
    if (jobId) {
      startPolling();
    }
  }, [jobId, startPolling]);

  return { jobStatus, isPolling, setJobStatus };
};
