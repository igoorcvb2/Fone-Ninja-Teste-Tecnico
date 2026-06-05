<script setup lang="ts">
import type { Venda } from '@/types/venda'

defineProps<{
  venda: Venda
}>()

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
  <div class="space-y-5">
    <div class="grid grid-cols-2 gap-4 text-sm">
      <div>
        <p class="text-slate-500">Cliente</p>
        <p class="font-medium text-slate-900">{{ venda.cliente }}</p>
      </div>
      <div>
        <p class="text-slate-500">Status</p>
        <span
          :class="[
            'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
            venda.status === 'CONCLUIDA'
              ? 'bg-emerald-50 text-emerald-700'
              : 'bg-slate-100 text-slate-500',
          ]"
        >
          {{ venda.status_label }}
        </span>
      </div>
      <div>
        <p class="text-slate-500">Data</p>
        <p class="font-medium text-slate-900 tabular-nums">{{ formatarData(venda.created_at) }}</p>
      </div>
      <div v-if="venda.cancelada_em">
        <p class="text-slate-500">Cancelada em</p>
        <p class="font-medium text-slate-900 tabular-nums">{{ formatarData(venda.cancelada_em) }}</p>
      </div>
    </div>

    <div>
      <h4 class="text-sm font-medium text-slate-700 mb-2">Itens</h4>
      <div class="border border-slate-200 rounded-md overflow-hidden">
        <table class="w-full text-xs">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="text-left font-medium px-3 py-2">Produto</th>
              <th class="text-right font-medium px-3 py-2">Qtd</th>
              <th class="text-right font-medium px-3 py-2">Preço</th>
              <th class="text-right font-medium px-3 py-2">Custo</th>
              <th class="text-right font-medium px-3 py-2">Subtotal</th>
              <th class="text-right font-medium px-3 py-2">Lucro</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in venda.itens" :key="item.id">
              <td class="px-3 py-2 text-slate-900">
                {{ item.produto?.nome ?? `#${item.produto_id}` }}
              </td>
              <td class="px-3 py-2 text-right tabular-nums">{{ item.quantidade }}</td>
              <td class="px-3 py-2 text-right tabular-nums">{{ formatarMoeda(item.preco_unitario) }}</td>
              <td class="px-3 py-2 text-right tabular-nums text-slate-500">
                {{ formatarMoeda(item.custo_unitario) }}
              </td>
              <td class="px-3 py-2 text-right tabular-nums">{{ formatarMoeda(item.subtotal) }}</td>
              <td
                class="px-3 py-2 text-right tabular-nums font-medium"
                :class="
                  venda.status === 'CANCELADA'
                    ? 'text-slate-400 line-through'
                    : item.lucro_item >= 0
                    ? 'text-emerald-700'
                    : 'text-red-600'
                "
              >
                {{ formatarMoeda(item.lucro_item) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="mt-2 text-[10px] text-slate-400">
        Custo: valor do custo médio do produto no momento da venda.
      </p>
    </div>

    <div class="grid grid-cols-2 gap-4 border-t border-slate-200 pt-4">
      <div>
        <p class="text-xs text-slate-500">Total da venda</p>
        <p class="text-xl font-semibold text-slate-900 tabular-nums">
          {{ formatarMoeda(venda.total) }}
        </p>
      </div>
      <div>
        <p class="text-xs text-slate-500">Lucro</p>
        <p
          class="text-xl font-semibold tabular-nums"
          :class="
            venda.status === 'CANCELADA'
              ? 'text-slate-400 line-through'
              : venda.lucro >= 0
              ? 'text-emerald-700'
              : 'text-red-600'
          "
        >
          {{ formatarMoeda(venda.lucro) }}
        </p>
      </div>
    </div>
  </div>
</template>
