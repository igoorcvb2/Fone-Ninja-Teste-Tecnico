<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { TabsRoot, TabsList, TabsTrigger, TabsContent } from 'reka-ui'
import { listarCompras } from '@/api/compras'
import { listarVendas } from '@/api/vendas'
import type { Compra } from '@/types/compra'
import type { Venda } from '@/types/venda'

const aba = ref<'vendas' | 'compras'>('vendas')
const filtroData = ref('')

const compras = ref<Compra[]>([])
const vendas = ref<Venda[]>([])
const carregando = ref(false)

async function carregar() {
  carregando.value = true
  try {
    const [c, v] = await Promise.all([listarCompras(), listarVendas()])
    compras.value = c
    vendas.value = v
  } finally {
    carregando.value = false
  }
}

onMounted(carregar)

function dentroDoFiltro(iso: string): boolean {
  if (!filtroData.value) return true
  const data = new Date(iso).toISOString().slice(0, 10)
  return data === filtroData.value
}

const comprasFiltradas = computed(() => compras.value.filter((c) => dentroDoFiltro(c.created_at)))
const vendasFiltradas = computed(() => vendas.value.filter((v) => dentroDoFiltro(v.created_at)))

const totaisVendas = computed(() => {
  const concluidas = vendasFiltradas.value.filter((v) => v.status === 'CONCLUIDA')
  return {
    quantidade: concluidas.length,
    receita: concluidas.reduce((s, v) => s + v.total, 0),
    lucro: concluidas.reduce((s, v) => s + v.lucro, 0),
  }
})

const totaisCompras = computed(() => ({
  quantidade: comprasFiltradas.value.length,
  gasto: comprasFiltradas.value.reduce((s, c) => s + c.total, 0),
}))

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
</script>

