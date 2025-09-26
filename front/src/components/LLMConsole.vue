<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useLLMChannel } from '@/composables/useLLMChannel'
import LlmResultCard from '@/components/LlmResultCard.vue'
import type { LlmResultEnvelope } from '@/types/llm'

const prompt = ref('')
const { result, loading, connect, send } = useLLMChannel()

onMounted(() => { connect() })

const sendPrompt = async () => {
  if (!prompt.value.trim() || loading.value) return
  loading.value = true
  await send(prompt.value)
}

const envelope = computed<LlmResultEnvelope | null>(() => result.value)
const errorState = computed(() => {
  const r = result.value?.result
  return r && 'status' in r && r.status === 'error' ? r.error : null
})
</script>

<template>
  <section class="w-full max-w-3xl mx-auto min-h-screen flex flex-col justify-center items-center">
    <!-- Prompt form -->
    <form class="grid gap-4 rounded-2xl border border-slate-200 bg-white/90 backdrop-blur p-5 shadow-sm min-w-[600px]" @submit.prevent="sendPrompt">

      <h2 class="text-xl font-semibold text-slate-900">Analyse LLM</h2>
      <div class="grid gap-2">
        <label for="prompt" class="text-sm font-medium text-slate-700">Prompt</label>
        <input id="prompt" v-model="prompt" placeholder="Écris ton prompt ici" required :disabled="loading" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder:text-slate-400
                 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent disabled:opacity-60" />
      </div>

      <div class="flex items-center justify-between">
        <button type="submit" :disabled="loading || !prompt.trim()"
          class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-white font-medium
                 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500">
          
          <svg v-if="loading" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
            <path d="M4 12a8 8 0 0 1 8-8" fill="currentColor" class="opacity-75" />
          </svg>

          <span>{{ loading ? 'Analyse en cours…' : 'Analyser' }}</span>
        </button>
      </div>
    </form>

    <!-- Result -->
    <div class="mt-6">
      <LlmResultCard :data="envelope" :loading="loading" :showRetry="!!errorState" @retry="sendPrompt" />
    </div>
  </section>
</template>
