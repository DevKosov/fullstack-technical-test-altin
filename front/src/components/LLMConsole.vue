<template>
  <section class="llm-console">
    <h2>Analyse LLM</h2>

    <form class="llm-form" @submit.prevent="sendPrompt">
      <div>
        <label for="prompt">Prompt :</label>
        <input id="prompt" v-model="prompt" placeholder="Écris ton prompt ici" required />
      </div>
      <button type="submit" :disabled="loading">Analyser</button>
    </form>

    <LlmResultCard :data="envelope" :loading="loading" :showRetry="!!errorState" @retry="sendPrompt" />
  </section>
</template>

<script setup lang="ts">

import { ref, onMounted, computed } from 'vue';
import { useLLMChannel } from '@/composables/useLLMChannel';
import LlmResultCard from '@/components/LlmResultCard.vue';
import type { LlmResultEnvelope } from '@/types/llm';

const prompt = ref('')
const { result, loading, analysisId, connect, send } = useLLMChannel()

onMounted(() => { connect() })

const sendPrompt = async () => {
  loading.value = true
  await send(prompt.value)     // your composable puts the result in `result`
}

const envelope = computed<LlmResultEnvelope | null>(() => result.value)
const errorState = computed(() => {
  const r = result.value?.result
  return r && 'status' in r && r.status === 'error' ? r.error : null
})
</script>

<style scoped>
.llm-console {
  padding: 1rem;
  min-width: 400px;
  margin: auto;
}

.llm-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

input {
  padding: .5rem;
  width: 70%;
}

button {
  padding: .5rem 1rem;
}
</style>
