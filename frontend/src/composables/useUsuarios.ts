import { ref } from 'vue'
import axios from 'axios'

export interface Perfil {
  id: number
  nome: string
}

export interface Endereco {
  id: number
  logradouro: string
  cep: string
}

export interface Usuario {
  id: number
  nome: string
  email: string
  cpf: string
  perfil_id: number
  perfil: Perfil
  enderecos: Endereco[]
  created_at: string
}

export interface UsuarioPayload {
  nome: string
  email: string
  cpf: string
  perfil_id: number
  endereco_ids: number[]
}

export interface UsuarioFiltros {
  nome?: string
  cpf?: string
  data_inicio?: string
  data_fim?: string
  page?: number
}

export interface PaginaMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export function useUsuarios() {
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function listar(filtros: UsuarioFiltros = {}) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/usuarios', { params: filtros })
      return data as { data: Usuario[]; meta: PaginaMeta }
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao listar usuários.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function buscar(id: number) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(`/api/usuarios/${id}`)
      return data as Usuario
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao buscar usuário.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function criar(payload: UsuarioPayload) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.post('/api/usuarios', payload)
      return data as Usuario
    } catch (e: any) {
      error.value = extrairErros(e)
      throw e
    } finally {
      loading.value = false
    }
  }

  async function atualizar(id: number, payload: UsuarioPayload) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.put(`/api/usuarios/${id}`, payload)
      return data as Usuario
    } catch (e: any) {
      error.value = extrairErros(e)
      throw e
    } finally {
      loading.value = false
    }
  }

  async function excluir(id: number) {
    loading.value = true
    error.value = null
    try {
      await axios.delete(`/api/usuarios/${id}`)
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao excluir usuário.'
      throw e
    } finally {
      loading.value = false
    }
  }

  return { loading, error, listar, buscar, criar, atualizar, excluir }
}

function extrairErros(e: any): string {
  const errors = e.response?.data?.errors
  if (errors) return Object.values(errors).flat().join(' ')
  return e.response?.data?.message ?? 'Erro ao processar requisição.'
}
