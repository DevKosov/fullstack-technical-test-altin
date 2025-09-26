<template>
   <div class="min-h-screen w-full flex items-center justify-center bg-gradient-to-b from-slate-50 to-slate-100 p-6">
      <div class="w-full max-w-md">
         <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl p-8 border border-slate-200">
            <h1 class="text-2xl font-semibold text-slate-800 mb-6">Connexion</h1>

            <form @submit.prevent="submit" class="space-y-4">
               <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                  <input v-model="email" type="email" placeholder="votre@email.com"
                     class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                     autocomplete="username" required />
               </div>

               <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe</label>
                  <input v-model="password" type="password" placeholder=""
                     class="w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                     autocomplete="current-password" required />
               </div>

               <button type="submit" :disabled="loading"
                  class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white font-medium px-4 py-2.5 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed transition">
                  <svg v-if="loading" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                     <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                     <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                  </svg>
                  <span>{{ loading ? 'Connexion…' : 'Se connecter' }}</span>
               </button>

               <p v-if="errorMsg" class="text-sm text-rose-600 mt-2">{{ errorMsg }}</p>
            </form>

         </div>
      </div>
   </div>
</template>


<script setup lang="ts">
import { ref } from 'vue'
import { useAuth } from '@/composables/useAuth'

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMsg = ref<string | null>(null)

const { login } = useAuth()

const emit = defineEmits<{
   (e: 'success'): void
}>()

const submit = async () => {
   errorMsg.value = null
   loading.value = true
   try {
      await login(email.value, password.value)
      emit('success')
   } catch (e) {
      errorMsg.value = 'Échec de connexion. Vérifiez vos identifiants.'
      // console.error(e)
   } finally {
      loading.value = false
   }
}
</script>