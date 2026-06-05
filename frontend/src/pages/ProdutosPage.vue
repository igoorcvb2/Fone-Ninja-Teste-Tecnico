<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { Package } from 'lucide-vue-next'
import { useProdutosStore } from '@/stores/produtos'
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import FormProduto from '@/components/produtos/FormProduto.vue'

const produtos = useProdutosStore()
const modalAberto = ref(false)

onMounted(() => produtos.carregar())

function formatarMoeda(valor: number) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(valor)
}
</script>

<template>
  <section>
    <header class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">Produtos</h1>
        <p class="mt-1 text-sm text-slate-500">
          Cadastre produtos e acompanhe o custo médio e o estoque atual.
        </p>
      </div>
      <Button @click="modalAberto = true">+ Novo produto</Button>
    </header>

    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left font-medium px-4 py-3">Nome</th>
            <th class="text-right font-medium px-4 py-3">Custo médio</th>
            <th class="text-right font-medium px-4 py-3">Preço de venda</th>
            <th class="text-right font-medium px-4 py-3">Estoque</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="produtos.carregando">
            <td colspan="4" class="px-4 py-8 text-center text-slate-400">Carregando…</td>
          </tr>
          <tr v-else-if="!produtos.lista.length">
            <td colspan="4">
              <EmptyState
                :icon="Package"
                titulo="Nenhum produto cadastrado"
                descricao="Comece cadastrando seu primeiro produto. Ele entra no catálogo com estoque e custo zerados,vão ser atualizados a cada compra."
              >
                <template #acao>
                  <Button @click="modalAberto = true">+ Cadastrar primeiro produto</Button>
                </template>
              </EmptyState>
            </td>
          </tr>
          <tr v-for="p in produtos.lista" :key="p.id" class="hover:bg-slate-50">
            <td class="px-4 py-3 font-medium text-slate-900">{{ p.nome }}</td>
            <td class="px-4 py-3 text-right tabular-nums text-slate-600">
              {{ formatarMoeda(p.custo_medio) }}
            </td>
            <td class="px-4 py-3 text-right tabular-nums text-slate-600">
              {{ formatarMoeda(p.preco_venda) }}
            </td>
            <td class="px-4 py-3 text-right">
              <span
                :class="[
                  'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium tabular-nums',
                  p.estoque > 0
                    ? 'bg-emerald-50 text-emerald-700'
                    : 'bg-slate-100 text-slate-500',
                ]"
              >
                {{ p.estoque }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Modal
      v-model:open="modalAberto"
      titulo="Cadastrar produto"
      descricao="O produto entra com estoque e custo médio zerados."
    >
      <FormProduto
        @criado="modalAberto = false"
        @cancelar="modalAberto = false"
      />
    </Modal>
  </section>
</template>
