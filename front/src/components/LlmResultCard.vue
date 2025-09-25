<template>
   <section class="result-card" v-if="data">
      <header class="result-header">
         <h3>Résultat</h3>
         <small v-if="data.prompt">Prompt : <code>{{ data.prompt }}</code></small>
         <small>ID : <code>{{ data.job_id }}</code></small>
      </header>

      <!-- Loading -->
      <div v-if="loading" class="loading">Analyse en cours…</div>

      <!-- Error -->
      <div v-else-if="isError(data.result)" class="error">
         <strong>Erreur :</strong> {{ data.result.error }}
         <ul v-if="data.result.violations?.length">
            <li v-for="v in data.result.violations" :key="v">{{ v }}</li>
         </ul>
         <button v-if="showRetry" @click="$emit('retry')">Réessayer</button>
      </div>

      <!-- Success -->
      <div v-else class="success">
         <p><strong>Résumé :</strong> {{ data.result.summary }}</p>
         <p><strong>Sentiment :</strong> {{ data.result.sentiment }}</p>
         <p><strong>Mots clés :</strong> {{ data.result.keywords.join(', ') }}</p>
         <p v-if="data.result.tokens != null"><strong>Tokens :</strong> {{ data.result.tokens }}</p>
         <p v-if="data.result.duration_ms != null"><strong>Durée :</strong> {{ data.result.duration_ms }} ms</p>
      </div>
   </section>
</template>

<script setup lang="ts">
import type { LlmResultEnvelope, LlmResultPayload } from '@/types/llm'
import { isError } from '@/types/llm'

defineProps<{
   data: LlmResultEnvelope | null
   loading?: boolean
   showRetry?: boolean
}>()

defineEmits<{
   (e: 'retry'): void
}>()

// re-export the guard so template can use it
// (or use a computed wrapper if you prefer)
</script>

<style scoped>
.result-card {
   margin-top: 1rem;
   padding: 1rem;
   border-radius: 12px;
   background: #f7fafc;
   color: #1a202c;
   box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
}

.result-header {
   display: grid;
   gap: .25rem;
   margin-bottom: .75rem;
}

.loading {
   font-style: italic;
}

.error {
   color: #b91c1c;
}

.success {
   color: #1a202c;
}

code {
   background: #edf2f7;
   padding: .1rem .3rem;
   border-radius: 6px;
}

button {
   margin-top: .5rem;
   padding: .4rem .8rem;
   border-radius: 8px;
   border: 0;
   background: #1f2937;
   color: white;
   cursor: pointer;
}
</style>
 
