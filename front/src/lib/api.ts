// src/lib/api.ts
import axios from 'axios'
import Cookies from 'js-cookie'

export const api = axios.create({
  baseURL: 'http://localhost:8000',
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
})

api.interceptors.request.use((config) => {
  const xsrf = Cookies.get('XSRF-TOKEN')
  if (xsrf) config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrf)
  return config
})
