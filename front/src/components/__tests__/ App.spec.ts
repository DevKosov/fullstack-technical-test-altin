import { describe, it, expect } from 'vitest'

import { mount } from '@vue/test-utils'
import App from '@/App.vue'
import LLMConsole from '@/components/LLMConsole.vue'

describe('App', () => {
  it('renders properly', () => {
    const wrapper = mount(App)
    const form = wrapper.find('form')
    expect(form.exists()).toBe(true)
    expect(form.find('h2').text()).toBe('Connexion')
    const inputs = form.findAll('input')
    expect(inputs.length).toBe(2)
    expect(form.find('button').text()).toBe('Se connecter')
    expect(wrapper.findComponent(LLMConsole).exists()).toBe(false)
  })

  it('submits the form', async () => {
    const wrapper = mount(App)
    const form = wrapper.find('form')
    const inputs = form.findAll('input')
    await inputs[0].setValue('testuser')
    await inputs[1].setValue('testpassword')
    await form.trigger('submit.prevent')
    await new Promise(setImmediate)
    await wrapper.vm.$nextTick()
    expect(wrapper.findComponent(LLMConsole).exists()).toBe(true)
  })
})
