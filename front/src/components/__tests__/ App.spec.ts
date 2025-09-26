// src/components/__tests__/App.spec.ts
import { describe, it, expect, vi } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import { ref } from 'vue'

// 🔧 Mock the composable so we control auth state in tests
const userRef = ref<null | { id: number }>(null)
const loginSpy = vi.fn(async () => {
  // pretend login succeeded; LoginForm emits "success" and App calls fetchUser
  userRef.value = { id: 1 }
})
const fetchUserSpy = vi.fn(async () => {
  userRef.value = { id: 1 }
})

vi.mock('@/composables/useAuth', () => ({
  useAuth: () => ({
    user: userRef,
    login: loginSpy,
    fetchUser: fetchUserSpy,
  }),
}))

import App from '@/App.vue'
import LLMConsole from '@/components/LLMConsole.vue'
import LoginForm from '@/components/LoginForm.vue'

describe('App', () => {
  it('renders the LoginForm by default', () => {
    userRef.value = null
    const wrapper = mount(App)

    // LoginForm is present
    const login = wrapper.findComponent(LoginForm)
    expect(login.exists()).toBe(true)

    // Title text comes from LoginForm (it's an <h1> now)
    expect(login.text()).toContain('Connexion')

    // LLMConsole is not visible yet
    expect(wrapper.findComponent(LLMConsole).exists()).toBe(false)
  })

  it('submits the form and shows LLMConsole', async () => {
    userRef.value = null
    const wrapper = mount(App)

    const login = wrapper.findComponent(LoginForm)
    const inputs = login.findAll('input')
    expect(inputs.length).toBe(2)

    await inputs[0].setValue('test@example.com')
    await inputs[1].setValue('secret123')

    // Submit the LoginForm
    await login.find('form').trigger('submit.prevent')

    // Wait for composable mocks + re-render
    await flushPromises()

    // After "login" the App shows LLMConsole
    expect(wrapper.findComponent(LLMConsole).exists()).toBe(true)

    // Optional: ensure our mocks were used
    expect(loginSpy).toHaveBeenCalledTimes(1)
  })
})
