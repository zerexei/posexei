import { useState } from 'react';
import { socialApi } from '../api/client';
import type { PublishPostRequest } from '../api/types';
import { useJobPolling } from './useJobPolling';

export const usePublishPost = () => {
  const [jobId, setJobId] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);
  
  const { jobStatus, isPolling, setJobStatus } = useJobPolling(jobId);

  const publish = async (payload: PublishPostRequest) => {
    setError(null);
    setJobId(null);
    setJobStatus(null);
    
    try {
      const response = await socialApi.publishPost(payload);
      setJobId(response.job_id);
    } catch (e: any) {
      setError(e.response?.data?.detail || e.message || 'Failed to enqueue post');
    }
  };

  const isPending = !!jobId && isPolling;
  const isSuccess = jobStatus?.status === 'completed' || jobStatus?.status === 'unknown_or_completed';
  const isFailed = jobStatus?.status === 'failed' || !!error;

  return {
    publish,
    jobId,
    jobStatus,
    isPending,
    isSuccess,
    isFailed,
    error
  };
};
