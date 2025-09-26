<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import LLMConsole from '@/components/LLMConsole.vue'
import LoginForm from '@/components/LoginForm.vue'

const { user, fetchUser } = useAuth()

const afterLogin = async () => {
  await fetchUser()
}

onMounted(() => {
  fetchUser().catch(() => { })
})
</script>

<template>
  <main class="min-h-screen w-full">
    <LoginForm v-if="!user" @success="afterLogin" />
    <LLMConsole v-else />
  </main>
</template>
