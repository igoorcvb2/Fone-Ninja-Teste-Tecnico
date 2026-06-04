<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import Toaster from '@/components/ui/Toaster.vue'

const links = [
  { to: '/produtos', label: 'Produtos' },
  { to: '/compras', label: 'Compras' },
  { to: '/vendas', label: 'Vendas' },
  { to: '/historico', label: 'Histórico' },
]
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <header class="bg-white border-b border-slate-200">
      <div class="mx-auto max-w-6xl px-6 py-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold tracking-tight text-slate-900">ERP Estoque</h1>
        <nav class="flex gap-1">
          <RouterLink
            v-for="l in links"
            :key="l.to"
            :to="l.to"
            class="px-3 py-1.5 rounded-md text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
            active-class="text-slate-900 bg-slate-100"
          >
            {{ l.label }}
          </RouterLink>
        </nav>
      </div>
    </header>

    <main class="flex-1 mx-auto max-w-6xl w-full px-6 py-8">
      <RouterView />
    </main>

    <Toaster />
  </div>
</template>
