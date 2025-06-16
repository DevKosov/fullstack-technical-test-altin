import axios from 'axios'
import { ref } from 'vue'
import Cookies from 'js-cookie'

const user = ref(null)

const api = axios.create({
  baseURL: 'http://localhost:8000',
  withCredentials: true
})

// Automatically attach the XSRF token from cookie
api.interceptors.request.use(config => {
  const xsrfToken = Cookies.get('XSRF-TOKEN')
  if (xsrfToken) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken)
  }
  return config
})

const csrf = async () => {
  await api.get('/sanctum/csrf-cookie')
}

const login = async (email: string, password: string) => {
  await csrf()

  const response = await api.post('/login', {
    email,
    password
  })

  return response.data
}

const fetchUser = async () => {
  const response = await api.get('/api/user')
  user.value = response.data
  return user.value
}

const logout = async () => {
  await api.post('/logout')
  user.value = null
}

export const useAuth = () => {
  return {
    user,
    login,
    logout,
    fetchUser
  }
}
