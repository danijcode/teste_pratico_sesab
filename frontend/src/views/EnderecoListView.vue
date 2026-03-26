<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useEnderecos, type Endereco } from '../composables/useEnderecos'

const { loading, error, listar, criar, atualizar, excluir } = useEnderecos()

const enderecos = ref<Endereco[]>([])
const totalItens = ref(0)
const totalPaginas = ref(1)
const filtroLogradouro = ref('')
const filtroCep = ref('')
const page = ref(1)

// Dialog Novo/Editar
const dialog = ref(false)
const editando = ref<Endereco | null>(null)
const form = ref({ logradouro: '', cep: '' })
const erroForm = ref<string | null>(null)

// Dialog Excluir
const dialogExcluir = ref(false)
const enderecoParaExcluir = ref<Endereco | null>(null)

const headers = [
  { title: 'ID',         key: 'id',             width: '70px' },
  { title: 'Logradouro', key: 'logradouro' },
  { title: 'CEP',        key: 'cep',            width: '130px' },
  { title: 'Usuários',   key: 'usuarios_count', width: '110px', align: 'center' as const },
  { title: 'Ações',      key: 'acoes', sortable: false, width: '150px', align: 'center' as const },
]

async function buscar(p = 1) {
  page.value = p
  const res = await listar({ logradouro: filtroLogradouro.value, cep: filtroCep.value, page: p })
  enderecos.value = res.data
  totalItens.value = res.meta.total
  totalPaginas.value = res.meta.last_page
}

function limpar() {
  filtroLogradouro.value = ''
  filtroCep.value = ''
  buscar(1)
}

function formatarCep(e: Event) {
  const digits = (e.target as HTMLInputElement).value.replace(/\D/g, '').slice(0, 8)
  form.value.cep = digits.replace(/(\d{5})(\d)/, '$1-$2')
}

function abrirNovo() {
  editando.value = null
  form.value = { logradouro: '', cep: '' }
  erroForm.value = null
  dialog.value = true
}

function abrirEditar(e: Endereco) {
  editando.value = e
  form.value = { logradouro: e.logradouro, cep: e.cep }
  erroForm.value = null
  dialog.value = true
}

async function salvar() {
  erroForm.value = null
  try {
    if (editando.value) {
      await atualizar(editando.value.id, form.value)
    } else {
      await criar(form.value)
    }
    dialog.value = false
    buscar(page.value)
  } catch {
    erroForm.value = error.value
  }
}

function confirmarExcluir(e: Endereco) {
  enderecoParaExcluir.value = e
  dialogExcluir.value = true
}

async function deletar() {
  if (!enderecoParaExcluir.value) return
  try {
    await excluir(enderecoParaExcluir.value.id)
    dialogExcluir.value = false
    buscar(page.value)
  } catch {
    dialogExcluir.value = false
  }
}

onMounted(() => buscar())
</script>

<template>
  <v-container fluid class="py-6">

    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold text-grey-darken-3">Endereços</h1>
        <p class="text-body-2 text-medium-emphasis">Gerencie os endereços compartilhados entre usuários</p>
      </div>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="abrirNovo">Novo Endereço</v-btn>
    </div>

    <!-- Filtros -->
    <v-card class="mb-6" elevation="1" rounded="lg">
      <v-card-text>
        <v-row dense>
          <v-col cols="12" sm="6">
            <v-text-field v-model="filtroLogradouro" label="Logradouro" variant="outlined" density="compact" clearable hide-details />
          </v-col>
          <v-col cols="12" sm="3">
            <v-text-field v-model="filtroCep" label="CEP" variant="outlined" density="compact" clearable hide-details />
          </v-col>
          <v-col cols="auto" class="d-flex align-center gap-2">
            <v-btn color="primary" prepend-icon="mdi-magnify" @click="buscar(1)">Filtrar</v-btn>
            <v-btn variant="tonal" prepend-icon="mdi-eraser" @click="limpar">Limpar</v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-alert v-if="error && !dialog" type="error" variant="tonal" class="mb-4" closable>{{ error }}</v-alert>

    <v-card elevation="1" rounded="lg">
      <v-data-table :headers="headers" :items="enderecos" :loading="loading" hide-default-footer density="comfortable">
        <template #item.usuarios_count="{ item }">
          <v-chip size="small" color="primary" variant="tonal">{{ item.usuarios_count ?? 0 }}</v-chip>
        </template>
        <template #item.acoes="{ item }">
          <v-btn icon="mdi-pencil" size="small" variant="text" color="warning" @click="abrirEditar(item)" />
          <v-btn icon="mdi-delete" size="small" variant="text" color="error" @click="confirmarExcluir(item)" />
        </template>
        <template #bottom />
      </v-data-table>

      <v-divider />
      <div class="d-flex align-center justify-space-between px-4 py-2">
        <span class="text-caption text-medium-emphasis">{{ totalItens }} registro(s)</span>
        <v-pagination v-model="page" :length="totalPaginas" size="small" @update:model-value="buscar($event)" />
      </div>
    </v-card>

    <!-- Dialog Novo/Editar -->
    <v-dialog v-model="dialog" max-width="480" persistent>
      <v-card rounded="lg">
        <v-card-title class="pt-5 px-6">{{ editando ? 'Editar Endereço' : 'Novo Endereço' }}</v-card-title>
        <v-card-text class="px-6">
          <v-alert v-if="erroForm" type="error" variant="tonal" class="mb-3" density="compact">{{ erroForm }}</v-alert>
          <v-text-field v-model="form.logradouro" label="Logradouro *" variant="outlined" density="comfortable" autofocus class="mb-2" />
          <v-text-field
            v-model="form.cep"
            label="CEP *"
            variant="outlined"
            density="comfortable"
            placeholder="00000-000"
            @input="formatarCep"
          />
        </v-card-text>
        <v-card-actions class="px-6 pb-5">
          <v-spacer />
          <v-btn variant="tonal" @click="dialog = false">Cancelar</v-btn>
          <v-btn color="primary" variant="flat" :loading="loading" @click="salvar">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog Confirmar Exclusão -->
    <v-dialog v-model="dialogExcluir" max-width="420">
      <v-card rounded="lg">
        <v-card-title class="d-flex align-center pt-5 px-6">
          <v-icon color="error" icon="mdi-alert-circle" class="mr-2" />
          Confirmar exclusão
        </v-card-title>
        <v-card-text class="px-6">
          Deseja excluir o endereço <strong>{{ enderecoParaExcluir?.logradouro }}</strong>?
          <span v-if="(enderecoParaExcluir?.usuarios_count ?? 0) > 0" class="text-error d-block mt-1">
            Este endereço possui usuários vinculados e não pode ser excluído.
          </span>
        </v-card-text>
        <v-card-actions class="px-6 pb-5">
          <v-spacer />
          <v-btn variant="tonal" @click="dialogExcluir = false">Cancelar</v-btn>
          <v-btn
            color="error"
            variant="flat"
            :loading="loading"
            :disabled="(enderecoParaExcluir?.usuarios_count ?? 0) > 0"
            @click="deletar"
          >
            Excluir
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>
