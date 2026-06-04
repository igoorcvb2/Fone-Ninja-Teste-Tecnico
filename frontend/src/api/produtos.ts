import { http } from '@/api/http'
import type { Produto } from '@/types/produto'

export interface CriarProdutoPayload {
  nome: string
  preco_venda: number
}

export async function listarProdutos(): Promise<Produto[]> {
  const { data } = await http.get<{ data: Produto[] }>('/produtos')
  return data.data
}

export async function criarProduto(payload: CriarProdutoPayload): Promise<Produto> {
  const { data } = await http.post<{ data: Produto }>('/produtos', payload)
  return data.data
}
