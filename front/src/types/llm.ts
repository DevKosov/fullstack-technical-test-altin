/** Successful LLM payload */
export type LlmSuccess = {
  summary: string
  sentiment: string
  keywords: string[]
  tokens?: number
  duration_ms?: number
}

/** Error payload (when moderation/LLM fails) */
export type LlmError = {
  status: 'error'
  error: string
  violations?: string[]
}

/** Union of success | error */
export type LlmResultPayload = LlmSuccess | LlmError

/** Envelope that the backend broadcasts */
export interface LlmResultEnvelope {
  job_id: string
  result: LlmResultPayload
}

/** Type guards (handy in components) */
export const isError = (r: LlmResultPayload): r is LlmError => (r as LlmError).status === 'error'

export const isSuccess = (r: LlmResultPayload): r is LlmSuccess => !('status' in r)
