import { vi } from 'vitest'

let isLoggedIn = false

vi.mock(import('axios'), async (importOriginal) => {
  const mockAxiosInstance = {
    get: vi.fn((url: string) => {
      if (url.includes('/sanctum/csrf-cookie')) {
        return Promise.resolve({ data: { message: 'Mocked response from sanctum' } })
      }
      if (url.includes('/api/user')) {
        if (isLoggedIn) {
          return Promise.resolve({
            data: {
              data: {
                id: 3,
                name: 'Test User',
                email: 'test@example.com',
                email_verified_at: '2025-06-18T09:14:48.000000Z',
                two_factor_secret: null,
                two_factor_recovery_codes: null,
                two_factor_confirmed_at: null,
                created_at: '2025-06-18T09:14:49.000000Z',
                updated_at: '2025-06-18T09:14:49.000000Z',
              },
            },
          })
        }
        return Promise.resolve({ data: null })
      }
      return Promise.reject(new Error('Not Found'))
    }),
    post: vi.fn((url: string) => {
      if (url.includes('login')) {
        isLoggedIn = true
        return Promise.resolve({ data: { message: 'Mocked response from login' } })
      }
      return Promise.resolve({ data: { success: true } })
    }),
    interceptors: {
      request: {
        use: vi.fn(),
      },
    },
  }
  const actual = await importOriginal()
  return {
    ...actual,
    default: {
      ...actual,
      ...mockAxiosInstance,
      create: vi.fn(() => mockAxiosInstance),
      defaults: {
        baseURL: 'http://localhost:8000',
        withCredentials: true,
      },
    },
  }
})
