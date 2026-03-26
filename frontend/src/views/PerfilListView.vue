<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { usePerfis, type Perfil } from '../composables/usePerfis'

const { loading, error, listar, criar, atualizar, excluir } = usePerfis()

const perfis = ref<Perfil[]>([])

// Dialog Novo/Editar
const dialog = ref(false)
const editando = ref<Perfil | null>(null)
const nomeForm = ref('')
const erroForm = ref<string | null>(null)

// Dialog Excluir
const dialogExcluir = ref(false)
const perfilParaExcluir = ref<Perfil | null>(null)

const headers = [
  { title: 'ID',       key: 'id',             width: '70px' },
  { title: 'Nome',     key: 'nome' },
  { title: 'Usuários', key: 'usuarios_count',  width: '120px', align: 'center' as const },
  { title: 'Ações',    key: 'acoes', sortable: false, width: '150px', align: 'center' as const },
]

async function carregar() {
  perfis.value = await listar()
}

function abrirNovo() {
  editando.value = null
  nomeForm.value = ''
  erroForm.value = null
  dialog.value = true
}

function abrirEditar(p: Perfil) {
  editando.value = p
  nomeForm.value = p.nome
  erroForm.value = null
  dialog.value = true
}

async function salvar() {
  erroForm.value = null
  try {
    if (editando.value) {
      await atualizar(editando.value.id, nomeForm.value)
    } else {
      await criar(nomeForm.value)
    }
    dialog.value = false
    carregar()
  } catch {
    erroForm.value = error.value
  }
}

function confirmarExcluir(p: Perfil) {
  perfilParaExcluir.value = p
  dialogExcluir.value = true
}

async function deletar() {
  if (!perfilParaExcluir.value) return
  try {
    await excluir(perfilParaExcluir.value.id)
    dialogExcluir.value = false
    carregar()
  } catch {
    dialogExcluir.value = false
  }
}

onMounted(carregar)
</script>

<template>
  <v-container fluid class="py-6">

    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold text-grey-darken-3">Perfis</h1>
        <p class="text-body-2 text-medium-emphasis">Gerencie os perfis de acesso do sistema</p>
      </div>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="abrirNovo">Novo Perfil</v-btn>
    </div>

    <v-alert v-if="error && !dialog" type="error" variant="tonal" class="mb-4" closable>{{ error }}</v-alert>

    <v-card elevation="1" rounded="lg">
      <v-data-table
        :headers="headers"
        :items="perfis"
        :loading="loading"
        hide-default-footer
        density="comfortable"
      >
        <template #item.usuarios_count="{ item }">
          <v-chip size="small" color="primary" variant="tonal">{{ item.usuarios_count ?? 0 }}</v-chip>
        </template>

        <template #item.acoes="{ item }">
          <v-btn icon="mdi-pencil" size="small" variant="text" color="warning" title="Editar" @click="abrirEditar(item)" />
          <v-btn icon="mdi-delete" size="small" variant="text" color="error" title="Excluir" @click="confirmarExcluir(item)" />
        </template>

        <template #bottom />
      </v-data-table>
    </v-card>

    <!-- Dialog Novo/Editar -->
    <v-dialog v-model="dialog" max-width="440" persistent>
      <v-card rounded="lg">
        <v-card-title class="pt-5 px-6">
          {{ editando ? 'Editar Perfil' : 'Novo Perfil' }}
        </v-card-title>
        <v-card-text class="px-6">
          <v-alert v-if="erroForm" type="error" variant="tonal" class="mb-3" density="compact">{{ erroForm }}</v-alert>
          <v-text-field
            v-model="nomeForm"
            label="Nome do perfil *"
            variant="outlined"
            density="comfortable"
            autofocus
            @keyup.enter="salvar"
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
          Deseja excluir o perfil <strong>{{ perfilParaExcluir?.nome }}</strong>?
          <span v-if="(perfilParaExcluir?.usuarios_count ?? 0) > 0" class="text-error">
            Este perfil possui usuários vinculados e não pode ser excluído.
          </span>
        </v-card-text>
        <v-card-actions class="px-6 pb-5">
          <v-spacer />
          <v-btn variant="tonal" @click="dialogExcluir = false">Cancelar</v-btn>
          <v-btn
            color="error"
            variant="flat"
            :loading="loading"
            :disabled="(perfilParaExcluir?.usuarios_count ?? 0) > 0"
            @click="deletar"
          >
            Excluir
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>
