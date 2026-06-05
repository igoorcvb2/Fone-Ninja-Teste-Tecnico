<script setup lang="ts">
import { computed } from 'vue'

type Variante = 'primary' | 'secondary' | 'danger' | 'ghost'

const props = withDefaults(
  defineProps<{
    variante?: Variante
    type?: 'button' | 'submit' | 'reset'
    disabled?: boolean
    loading?: boolean
  }>(),
  {
    variante: 'primary',
    type: 'button',
    disabled: false,
    loading: false,
  },
)

const classes = computed(() => {
  const base = 'inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed'
  const variantes: Record<Variante, string> = {
    primary: 'bg-emerald-500 text-white hover:bg-emerald-600 focus:ring-emerald-300 shadow-sm',
    secondary: 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 focus:ring-slate-300',
    danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-400',
    ghost: 'text-slate-600 hover:bg-slate-100 focus:ring-slate-300',
  }
  return `${base} ${variantes[props.variante]}`
})
</script>

<template>
  <button :type="type" :disabled="disabled || loading" :class="classes">
    <span v-if="loading" class="size-3.5 border-2 border-current border-t-transparent rounded-full animate-spin" />
    <slot />
  </button>
</template>
