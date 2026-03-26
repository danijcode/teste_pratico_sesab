import { ref } from 'vue'
import axios from 'axios'

export interface Endereco {
  id: number
  logradouro: string
  cep: string
  usuarios_count?: number
}

export function useEnderecos() {
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function listar(params: { logradouro?: string; cep?: string; page?: number } = {}) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/enderecos', { params })
      return data as { data: Endereco[]; meta: { current_page: number; last_page: number; total: number; per_page: number } }
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao listar endereços.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function listarTodos() {
    const { data } = await axios.get('/api/enderecos', { params: { all: true } })
    return data as Endereco[]
  }

  async function buscar(id: number) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(`/api/enderecos/${id}`)
      return data as Endereco
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao buscar endereço.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function criar(payload: { logradouro: string; cep: string }) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.post('/api/enderecos', payload)
      return data as Endereco
    } catch (e: any) {
      error.value = extrairErros(e)
      throw e
    } finally {
      loading.value = false
    }
  }

  async function atualizar(id: number, payload: { logradouro: string; cep: string }) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.put(`/api/enderecos/${id}`, payload)
      return data as Endereco
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
      await axios.delete(`/api/enderecos/${id}`)
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao excluir endereço.'
      throw e
    } finally {
      loading.value = false
    }
  }

  return { loading, error, listar, listarTodos, buscar, criar, atualizar, excluir }
}

function extrairErros(e: any): string {
  const errors = e.response?.data?.errors
  if (errors) return Object.values(errors).flat().join(' ')
  return e.response?.data?.message ?? 'Erro ao processar requisição.'
}