<template>
  <section>
    <header class="mb-6">
      <h1 class="text-2xl font-semibold text-slate-900">Histórico</h1>
      <p class="mt-1 text-sm text-slate-500">
        Visão combinada de vendas e compras com resumo financeiro.
      </p>
    </header>

    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
      <TabsRoot v-model="aba" class="w-full">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-2">
          <TabsList class="flex gap-1">
            <TabsTrigger
              value="vendas"
              class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-900 data-[state=active]:text-slate-900 data-[state=active]:border-b-2 data-[state=active]:border-slate-900 -mb-px"
            >
              Vendas
            </TabsTrigger>
            <TabsTrigger
              value="compras"
              class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-900 data-[state=active]:text-slate-900 data-[state=active]:border-b-2 data-[state=active]:border-slate-900 -mb-px"
            >
              Compras
            </TabsTrigger>
          </TabsList>

          <label class="flex items-center gap-2 text-xs text-slate-500">
            Filtrar por data:
            <input
              v-model="filtroData"
              type="date"
              class="border border-slate-300 rounded px-2 py-1 text-xs"
            />
            <button
              v-if="filtroData"
              class="text-slate-400 hover:text-slate-700"
              @click="filtroData = ''"
              aria-label="Limpar filtro"
            >
              ✕
            </button>
          </label>
        </div>

        <TabsContent value="vendas" class="p-4">
          <div class="grid grid-cols-3 gap-4 mb-4 text-sm">
            <div class="bg-slate-50 rounded-md p-3">
              <p class="text-slate-500">Vendas concluídas</p>
              <p class="text-xl font-semibold text-slate-900 tabular-nums">
                {{ totaisVendas.quantidade }}
              </p>
            </div>
            <div class="bg-slate-50 rounded-md p-3">
              <p class="text-slate-500">Receita</p>
              <p class="text-xl font-semibold text-slate-900 tabular-nums">
                {{ formatarMoeda(totaisVendas.receita) }}
              </p>
            </div>
            <div class="bg-slate-50 rounded-md p-3">
              <p class="text-slate-500">Lucro</p>
              <p
                class="text-xl font-semibold tabular-nums"
                :class="totaisVendas.lucro >= 0 ? 'text-emerald-700' : 'text-red-600'"
              >
                {{ formatarMoeda(totaisVendas.lucro) }}
              </p>
            </div>
          </div>

          <table class="w-full text-sm">
            <thead class="text-slate-600 border-b border-slate-200">
              <tr>
                <th class="text-left font-medium px-2 py-2">Data</th>
                <th class="text-left font-medium px-2 py-2">Cliente</th>
                <th class="text-left font-medium px-2 py-2">Status</th>
                <th class="text-right font-medium px-2 py-2">Total</th>
                <th class="text-right font-medium px-2 py-2">Lucro</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="carregando">
                <td colspan="5" class="py-6 text-center text-slate-400">Carregando…</td>
              </tr>
              <tr v-else-if="!vendasFiltradas.length">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  Nenhuma venda encontrada.
                </td>
              </tr>
              <tr v-for="v in vendasFiltradas" :key="v.id">
                <td class="px-2 py-2 text-slate-600 tabular-nums">{{ formatarData(v.created_at) }}</td>
                <td class="px-2 py-2 font-medium text-slate-900">{{ v.cliente }}</td>
                <td class="px-2 py-2">
                  <span
                    :class="[
                      'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                      v.status === 'CONCLUIDA'
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'bg-slate-100 text-slate-500',
                    ]"
                  >
                    {{ v.status_label }}
                  </span>
                </td>
                <td class="px-2 py-2 text-right tabular-nums">{{ formatarMoeda(v.total) }}</td>
                <td class="px-2 py-2 text-right tabular-nums">
                  <span
                    :class="
                      v.status === 'CANCELADA'
                        ? 'text-slate-400 line-through'
                        : v.lucro >= 0
                        ? 'text-emerald-700'
                        : 'text-red-600'
                    "
                  >
                    {{ formatarMoeda(v.lucro) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </TabsContent>

        <TabsContent value="compras" class="p-4">
          <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
            <div class="bg-slate-50 rounded-md p-3">
              <p class="text-slate-500">Compras registradas</p>
              <p class="text-xl font-semibold text-slate-900 tabular-nums">
                {{ totaisCompras.quantidade }}
              </p>
            </div>
            <div class="bg-slate-50 rounded-md p-3">
              <p class="text-slate-500">Gasto total</p>
              <p class="text-xl font-semibold text-slate-900 tabular-nums">
                {{ formatarMoeda(totaisCompras.gasto) }}
              </p>
            </div>
          </div>

          <table class="w-full text-sm">
            <thead class="text-slate-600 border-b border-slate-200">
              <tr>
                <th class="text-left font-medium px-2 py-2">Data</th>
                <th class="text-left font-medium px-2 py-2">Fornecedor</th>
                <th class="text-right font-medium px-2 py-2">Itens</th>
                <th class="text-right font-medium px-2 py-2">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="carregando">
                <td colspan="4" class="py-6 text-center text-slate-400">Carregando…</td>
              </tr>
              <tr v-else-if="!comprasFiltradas.length">
                <td colspan="4" class="py-8 text-center text-slate-400">
                  Nenhuma compra encontrada.
                </td>
              </tr>
              <tr v-for="c in comprasFiltradas" :key="c.id">
                <td class="px-2 py-2 text-slate-600 tabular-nums">{{ formatarData(c.created_at) }}</td>
                <td class="px-2 py-2 font-medium text-slate-900">{{ c.fornecedor }}</td>
                <td class="px-2 py-2 text-right text-slate-600">{{ c.itens.length }}</td>
                <td class="px-2 py-2 text-right font-medium tabular-nums text-slate-900">
                  {{ formatarMoeda(c.total) }}
                </td>
              </tr>
            </tbody>
          </table>
        </TabsContent>
      </TabsRoot>
    </div>
  </section>
</template>
