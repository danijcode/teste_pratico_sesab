import { ref } from 'vue'
import axios from 'axios'

export interface Perfil {
  id: number
  nome: string
  usuarios_count?: number
}

export function usePerfis() {
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function listar() {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/perfis')
      return data as Perfil[]
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao listar perfis.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function buscar(id: number) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get(`/api/perfis/${id}`)
      return data as Perfil
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao buscar perfil.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function criar(nome: string) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.post('/api/perfis', { nome })
      return data as Perfil
    } catch (e: any) {
      error.value = extrairErros(e)
      throw e
    } finally {
      loading.value = false
    }
  }

  async function atualizar(id: number, nome: string) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.put(`/api/perfis/${id}`, { nome })
      return data as Perfil
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
      await axios.delete(`/api/perfis/${id}`)
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Erro ao excluir perfil.'
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
