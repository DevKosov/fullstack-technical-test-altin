import { ref, onMounted } from 'vue'
import axios from 'axios'

const token = ref<string | null>(null)

const fetchToken = async () => {
  try {
    await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
      withCredentials: true
    })

    // Laravel place le token dans le cookie "XSRF-TOKEN"
    const cookie = document.cookie
      .split('; ')
      .find(row => row.startsWith('XSRF-TOKEN='))

    token.value = cookie ? decodeURIComponent(cookie.split('=')[1]) : null
  } catch (err) {
    console.error('❌ Failed to fetch CSRF token', err)
    token.value = null
  }
}

export const useXsrfToken = () => {
  onMounted(() => {
    if (!token.value) fetchToken()
  })

  return {
    token,
    refresh: fetchToken
  }
}
