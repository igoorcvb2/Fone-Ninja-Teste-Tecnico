<script setup lang="ts">
import {
  DialogRoot,
  DialogPortal,
  DialogOverlay,
  DialogContent,
  DialogTitle,
  DialogDescription,
  DialogClose,
} from 'reka-ui'

defineProps<{
  open: boolean
  titulo: string
  descricao?: string
}>()

defineEmits<{
  (e: 'update:open', valor: boolean): void
}>()
</script>

<template>
  <DialogRoot :open="open" @update:open="$emit('update:open', $event)">
    <DialogPortal>
      <DialogOverlay class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40" />
      <DialogContent
        class="fixed top-1/2 left-1/2 z-50 w-full max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-lg bg-white shadow-xl focus:outline-none"
      >
        <header class="flex items-start justify-between border-b border-slate-200 p-5">
          <div>
            <DialogTitle class="text-lg font-semibold text-slate-900">
              {{ titulo }}
            </DialogTitle>
            <DialogDescription v-if="descricao" class="mt-1 text-sm text-slate-500">
              {{ descricao }}
            </DialogDescription>
          </div>
          <DialogClose
            class="text-slate-400 hover:text-slate-600 rounded p-1 transition"
            aria-label="Fechar"
          >
            ✕
          </DialogClose>
        </header>

        <div class="p-5">
          <slot />
        </div>

        <footer v-if="$slots.acoes" class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4 bg-slate-50 rounded-b-lg">
          <slot name="acoes" />
        </footer>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>
