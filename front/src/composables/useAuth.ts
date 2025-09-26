import { ref } from 'vue'
import { api } from '@/lib/api'

const user = ref(null)

const csrf = async () => {
  await api.get('/sanctum/csrf-cookie')
}

export const useAuth = () => {
  const login = async (email: string, password: string) => {
    await csrf()
    await api.post('/login', { email, password })
  }

  const fetchUser = async () => {
    const { data } = await api.get('/api/user')
    user.value = data
    return data
  }

  const logout = async () => {
    await api.post('/logout')
    user.value = null
  }

  return { user, login, logout, fetchUser }
}
