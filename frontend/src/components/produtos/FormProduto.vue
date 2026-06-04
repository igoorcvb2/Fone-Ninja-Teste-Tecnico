<script setup lang="ts">
import { reactive, ref } from 'vue'
import Input from '@/components/ui/Input.vue'
import Button from '@/components/ui/Button.vue'
import { useProdutosStore } from '@/stores/produtos'
import { useToast } from '@/composables/useToast'
import { isAxiosError } from 'axios'

const emit = defineEmits<{
  (e: 'criado'): void
  (e: 'cancelar'): void
}>()

const produtos = useProdutosStore()
const toast = useToast()

const form = reactive({
  nome: '',
  preco_venda: '' as string,
})

const erros = reactive<{ nome?: string; preco_venda?: string }>({})
const enviando = ref(false)

function validar(): boolean {
  erros.nome = undefined
  erros.preco_venda = undefined

  if (form.nome.trim().length < 3) {
    erros.nome = 'Mínimo 3 caracteres.'
  }
  const preco = Number(form.preco_venda)
  if (!Number.isFinite(preco) || preco <= 0) {
    erros.preco_venda = 'Informe um preço maior que zero.'
  }

  return !erros.nome && !erros.preco_venda
}

async function submeter() {
  if (!validar()) return

  enviando.value = true
  try {
    await produtos.criar({
      nome: form.nome.trim(),
      preco_venda: Number(form.preco_venda),
    })
    toast.sucesso('Produto cadastrado')
    emit('criado')
  } catch (e) {
    if (isAxiosError(e) && e.response?.status === 422) {
      const validation = e.response.data?.errors ?? {}
      if (validation.nome) erros.nome = validation.nome[0]
      if (validation.preco_venda) erros.preco_venda = validation.preco_venda[0]
    } else {
      toast.erro('Não foi possível cadastrar o produto.')
    }
  } finally {
    enviando.value = false
  }
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="submeter">
    <Input
      v-model="form.nome"
      label="Nome do produto"
      placeholder="Ex.: Camiseta básica"
      :error="erros.nome"
      required
    />

    <Input
      v-model="form.preco_venda"
      type="number"
      step="0.01"
      min="0.01"
      label="Preço de venda sugerido (R$)"
      placeholder="0,00"
      :error="erros.preco_venda"
      required
    />

    <div class="flex justify-end gap-2 pt-2">
      <Button variante="secondary" type="button" @click="emit('cancelar')">
        Cancelar
      </Button>
      <Button variante="primary" type="submit" :loading="enviando">
        Cadastrar
      </Button>
    </div>
  </form>
</template>
