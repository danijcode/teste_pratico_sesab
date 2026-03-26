import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '../stores/auth'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
    meta: { public: true },
  },
  {
    path: '/',
    component: () => import('../views/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', redirect: { name: 'usuarios' } },

      // Usuários
      { path: 'usuarios',             name: 'usuarios',        component: () => import('../views/UsuarioListView.vue') },
      { path: 'usuarios/novo',        name: 'usuario-novo',    component: () => import('../views/UsuarioFormView.vue') },
      { path: 'usuarios/:id/editar',  name: 'usuario-editar',  component: () => import('../views/UsuarioFormView.vue') },
      { path: 'usuarios/:id',         name: 'usuario-detalhe', component: () => import('../views/UsuarioDetalheView.vue') },

      // Perfis
      { path: 'perfis', name: 'perfis', component: () => import('../views/PerfilListView.vue') },

      // Endereços
      { path: 'enderecos', name: 'enderecos', component: () => import('../views/EnderecoListView.vue') },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const { isAuthenticated } = useAuth()

  if (to.meta.requiresAuth && !isAuthenticated()) {
    return { name: 'login' }
  }

  if (to.meta.public && isAuthenticated()) {
    return { name: 'usuarios' }
  }
})

export default router
