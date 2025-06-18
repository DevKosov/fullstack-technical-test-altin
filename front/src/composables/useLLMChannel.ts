import { ref, type Ref } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { useXsrfToken } from '@/composables/useXsrfToken'
import axios from 'axios'
axios.defaults.withCredentials = true

const result: Ref = ref(null)

let echo: Echo | null = null
let socketId = ''
let subscribed = false
let channel: any = null

const connect = async () => {
  if (echo || subscribed) return

  const { token, refresh } = useXsrfToken()
  const baseUrl = 'http://localhost:8000/'
  await refresh()

  if (!token.value) {
    console.error('❌ No XSRF token found')
    return
  }

  // @ts-ignore
  window.Pusher = Pusher

  echo = new Echo({
    broadcaster: 'pusher',
    key: 'local',
    cluster: 'eu',
    wsHost: '127.0.0.1',
    wsPort: 8080,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws'],
    authEndpoint: `${baseUrl}broadcasting/auth`,
    authorizer: (channel) => ({
      authorize: (socketId, callback) => {
        axios
          .post(`${baseUrl}broadcasting/auth`, {
            socket_id: socketId,
            channel_name: channel.name,
          })
          .then((response) => {
            console.log('Got autoriser response')
            callback(false, response.data)
          })
          .catch((error) => {
            console.error('Error autoriser response')

            throw error
            // callback(true, error)
          })
      },
    }),
    csrfToken: token.value,
    withCredentials: true,
    auth: {
      headers: {
        'X-CSRF-TOKEN': token.value,
      },
    },
  })

  echo.connector.pusher.connection.bind('connected', () => {
    socketId = echo?.socketId() ?? ''
  })

  channel = echo.private('analysis')

  channel.listen('.llm.result', (data) => {
    result.value = data
  })

  subscribed = true
}

const send = async (prompt: string) => {
  const payload = {
    id: 'frontend-' + Date.now(),
    prompt,
  }
  const { token } = useXsrfToken()

  try {
    await axios.post('http://localhost:8000/api/prompt', payload, {
      withCredentials: true,
      headers: {},
    })
  } catch (e) {
    console.error('❌ Failed to send prompt', e)
  }
}

export const useLLMChannel = () => {
  return {
    result,
    connect,
    send,
  }
}
