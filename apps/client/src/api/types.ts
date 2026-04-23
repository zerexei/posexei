export interface PublishPostRequest {
  page_id: string;
  message: string;
  media_url?: string;
  platforms: string[];
}

export interface EnqueueResponse {
  status: string;
  job_id: string;
}

export interface JobStatusResponse {
  job_id: string;
  status: 'pending' | 'processing' | 'completed' | 'failed' | 'unknown_or_completed';
  last_step?: string;
}

export interface DlqJob {
  message_id: string;
  payload: string;
  error: string;
  retry_count: string;
}

export interface DlqResponse {
  dlq_jobs: DlqJob[];
}
