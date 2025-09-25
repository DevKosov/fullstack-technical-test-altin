// src/types/llm.ts
export type LlmSuccess = {
  summary: string
  sentiment: string
  keywords: string[]
  tokens?: number
  duration_ms?: number
}

export type LlmError = {
  status: 'error'
  error: string
  violations?: string[]
}

export type LlmResultPayload = LlmSuccess | LlmError

export interface LlmResultEnvelope {
  job_id: string
  prompt?: string
  result: LlmResultPayload
}

/** Type guards (handy in components) */
export const isError = (r: LlmResultPayload): r is LlmError => (r as LlmError).status === 'error'

export const isSuccess = (r: LlmResultPayload): r is LlmSuccess => !('status' in r)
