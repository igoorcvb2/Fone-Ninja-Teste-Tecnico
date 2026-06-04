import { ref } from 'vue'

export type ToastTipo = 'sucesso' | 'erro' | 'info'

export interface Toast {
  id: number
  tipo: ToastTipo
  titulo: string
  descricao?: string
}

const toasts = ref<Toast[]>([])
let proximoId = 1

function adicionar(tipo: ToastTipo, titulo: string, descricao?: string) {
  const id = proximoId++
  toasts.value.push({ id, tipo, titulo, descricao })
  setTimeout(() => remover(id), 4500)
}

function remover(id: number) {
  toasts.value = toasts.value.filter((t) => t.id !== id)
}

export function useToast() {
  return {
    toasts,
    sucesso: (titulo: string, descricao?: string) => adicionar('sucesso', titulo, descricao),
    erro: (titulo: string, descricao?: string) => adicionar('erro', titulo, descricao),
    info: (titulo: string, descricao?: string) => adicionar('info', titulo, descricao),
    remover,
  }
}
