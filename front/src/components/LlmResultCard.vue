<template>
   <section v-if="data"
      class="w-full max-w-3xl mx-auto rounded-2xl border border-slate-200 bg-white/90 backdrop-blur shadow-lg p-6 min-w-[600px]"
      aria-live="polite">
      <header class="mb-4 flex items-start justify-between gap-4">
         <div>
            <h3 class="text-lg font-semibold text-slate-900">Résultat</h3>
            <p v-if="data.prompt" class="mt-1 text-sm text-slate-600">
               <span class="font-medium">Prompt :</span>
               <code class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 text-slate-700">{{ data.prompt }}</code>
            </p>
         </div>
         <small class="text-xs text-slate-500 shrink-0">
            ID&nbsp;: <code class="font-mono">{{ data.job_id }}</code>
         </small>
      </header>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center gap-3 text-slate-600">
         <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-20" />
            <path d="M4 12a8 8 0 0 1 8-8" fill="currentColor" class="opacity-60" />
         </svg>
         <span>Analyse en cours…</span>
      </div>

      <!-- Error -->
      <div v-else-if="isError(data.result)" class="rounded-xl border border-rose-200 bg-rose-50 text-rose-700 p-4"
         role="alert">
         <p class="font-medium">Erreur&nbsp;: {{ (data.result as any).error }}</p>
         <ul v-if="(data.result as any).violations?.length" class="mt-2 list-disc pl-5 text-sm">
            <li v-for="v in (data.result as any).violations" :key="v">{{ v }}</li>
         </ul>

         <div v-if="showRetry" class="mt-4">
            <button type="button" @click="emit('retry')"
               class="inline-flex items-center rounded-lg bg-rose-600 px-3 py-1.5 text-white text-sm font-medium hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500">
               Réessayer
            </button>
         </div>
      </div>

      <!-- Success -->
      <div v-else class="grid gap-3">
         <p>
            <span class="font-medium text-slate-700">Résumé :</span>
            <span class="text-slate-900">{{ (data.result as any).summary }}</span>
         </p>
         <p>
            <span class="font-medium text-slate-700">Sentiment :</span>
            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-0.5 text-slate-800 text-sm">
               {{ (data.result as any).sentiment }}
            </span>
         </p>
         <p v-if="(data.result as any).keywords?.length">
            <span class="font-medium text-slate-700">Mots clés :</span>
            <span class="ml-1 text-slate-900">
               {{ (data.result as any).keywords.join(', ') }}
            </span>
         </p>
         <div class="flex flex-wrap gap-4 text-sm text-slate-600">
            <p v-if="(data.result as any).tokens != null">
               <span class="font-medium text-slate-700">Tokens :</span>
               {{ (data.result as any).tokens }}
            </p>
            <p v-if="(data.result as any).duration_ms != null">
               <span class="font-medium text-slate-700">Durée :</span>
               {{ (data.result as any).duration_ms }} ms
            </p>
         </div>
      </div>
   </section>
</template>

<script lang="ts" setup>

import type { LlmErrorResult, LlmPayload } from '@/types/llm'

/** Props */
const props = defineProps<{
   data: LlmPayload | null
   loading?: boolean
   showRetry?: boolean
}>()

/** Emits */
const emit = defineEmits<{
   (e: 'retry'): void
}>()

/** Type guard */
const isError = (r: LlmPayload['result']): r is LlmErrorResult =>
   (r as LlmErrorResult).status === 'error'
</script>

<style scoped>
/* Optional: keep it purely Tailwind; no custom CSS required here. */
</style>
