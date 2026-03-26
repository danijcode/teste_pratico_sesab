import { reactive } from 'vue'
import axios from 'axios'

interface User {
  id: number
  name: string
  email: string
  role: string
}

interface AuthState {
  token: string | null
  user: User | null
  loading: boolean
  error: string | null
}

const STORAGE_KEY = 'sesab_auth'

function loadFromStorage(): Pick<AuthState, 'token' | 'user'> {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (raw) return JSON.parse(raw)
  } catch {}
  return { token: null, user: null }
}

function saveToStorage(token: string | null, user: User | null) {
  if (token && user) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify({ token, user }))
  } else {
    localStorage.removeItem(STORAGE_KEY)
  }
}

function setAuthHeader(token: string | null) {
  if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  } else {
    delete axios.defaults.headers.common['Authorization']
  }
}

// Restaura sessão salva ao carregar o módulo
const saved = loadFromStorage()

const state = reactive<AuthState>({
  token: saved.token,
  user: saved.user,
  loading: false,
  error: null,
})

// Garante que o header do axios já esteja configurado na inicialização
setAuthHeader(saved.token)

// Interceptor: qualquer 401 limpa a sessão e redireciona para login
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    const isLoginRequest = error.config?.url?.includes('/auth/login')
    if (error.response?.status === 401 && state.token && !isLoginRequest) {
      state.token = null
      state.user = null
      setAuthHeader(null)
      saveToStorage(null, null)
      // Redireciona para login sem usar o router (evita dependência circular)
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

async function login(email: string, password: string): Promise<boolean> {
  state.loading = true
  state.error = null
  try {
    const { data } = await axios.post('/api/auth/login', { email, password })
    state.token = data.token
    state.user = data.user
    setAuthHeader(data.token)
    saveToStorage(data.token, data.user)
    return true
  } catch (err: any) {
    state.error = err.response?.data?.message ?? 'Erro ao realizar login.'
    return false
  } finally {
    state.loading = false
  }
}

async function logout(): Promise<void> {
  state.loading = true
  try {
    await axios.post('/api/auth/logout')
  } finally {
    state.token = null
    state.user = null
    state.loading = false
    setAuthHeader(null)
    saveToStorage(null, null)
  }
}

function isAuthenticated(): boolean {
  return !!state.token
}

export const useAuth = () => ({ state, login, logout, isAuthenticated })
