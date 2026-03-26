<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUsuarios, type Usuario, type UsuarioFiltros } from '../composables/useUsuarios'

const router = useRouter()
const { loading, error, listar, excluir } = useUsuarios()

const usuarios = ref<Usuario[]>([])
const totalItens = ref(0)
const totalPaginas = ref(1)

const filtros = ref<UsuarioFiltros>({ nome: '', cpf: '', data_inicio: '', data_fim: '', page: 1 })

const dialogExcluir = ref(false)
const usuarioParaExcluir = ref<Usuario | null>(null)

const headers = [
  { title: 'ID',             key: 'id',         width: '60px' },
  { title: 'Data Cadastro',  key: 'created_at', width: '130px' },
  { title: 'Nome',           key: 'nome' },
  { title: 'CPF',            key: 'cpf',        width: '140px' },
  { title: 'E-mail',         key: 'email' },
  { title: 'Perfil',         key: 'perfil.nome', width: '100px' },
  { title: 'Ações',          key: 'acoes',      sortable: false, width: '200px', align: 'center' as const },
]

async function buscar(page = 1) {
  filtros.value.page = page
  const res = await listar(filtros.value)
  usuarios.value = res.data
  totalItens.value = res.meta.total
  totalPaginas.value = res.meta.last_page
}

function limpar() {
  filtros.value = { nome: '', cpf: '', data_inicio: '', data_fim: '', page: 1 }
  buscar()
}

function confirmarExcluir(u: Usuario) {
  usuarioParaExcluir.value = u
  dialogExcluir.value = true
}

async function deletar() {
  if (!usuarioParaExcluir.value) return
  await excluir(usuarioParaExcluir.value.id)
  dialogExcluir.value = false
  buscar(filtros.value.page)
}

onMounted(() => buscar())
</script>

<template>
  <v-container fluid class="py-6">

    <!-- Cabeçalho -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <h1 class="text-h5 font-weight-bold text-grey-darken-3">Usuários</h1>
        <p class="text-body-2 text-medium-emphasis">Gerenciamento de usuários do sistema</p>
      </div>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="router.push({ name: 'usuario-novo' })">
        Novo
      </v-btn>
    </div>

    <!-- Filtros -->
    <v-card class="mb-6" elevation="1" rounded="lg">
      <v-card-text>
        <v-row dense>
          <v-col cols="12" sm="6" md="3">
            <v-text-field
              v-model="filtros.nome"
              label="Nome"
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-text-field
              v-model="filtros.cpf"
              label="CPF"
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-text-field
              v-model="filtros.data_inicio"
              label="Início"
              type="date"
              variant="outlined"
              density="compact"
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-text-field
              v-model="filtros.data_fim"
              label="Fim"
              type="date"
              variant="outlined"
              density="compact"
              hide-details
            />
          </v-col>
        </v-row>
        <v-row dense class="mt-2">
          <v-col cols="auto">
            <v-btn color="primary" prepend-icon="mdi-magnify" @click="buscar(1)">Filtrar</v-btn>
          </v-col>
          <v-col cols="auto">
            <v-btn variant="tonal" prepend-icon="mdi-eraser" @click="limpar">Limpar</v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Alerta de erro -->
    <v-alert v-if="error" type="error" variant="tonal" class="mb-4" closable>{{ error }}</v-alert>

    <!-- Tabela -->
    <v-card elevation="1" rounded="lg">
      <v-data-table
        :headers="headers"
        :items="usuarios"
        :loading="loading"
        hide-default-footer
        density="comfortable"
      >
        <template #item.perfil.nome="{ item }">
          <v-chip :color="item.perfil?.nome === 'ADMIN' ? 'error' : 'primary'" size="small" label>
            {{ item.perfil?.nome }}
          </v-chip>
        </template>

        <template #item.acoes="{ item }">
          <v-btn
            icon="mdi-eye"
            size="small"
            variant="text"
            color="info"
            title="Detalhar"
            @click="router.push({ name: 'usuario-detalhe', params: { id: item.id } })"
          />
          <v-btn
            icon="mdi-pencil"
            size="small"
            variant="text"
            color="warning"
            title="Editar"
            @click="router.push({ name: 'usuario-editar', params: { id: item.id } })"
          />
          <v-btn
            icon="mdi-delete"
            size="small"
            variant="text"
            color="error"
            title="Excluir"
            @click="confirmarExcluir(item)"
          />
        </template>

        <template #bottom />
      </v-data-table>

      <!-- Paginação -->
      <v-divider />
      <div class="d-flex align-center justify-space-between px-4 py-2">
        <span class="text-caption text-medium-emphasis">{{ totalItens }} registro(s)</span>
        <v-pagination
          v-model="filtros.page"
          :length="totalPaginas"
          size="small"
          @update:model-value="buscar($event)"
        />
      </div>
    </v-card>

    <!-- Dialog Confirmar Exclusão -->
    <v-dialog v-model="dialogExcluir" max-width="420">
      <v-card rounded="lg">
        <v-card-title class="d-flex align-center gap-2 pt-5 px-6">
          <v-icon color="error" icon="mdi-alert-circle" class="mr-2" />
          Confirmar exclusão
        </v-card-title>
        <v-card-text class="px-6">
          Deseja excluir o usuário <strong>{{ usuarioParaExcluir?.nome }}</strong>? Esta ação não pode ser desfeita.
        </v-card-text>
        <v-card-actions class="px-6 pb-5">
          <v-spacer />
          <v-btn variant="tonal" @click="dialogExcluir = false">Cancelar</v-btn>
          <v-btn color="error" variant="flat" :loading="loading" @click="deletar">Excluir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>
