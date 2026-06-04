import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as api from '@/api/produtos'
import type { Produto } from '@/types/produto'

export const useProdutosStore = defineStore('produtos', () => {
  const lista = ref<Produto[]>([])
  const carregando = ref(false)
  const carregado = ref(false)

  const porId = computed(() => {
    const mapa = new Map<number, Produto>()
    for (const p of lista.value) mapa.set(p.id, p)
    return mapa
  })

  async function carregar(forcar = false) {
    if (carregado.value && !forcar) return
    carregando.value = true
    try {
      lista.value = await api.listarProdutos()
      carregado.value = true
    } finally {
      carregando.value = false
    }
  }

  async function criar(payload: api.CriarProdutoPayload) {
    const novo = await api.criarProduto(payload)
    lista.value = [...lista.value, novo].sort((a, b) => a.nome.localeCompare(b.nome))
    return novo
  }

  return { lista, carregando, carregado, porId, carregar, criar }
})
