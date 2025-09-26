import { ref } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { api } from '@/lib/api'

const result = ref<any | null>(null)
const loading = ref(false)
const analysisId = ref<string | null>(null)
let echo: Echo | null = null

export const connect = async () => {
  if (echo) return

  // Make sure CSRF cookie exists (so /broadcasting/auth can read the session)
  await api.get('/sanctum/csrf-cookie')

  // @ts-ignore
  window.Pusher = Pusher

  echo = new Echo({
    broadcaster: 'pusher',
    key: 'local',
    cluster: 'mt1', // pusher-js wants a cluster string
    wsHost: '127.0.0.1',
    wsPort: 8080,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws'],

    // Use our axios client so cookies + XSRF header are sent
    authorizer: (channel) => ({
      authorize: async (socketId, callback) => {
        try {
          const { data } = await api.post('/broadcasting/auth', {
            socket_id: socketId,
            channel_name: channel.name,
          })
          callback(false, data)
        } catch (err: any) {
          console.error('Channel auth failed', err?.response?.data || err)
          callback(true, err)
        }
      },
    }),
  })
}

export const send = async (prompt: string) => {
  if (!echo) await connect()
  loading.value = true
  result.value = null
  analysisId.value = null

  
  const { data } = await api.post('/api/prompt', { prompt })
  analysisId.value = data.analysis_id

  // 2) Subscribe to the correct channel: private-analysis.{id}
  const channelName = `analysis.${analysisId.value}`
  echo!.private(channelName).listen('.llm.result', (payload: any) => {
    result.value = payload
    loading.value = false
  })
}

export const useLLMChannel = () => ({ result, loading, analysisId, connect, send })
