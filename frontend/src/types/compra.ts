export interface ItemCompra {
  id: number
  produto_id: number
  produto?: { id: number; nome: string }
  quantidade: number
  preco_unitario: number
  subtotal: number
}

export interface Compra {
  id: number
  fornecedor: string
  total: number
  created_at: string
  itens: ItemCompra[]
}
