<script setup lang="ts">
import { onMounted, ref } from 'vue'
import Button from '@/components/ui/Button.vue'
import FormCompra from '@/components/compras/FormCompra.vue'
import { listarCompras } from '@/api/compras'
import type { Compra } from '@/types/compra'

const compras = ref<Compra[]>([])
const carregando = ref(false)
const formAberto = ref(false)

async function recarregar() {
  carregando.value = true
  try {
    compras.value = await listarCompras()
  } finally {
    carregando.value = false
  }
}

onMounted(recarregar)

function formatarMoeda(v: number) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(v)
}

function formatarData(iso: string) {
  return new Date(iso).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function aoRegistrar() {
  formAberto.value = false
  await recarregar()
}
</script>

<template>
  <section>
    <header class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">Compras</h1>
        <p class="mt-1 text-sm text-slate-500">
          Cada compra entra com os itens no estoque e recalcula o custo médio dos produtos.
        </p>
      </div>
      <Button v-if="!formAberto" @click="formAberto = true">+ Nova compra</Button>
    </header>

    <div
      v-if="formAberto"
      class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 mb-6"
    >
      <h2 class="text-lg font-medium text-slate-900 mb-4">Nova compra</h2>
      <FormCompra @registrada="aoRegistrar" @cancelar="formAberto = false" />
    </div>

    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="text-sm font-medium text-slate-700">Histórico de compras</h2>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Data</th>
            <th class="text-left font-medium px-4 py-3">Fornecedor</th>
            <th class="text-right font-medium px-4 py-3">Itens</th>
            <th class="text-right font-medium px-4 py-3">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="carregando">
            <td colspan="4" class="px-4 py-8 text-center text-slate-400">Carregando…</td>
          </tr>
          <tr v-else-if="!compras.length">
            <td colspan="4" class="px-4 py-12 text-center text-slate-400">
              Ainda não há compras registradas.
            </td>
          </tr>
          <tr v-for="c in compras" :key="c.id" class="hover:bg-slate-50">
            <td class="px-4 py-3 text-slate-600 tabular-nums">
              {{ formatarData(c.created_at) }}
            </td>
            <td class="px-4 py-3 font-medium text-slate-900">{{ c.fornecedor }}</td>
            <td class="px-4 py-3 text-right text-slate-600">
              {{ c.itens.length }}
            </td>
            <td class="px-4 py-3 text-right font-medium tabular-nums text-slate-900">
              {{ formatarMoeda(c.total) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>
