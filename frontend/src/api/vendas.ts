import { http } from '@/api/http'
import type { Venda } from '@/types/venda'

export interface ItemVendaPayload {
  id: number
  quantidade: number
  preco_unitario: number
}

export interface RegistrarVendaPayload {
  cliente: string
  produtos: ItemVendaPayload[]
}

export async function listarVendas(status?: 'concluida' | 'cancelada'): Promise<Venda[]> {
  const { data } = await http.get<{ data: Venda[] }>('/vendas', {
    params: status ? { status } : undefined,
  })
  return data.data
}

export async function registrarVenda(payload: RegistrarVendaPayload): Promise<Venda> {
  const { data } = await http.post<{ data: Venda }>('/vendas', payload)
  return data.data
}

export async function cancelarVenda(id: number): Promise<Venda> {
  const { data } = await http.post<{ data: Venda }>(`/vendas/${id}/cancelar`)
  return data.data
}
