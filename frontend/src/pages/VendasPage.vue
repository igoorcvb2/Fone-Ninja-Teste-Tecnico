<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { Receipt, Eye } from 'lucide-vue-next'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import FormVenda from '@/components/vendas/FormVenda.vue'
import DetalheVenda from '@/components/vendas/DetalheVenda.vue'
import { listarVendas, cancelarVenda } from '@/api/vendas'
import { useProdutosStore } from '@/stores/produtos'
import { useToast } from '@/composables/useToast'
import type { Venda } from '@/types/venda'

const vendas = ref<Venda[]>([])
const carregando = ref(false)
const formAberto = ref(false)
const cancelando = ref(false)
const vendaParaCancelar = ref<Venda | null>(null)
const vendaParaDetalhar = ref<Venda | null>(null)

const produtos = useProdutosStore()
const toast = useToast()

async function recarregar() {
  carregando.value = true
  try {
    vendas.value = await listarVendas()
  } finally {
    carregando.value = false
  }
}

onMounted(recarregar)

async function aoRegistrar() {
  formAberto.value = false
  await recarregar()
}

async function confirmarCancelamento() {
  if (!vendaParaCancelar.value) return
  cancelando.value = true
  try {
    await cancelarVenda(vendaParaCancelar.value.id)
    toast.sucesso('Venda cancelada', 'Os itens foram devolvidos ao estoque.')
    await Promise.all([recarregar(), produtos.carregar(true)])
    vendaParaCancelar.value = null
  } catch {
    toast.erro('Não foi possível cancelar a venda.')
  } finally {
    cancelando.value = false
  }
}

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
    <header class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">Vendas</h1>
        <p class="mt-1 text-sm text-slate-500">
          O lucro é calculado em relação ao custo médio atual de cada produto.
        </p>
      </div>
      <Button v-if="!formAberto" @click="formAberto = true">+ Nova venda</Button>
    </header>

    <div
      v-if="formAberto"
      class="bg-white border border-slate-200 rounded-lg shadow-sm p-6 mb-6"
    >
      <h2 class="text-lg font-medium text-slate-900 mb-4">Nova venda</h2>
      <FormVenda @registrada="aoRegistrar" @cancelar="formAberto = false" />
    </div>

    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="text-sm font-medium text-slate-700">Vendas registradas</h2>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Data</th>
            <th class="text-left font-medium px-4 py-3">Cliente</th>
            <th class="text-left font-medium px-4 py-3">Status</th>
            <th class="text-right font-medium px-4 py-3">Total</th>
            <th class="text-right font-medium px-4 py-3">Lucro</th>
            <th class="text-right font-medium px-4 py-3 w-32">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="carregando">
            <td colspan="6" class="px-4 py-8 text-center text-slate-400">Carregando…</td>
          </tr>
          <tr v-else-if="!vendas.length">
            <td colspan="6">
              <EmptyState
                :icon="Receipt"
                titulo="Nenhuma venda registrada"
                descricao="Registre uma venda pra ver o lucro calculado em cima do custo médio atual dos produtos."
              >
                <template #acao>
                  <Button @click="formAberto = true">+ Registrar primeira venda</Button>
                </template>
              </EmptyState>
            </td>
          </tr>
          <tr
            v-for="v in vendas"
            :key="v.id"
            class="hover:bg-slate-50 cursor-pointer transition"
            @click="vendaParaDetalhar = v"
          >
            <td class="px-4 py-3 text-slate-600 tabular-nums">{{ formatarData(v.created_at) }}</td>
            <td class="px-4 py-3 font-medium text-slate-900">{{ v.cliente }}</td>
            <td class="px-4 py-3">
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
            <td class="px-4 py-3 text-right tabular-nums text-slate-900">
              {{ formatarMoeda(v.total) }}
            </td>
            <td class="px-4 py-3 text-right tabular-nums">
              <span
                :class="
                  v.status === 'CANCELADA'
                    ? 'text-slate-400 line-through'
                    : v.lucro >= 0
                    ? 'text-emerald-700 font-medium'
                    : 'text-red-600 font-medium'
                "
              >
                {{ formatarMoeda(v.lucro) }}
              </span>
            </td>
            <td class="px-4 py-3 text-right" @click.stop>
              <div class="flex items-center justify-end gap-1">
                <button
                  class="p-1.5 rounded text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                  aria-label="Ver detalhes"
                  @click="vendaParaDetalhar = v"
                >
                  <Eye class="w-4 h-4" />
                </button>
                <Button
                  v-if="v.status === 'CONCLUIDA'"
                  variante="ghost"
                  @click="vendaParaCancelar = v"
                >
                  Cancelar
                </Button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Modal
      :open="vendaParaCancelar !== null"
      titulo="Cancelar venda"
      :descricao="
        vendaParaCancelar
          ? `A venda #${vendaParaCancelar.id} de ${vendaParaCancelar.cliente} será marcada como cancelada e os itens voltarão para o estoque.`
          : ''
      "
      @update:open="(v) => !v && (vendaParaCancelar = null)"
    >
      <p class="text-sm text-slate-600">
        Tem certeza que deseja prosseguir? Essa ação não pode ser desfeita.
      </p>

      <template #acoes>
        <Button variante="secondary" @click="vendaParaCancelar = null">
          Voltar
        </Button>
        <Button variante="danger" :loading="cancelando" @click="confirmarCancelamento">
          Cancelar venda
        </Button>
      </template>
    </Modal>

    <Modal
      :open="vendaParaDetalhar !== null"
      :titulo="vendaParaDetalhar ? `Venda #${vendaParaDetalhar.id}` : ''"
      tamanho="lg"
      @update:open="(v) => !v && (vendaParaDetalhar = null)"
    >
      <DetalheVenda v-if="vendaParaDetalhar" :venda="vendaParaDetalhar" />
    </Modal>
  </section>
</template>
