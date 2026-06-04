<script setup lang="ts">
import { useToast } from '@/composables/useToast'

const { toasts, remover } = useToast()
</script>

<template>
  <div
    class="fixed top-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none"
    aria-live="polite"
  >
    <transition-group name="toast" tag="div" class="flex flex-col gap-2">
      <div
        v-for="t in toasts"
        :key="t.id"
        :class="[
          'pointer-events-auto min-w-[280px] max-w-sm rounded-lg shadow-lg p-4 border-l-4 bg-white',
          t.tipo === 'sucesso' && 'border-emerald-500',
          t.tipo === 'erro' && 'border-red-500',
          t.tipo === 'info' && 'border-sky-500',
        ]"
        role="status"
      >
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1">
            <p class="text-sm font-semibold text-slate-900">{{ t.titulo }}</p>
            <p v-if="t.descricao" class="mt-1 text-xs text-slate-600">{{ t.descricao }}</p>
          </div>
          <button
            class="text-slate-400 hover:text-slate-600 text-xs"
            @click="remover(t.id)"
            aria-label="Fechar notificação"
          >
            ✕
          </button>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.25s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
