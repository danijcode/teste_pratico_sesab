<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUsuarios } from '../composables/useUsuarios'
import { usePerfis, type Perfil } from '../composables/usePerfis'
import { useEnderecos, type Endereco } from '../composables/useEnderecos'

const router = useRouter()
const route = useRoute()
const { loading, error, buscar, criar, atualizar } = useUsuarios()
const { listar: listarPerfis } = usePerfis()
const { listarTodos: listarEnderecos } = useEnderecos()

const isEdicao = computed(() => !!route.params.id)
const titulo = computed(() => isEdicao.value ? 'Editar Usuário' : 'Novo Usuário')

const perfis = ref<Perfil[]>([])
const todosEnderecos = ref<Endereco[]>([])
const enderecoSelecionadoId = ref<number | null>(null)

const form = ref({
  nome: '',
  email: '',
  cpf: '',
  perfil_id: null as number | null,
  endereco_ids: [] as number[],
})

const enderecosSelecionados = ref<Endereco[]>([])

const enderecoHeaders = [
  { title: 'Logradouro', key: 'logradouro' },
  { title: 'CEP',        key: 'cep', width: '130px' },
  { title: 'Ação',       key: 'acao', sortable: false, width: '80px', align: 'center' as const },
]

function adicionarEndereco() {
  const endereco = todosEnderecos.value.find(e => e.id === enderecoSelecionadoId.value)
  if (!endereco) return
  const jaAdicionado = enderecosSelecionados.value.some(e => e.id === endereco.id)
  if (!jaAdicionado) {
    enderecosSelecionados.value.push({ ...endereco })
    form.value.endereco_ids.push(endereco.id!)
  }
  enderecoSelecionadoId.value = null
}

function removerEndereco(id: number) {
  enderecosSelecionados.value = enderecosSelecionados.value.filter(e => e.id !== id)
  form.value.endereco_ids = form.value.endereco_ids.filter(i => i !== id)
}

function formatarCpf(e: Event) {
  const digits = (e.target as HTMLInputElement).value.replace(/\D/g, '').slice(0, 11)
  form.value.cpf = digits
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d{1,2})$/, '$1-$2')
}

async function salvar() {
  if (!form.value.perfil_id) return
  try {
    if (isEdicao.value) {
      await atualizar(Number(route.params.id), { ...form.value, perfil_id: form.value.perfil_id! })
    } else {
      await criar({ ...form.value, perfil_id: form.value.perfil_id! })
    }
    router.push({ name: 'usuarios' })
  } catch {
    // erro já capturado no composable
  }
}

onMounted(async () => {
  ;[perfis.value, todosEnderecos.value] = await Promise.all([listarPerfis(), listarEnderecos()])

  if (isEdicao.value) {
    const u = await buscar(Number(route.params.id))
    form.value = {
      nome: u.nome,
      email: u.email,
      cpf: u.cpf,
      perfil_id: u.perfil_id,
      endereco_ids: u.enderecos.map(e => e.id!),
    }
    enderecosSelecionados.value = u.enderecos.map(e => ({
      id: e.id!,
      logradouro: e.logradouro,
      cep: e.cep,
    }))
  }
})
</script>

<template>
  <v-container fluid class="py-6" style="max-width: 860px">

    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" class="mr-2" @click="router.back()" />
      <div>
        <h1 class="text-h5 font-weight-bold text-grey-darken-3">{{ titulo }}</h1>
        <p class="text-body-2 text-medium-emphasis">Preencha os campos obrigatórios (*)</p>
      </div>
    </div>

    <v-alert v-if="error" type="error" variant="tonal" class="mb-4" closable>{{ error }}</v-alert>

    <v-form @submit.prevent="salvar">

      <!-- Dados do usuário -->
      <v-card class="mb-4" elevation="1" rounded="lg">
        <v-card-title class="text-subtitle-1 font-weight-medium px-5 pt-4">Dados do Usuário</v-card-title>
        <v-divider />
        <v-card-text class="px-5 pt-4">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.nome"
                label="Nome *"
                variant="outlined"
                density="comfortable"
                required
                :disabled="loading"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.cpf"
                label="CPF *"
                variant="outlined"
                density="comfortable"
                placeholder="000.000.000-00"
                required
                :disabled="loading"
                @input="formatarCpf"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.email"
                label="E-mail *"
                type="email"
                variant="outlined"
                density="comfortable"
                required
                :disabled="loading"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                v-model="form.perfil_id"
                :items="perfis"
                item-title="nome"
                item-value="id"
                label="Perfil *"
                variant="outlined"
                density="comfortable"
                required
                :disabled="loading"
              />
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Endereços -->
      <v-card class="mb-6" elevation="1" rounded="lg">
        <v-card-title class="text-subtitle-1 font-weight-medium px-5 pt-4">
          Endereços
          <v-tooltip text="Selecione endereços cadastrados previamente no menu Endereços" location="right">
            <template #activator="{ props }">
              <v-icon v-bind="props" icon="mdi-information-outline" size="16" class="ml-1 text-medium-emphasis" />
            </template>
          </v-tooltip>
        </v-card-title>
        <v-divider />
        <v-card-text class="px-5 pt-4">

          <v-row dense align="end">
            <v-col cols="12" sm="9">
              <v-autocomplete
                v-model="enderecoSelecionadoId"
                :items="todosEnderecos"
                item-title="logradouro"
                item-value="id"
                label="Selecionar endereço existente"
                variant="outlined"
                density="compact"
                hide-details
                clearable
                :disabled="loading"
              >
                <template #item="{ props, item }">
                  <v-list-item
                    v-bind="props"
                    :subtitle="(item as any)?.raw?.cep ? `CEP: ${(item as any).raw.cep}` : ''"
                  />
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="12" sm="3">
              <v-btn
                color="primary"
                variant="tonal"
                prepend-icon="mdi-plus"
                block
                :disabled="!enderecoSelecionadoId"
                @click="adicionarEndereco"
              >
                Adicionar
              </v-btn>
            </v-col>
          </v-row>

          <v-data-table
            v-if="enderecosSelecionados.length"
            :headers="enderecoHeaders"
            :items="enderecosSelecionados"
            hide-default-footer
            density="compact"
            class="mt-3 rounded-lg"
          >
            <template #item.acao="{ item }">
              <v-btn icon="mdi-delete" size="x-small" variant="text" color="error" @click="removerEndereco(item.id!)" />
            </template>
            <template #bottom />
          </v-data-table>

          <v-alert v-else type="info" variant="tonal" density="compact" class="mt-3">
            Nenhum endereço vinculado. Selecione um endereço da lista acima.
          </v-alert>

        </v-card-text>
      </v-card>

      <div class="d-flex gap-3 justify-end">
        <v-btn variant="tonal" @click="router.back()">Cancelar</v-btn>
        <v-btn type="submit" color="primary" :loading="loading" prepend-icon="mdi-content-save">Salvar</v-btn>
      </div>

    </v-form>
  </v-container>
</template>
