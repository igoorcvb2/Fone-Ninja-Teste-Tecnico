import { http } from '@/api/http'
import type { Compra } from '@/types/compra'

export interface ItemCompraPayload {
  id: number
  quantidade: number
  preco_unitario: number
}

export interface RegistrarCompraPayload {
  fornecedor: string
  produtos: ItemCompraPayload[]
}

export async function listarCompras(): Promise<Compra[]> {
  const { data } = await http.get<{ data: Compra[] }>('/compras')
  return data.data
}

export async function registrarCompra(payload: RegistrarCompraPayload): Promise<Compra> {
  const { data } = await http.post<{ data: Compra }>('/compras', payload)
  return data.data
}
