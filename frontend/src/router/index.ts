import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  { path: '/', redirect: '/produtos' },
  { path: '/produtos', name: 'produtos', component: () => import('@/pages/ProdutosPage.vue') },
  { path: '/compras', name: 'compras', component: () => import('@/pages/ComprasPage.vue') },
  { path: '/vendas', name: 'vendas', component: () => import('@/pages/VendasPage.vue') },
  { path: '/historico', name: 'historico', component: () => import('@/pages/HistoricoPage.vue') },
]

export const router = createRouter({
  history: createWebHistory(),
  routes,
})
