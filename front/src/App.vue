<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuth } from './composables/useAuth.ts'
import LLMConsole from './components/LLMConsole.vue'

const email = ref('')
const password = ref('')

const { user, login, fetchUser } = useAuth()

const submit = async () => {
  try {
    await login(email.value, password.value)
    await fetchUser()
  } catch (e) {
    console.error('Échec de connexion', e)
  }
}

// try auto-login on mount
onMounted(() => {
  fetchUser().catch(() => {}) // ignore fail silently
})
</script>
<template>
  <main>
    <form @submit.prevent="submit" v-if="!user">
      <h2>Connexion</h2>
      <input v-model="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Mot de passe" />
      <button>Se connecter</button>
    </form>

    <LLMConsole v-if="user" />
  </main>
</template>


