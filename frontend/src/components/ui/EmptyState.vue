<script setup lang="ts">
import type { Component } from 'vue'

defineProps<{
  icon: Component
  titulo: string
  descricao?: string
}>()
</script>

<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-4">
      <component :is="icon" class="w-7 h-7 text-slate-400" />
    </div>
    <h3 class="text-sm font-medium text-slate-700">{{ titulo }}</h3>
    <p v-if="descricao" class="mt-1 text-xs text-slate-500 max-w-sm">{{ descricao }}</p>
    <div v-if="$slots.acao" class="mt-4">
      <slot name="acao" />
    </div>
  </div>
</template>
