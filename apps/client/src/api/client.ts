import axios from 'axios';
import type { PublishPostRequest, EnqueueResponse, JobStatusResponse, DlqResponse } from './types';

// Use gateway.localhost for local development, as Traefik routes it to the gateway service
export const apiClient = axios.create({
  baseURL: 'http://gateway.localhost',
  headers: {
    'Content-Type': 'application/json',
  },
});

export const socialApi = {
  publishPost: async (payload: PublishPostRequest): Promise<EnqueueResponse> => {
    const { data } = await apiClient.post<EnqueueResponse>('/social/posts', payload);
    return data;
  },
  
  getJobStatus: async (jobId: string): Promise<JobStatusResponse> => {
    const { data } = await apiClient.get<JobStatusResponse>(`/jobs/${jobId}`);
    return data;
  },

  getDlqJobs: async (serviceName: string = 'social-post'): Promise<DlqResponse> => {
    const { data } = await apiClient.get<DlqResponse>(`/dlq/${serviceName}`);
    return data;
  },

  replayDlqJob: async (serviceName: string, messageId: string) => {
    const { data } = await apiClient.post(`/dlq/${serviceName}/${messageId}/replay`);
    return data;
  }
};
