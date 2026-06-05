<script setup lang="ts">
import { computed, reactive, ref, onMounted } from 'vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import Button from '@/components/ui/Button.vue'
import { registrarVenda } from '@/api/vendas'
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

const cliente = ref('')
const itens = reactive<ItemLinha[]>([novaLinha()])
const enviando = ref(false)
const erroCliente = ref<string | undefined>()

onMounted(() => produtos.carregar())

const opcoesProdutos = computed(() =>
  produtos.lista.map((p) => ({
    value: p.id,
    label: `${p.nome} — ${p.estoque} em estoque`,
    disabled: p.estoque === 0,
  })),
)

const total = computed(() =>
  itens.reduce((acc, it) => {
    const q = Number(it.quantidade) || 0
    const p = Number(it.preco_unitario) || 0
    return acc + q * p
  }, 0),
)

const lucroEstimado = computed(() =>
  itens.reduce((acc, it) => {
    const produto = produtos.porId.get(Number(it.produto_id))
    if (!produto) return acc
    const q = Number(it.quantidade) || 0
    const p = Number(it.preco_unitario) || 0
    return acc + (p - produto.custo_medio) * q
  }, 0),
)

function infoProduto(produtoId: string) {
  const produto = produtos.porId.get(Number(produtoId))
  if (!produto) return null
  return {
    estoque: produto.estoque,
    custo: produto.custo_medio,
  }
}

function lucroLinha(it: ItemLinha): number | null {
  const produto = produtos.porId.get(Number(it.produto_id))
  if (!produto) return null
  const q = Number(it.quantidade) || 0
  const p = Number(it.preco_unitario) || 0
  if (q <= 0 || p <= 0) return null
  return (p - produto.custo_medio) * q
}

function excedeEstoque(it: ItemLinha): boolean {
  const produto = produtos.porId.get(Number(it.produto_id))
  if (!produto) return false
  const q = Number(it.quantidade) || 0
  return q > produto.estoque
}

const algumaLinhaInvalida = computed(() =>
  itens.some((it) => excedeEstoque(it)),
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
  erroCliente.value = undefined
  if (cliente.value.trim().length < 2) {
    erroCliente.value = 'Informe o nome do cliente.'
    return false
  }
  for (const item of itens) {
    if (!item.produto_id || Number(item.quantidade) <= 0 || Number(item.preco_unitario) <= 0) {
      toast.erro('Preencha todos os itens da venda.')
      return false
    }
    const produto = produtos.porId.get(Number(item.produto_id))
    if (produto && Number(item.quantidade) > produto.estoque) {
      toast.erro(
        'Estoque insuficiente',
        `${produto.nome}: disponível ${produto.estoque}, solicitado ${item.quantidade}`,
      )
      return false
    }
  }
  return true
}

async function submeter() {
  if (!validar()) return

  enviando.value = true
  try {
    const venda = await registrarVenda({
      cliente: cliente.value.trim(),
      produtos: itens.map((i) => ({
        id: Number(i.produto_id),
        quantidade: Number(i.quantidade),
        preco_unitario: Number(i.preco_unitario),
      })),
    })
    toast.sucesso(
      'Venda registrada',
      `Total: ${formatarMoeda(venda.total)} · Lucro: ${formatarMoeda(venda.lucro)}`,
    )
    await produtos.carregar(true)
    emit('registrada')
  } catch (e) {
    if (isAxiosError(e) && e.response?.status === 422) {
      const dados = e.response.data
      if (dados?.erro === 'estoque_insuficiente') {
        toast.erro(
          dados.message ?? 'Estoque insuficiente',
          `Disponível: ${dados.disponivel} · Solicitado: ${dados.solicitado}`,
        )
      } else {
        toast.erro('Verifique os dados informados.', dados?.message)
      }
    } else {
      toast.erro('Não foi possível registrar a venda.')
    }
  } finally {
    enviando.value = false
  }
}
</script>

<template>
  <form class="space-y-5" @submit.prevent="submeter">
    <Input
      v-model="cliente"
      label="Cliente"
      placeholder="Nome do cliente"
      :error="erroCliente"
      required
    />

    <div>
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-medium text-slate-700">Itens da venda</h3>
        <button
          type="button"
          class="text-xs font-medium text-slate-600 hover:text-slate-900"
          @click="adicionarItem"
        >
          + Adicionar item
        </button>
      </div>

      <div class="space-y-3">
        <div
          v-for="(item, idx) in itens"
          :key="item._id"
          :class="[
            'border rounded-md p-3',
            excedeEstoque(item)
              ? 'border-red-300 bg-red-50/40'
              : 'border-slate-200 bg-slate-50/50',
          ]"
        >
          <div class="grid grid-cols-12 gap-2 items-start">
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

          <p
            v-if="infoProduto(item.produto_id)"
            class="mt-2 text-xs tabular-nums flex flex-wrap gap-x-4"
            :class="excedeEstoque(item) ? 'text-red-600' : 'text-slate-500'"
          >
            <span>
              Estoque:
              <span
                class="font-medium"
                :class="excedeEstoque(item) ? 'text-red-700' : 'text-slate-700'"
              >
                {{ infoProduto(item.produto_id)?.estoque }}
              </span>
            </span>
            <span>
              Custo médio:
              <span class="font-medium text-slate-700">
                {{ formatarMoeda(infoProduto(item.produto_id)?.custo ?? 0) }}
              </span>
            </span>
            <span v-if="lucroLinha(item) !== null && !excedeEstoque(item)">
              Lucro estimado:
              <span
                class="font-medium"
                :class="(lucroLinha(item) ?? 0) >= 0 ? 'text-emerald-700' : 'text-red-600'"
              >
                {{ formatarMoeda(lucroLinha(item) ?? 0) }}
              </span>
            </span>
            <span v-if="excedeEstoque(item)" class="font-medium">
              Quantidade excede o estoque disponível.
            </span>
          </p>
        </div>
      </div>
    </div>

    <div class="border-t border-slate-200 pt-4">
      <div class="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
          <p class="text-slate-500">Total</p>
          <p class="text-lg font-semibold text-slate-900 tabular-nums">
            {{ formatarMoeda(total) }}
          </p>
        </div>
        <div>
          <p class="text-slate-500">Lucro estimado</p>
          <p
            v-if="!algumaLinhaInvalida"
            class="text-lg font-semibold tabular-nums"
            :class="lucroEstimado >= 0 ? 'text-emerald-700' : 'text-red-600'"
          >
            {{ formatarMoeda(lucroEstimado) }}
          </p>
          <p v-else class="text-sm text-red-600">
            Ajuste os itens com estoque insuficiente.
          </p>
        </div>
      </div>
      <div class="flex justify-end gap-2">
        <Button variante="secondary" type="button" @click="emit('cancelar')">
          Cancelar
        </Button>
        <Button
          variante="primary"
          type="submit"
          :loading="enviando"
          :disabled="algumaLinhaInvalida"
        >
          Registrar venda
        </Button>
      </div>
    </div>
  </form>
</template>
