<template>
  <section class="llm-console">
    <h2>Analyse LLM</h2>

    <form @submit.prevent="sendPrompt">
      <label for="prompt">Prompt :</label>
      <input
        id="prompt"
        v-model="prompt"
        placeholder="Écris ton prompt ici"
        required
      />
      <button type="submit">Analyser</button>
    </form>

    <div v-if="result" class="result">
      <h3>Résultat :</h3>
      <p><strong>Résumé :</strong> {{ result.result.summary }}</p>
      <p><strong>Sentiment :</strong> {{ result.result.sentiment }}</p>
      <p><strong>Mots clés :</strong> {{ result.result.keywords.join(', ') }}</p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useLLMChannel } from '../composables/useLLMChannel'

const prompt = ref('')
const { result, connect, send } = useLLMChannel()
onMounted(() => {
  connect()
})
const sendPrompt = () => {
  send(prompt.value)
  result.value = null
}
</script>

<style scoped>
.llm-console {
  padding: 1rem;
  max-width: 600px;
  margin: auto;
}

input {
  padding: 0.5rem;
  margin-right: 1rem;
  width: 70%;
}

button {
  padding: 0.5rem 1rem;
}

.result {
  margin-top: 2rem;
  background: #f4f4f4;
  padding: 1rem;
  border-radius: 8px;
  color: #1a202c;
}

.waiting {
  margin-top: 1rem;
  font-style: italic;
}
</style>
