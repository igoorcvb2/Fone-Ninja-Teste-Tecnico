<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { Package, ShoppingCart, Receipt, BarChart3 } from 'lucide-vue-next'
import Toaster from '@/components/ui/Toaster.vue'

const links = [
  { to: '/produtos', label: 'Produtos', icon: Package },
  { to: '/compras', label: 'Compras', icon: ShoppingCart },
  { to: '/vendas', label: 'Vendas', icon: Receipt },
  { to: '/historico', label: 'Histórico', icon: BarChart3 },
]
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <header class="bg-white border-b border-slate-200">
      <div class="mx-auto max-w-6xl px-6 py-4 flex items-center justify-between">
        <RouterLink to="/" class="flex items-center gap-2.5 group">
          <img
            src="/favicon.png"
            alt="Fone Ninja"
            class="h-8 w-8 rounded-full shadow-sm group-hover:scale-105 transition-transform"
          />
          <div class="leading-tight">
            <h1 class="text-base font-semibold tracking-tight">
              <span class="text-slate-900">Fone</span>
              <span class="text-emerald-500">Ninja</span>
            </h1>
            <p class="text-[10px] font-medium uppercase tracking-widest text-slate-400">
              ERP de estoque
            </p>
          </div>
        </RouterLink>
        <nav class="flex gap-1">
          <RouterLink
            v-for="l in links"
            :key="l.to"
            :to="l.to"
            class="flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
            active-class="text-slate-900 bg-slate-100"
          >
            <component :is="l.icon" class="w-4 h-4" />
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
