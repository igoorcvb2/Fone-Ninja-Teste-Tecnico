export type StatusVenda = 'CONCLUIDA' | 'CANCELADA'

export interface ItemVenda {
  id: number
  produto_id: number
  produto?: { id: number; nome: string }
  quantidade: number
  preco_unitario: number
  custo_unitario: number
  subtotal: number
  lucro_item: number
}

export interface Venda {
  id: number
  cliente: string
  total: number
  lucro: number
  status: StatusVenda
  status_label: string
  cancelada_em: string | null
  created_at: string
  itens: ItemVenda[]
}
