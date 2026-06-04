<script setup lang="ts">
import { computed, reactive, ref, onMounted } from 'vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import Button from '@/components/ui/Button.vue'
import { registrarCompra } from '@/api/compras'
import { useProdutosStore } from '@/stores/produtos'
import { useToast } from '@/composables/useToast'
import { isAxiosError } from 'axios'

interface ItemLinha {
  _id: string
  produto_id: string
  quantidade: string
  preco_unitario: string
}

const emit = defineEmits<{
  (e: 'registrada'): void
  (e: 'cancelar'): void
}>()

const produtos = useProdutosStore()
const toast = useToast()

function novaLinha(): ItemLinha {
  return {
    _id: Math.random().toString(36).slice(2),
    produto_id: '',
    quantidade: '',
    preco_unitario: '',
  }
}

const fornecedor = ref('')
const itens = reactive<ItemLinha[]>([novaLinha()])
const enviando = ref(false)
const erroFornecedor = ref<string | undefined>()

onMounted(() => produtos.carregar())

const opcoesProdutos = computed(() =>
  produtos.lista.map((p) => ({
    value: p.id,
    label: p.nome,
  })),
)

const total = computed(() =>
  itens.reduce((acc, it) => {
    const q = Number(it.quantidade) || 0
    const p = Number(it.preco_unitario) || 0
    return acc + q * p
  }, 0),
)

function adicionarItem() {
  itens.push(novaLinha())
}

function removerItem(idx: number) {
  if (itens.length === 1) return
  itens.splice(idx, 1)
}

function formatarMoeda(v: number) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(v)
}

function validar(): boolean {
  erroFornecedor.value = undefined
  if (fornecedor.value.trim().length < 2) {
    erroFornecedor.value = 'Informe o nome do fornecedor.'
    return false
  }
  for (const item of itens) {
    if (!item.produto_id || Number(item.quantidade) <= 0 || Number(item.preco_unitario) <= 0) {
      toast.erro('Preencha todos os itens da compra.')
      return false
    }
  }
  return true
}

async function submeter() {
  if (!validar()) return

  enviando.value = true
  try {
    await registrarCompra({
      fornecedor: fornecedor.value.trim(),
      produtos: itens.map((i) => ({
        id: Number(i.produto_id),
        quantidade: Number(i.quantidade),
        preco_unitario: Number(i.preco_unitario),
      })),
    })
    toast.sucesso('Compra registrada', `Total: ${formatarMoeda(total.value)}`)
    // Estoque e custo médio mudaram — recarregar produtos.
    await produtos.carregar(true)
    emit('registrada')
  } catch (e) {
    if (isAxiosError(e) && e.response?.status === 422) {
      toast.erro('Verifique os dados informados.', e.response.data?.message)
    } else {
      toast.erro('Não foi possível registrar a compra.')
    }
  } finally {
    enviando.value = false
  }
}
</script>

<template>
  <form class="space-y-5" @submit.prevent="submeter">
    <Input
      v-model="fornecedor"
      label="Fornecedor"
      placeholder="Nome do fornecedor"
      :error="erroFornecedor"
      required
    />

    <div>
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-medium text-slate-700">Itens</h3>
        <button
          type="button"
          class="text-xs font-medium text-slate-600 hover:text-slate-900"
          @click="adicionarItem"
        >
          + Adicionar item
        </button>
      </div>

      <div class="space-y-2">
        <div
          v-for="(item, idx) in itens"
          :key="item._id"
          class="grid grid-cols-12 gap-2 items-start"
        >
          <div class="col-span-6">
            <Select
              v-model="item.produto_id"
              :opcoes="opcoesProdutos"
              placeholder="Selecione o produto"
            />
          </div>
          <div class="col-span-2">
            <Input
              v-model="item.quantidade"
              type="number"
              min="1"
              step="1"
              placeholder="Qtd"
            />
          </div>
          <div class="col-span-3">
            <Input
              v-model="item.preco_unitario"
              type="number"
              min="0.01"
              step="0.01"
              placeholder="Preço unit."
            />
          </div>
          <button
            type="button"
            class="col-span-1 mt-2 text-slate-400 hover:text-red-600 transition"
            :disabled="itens.length === 1"
            :class="{ 'opacity-30 cursor-not-allowed': itens.length === 1 }"
            aria-label="Remover item"
            @click="removerItem(idx)"
          >
            ✕
          </button>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between border-t border-slate-200 pt-4">
      <div class="text-sm">
        <span class="text-slate-500">Total da compra:</span>
        <span class="ml-2 text-lg font-semibold text-slate-900 tabular-nums">
          {{ formatarMoeda(total) }}
        </span>
      </div>
      <div class="flex gap-2">
        <Button variante="secondary" type="button" @click="emit('cancelar')">
          Cancelar
        </Button>
        <Button variante="primary" type="submit" :loading="enviando">
          Registrar compra
        </Button>
      </div>
    </div>
  </form>
</template>
