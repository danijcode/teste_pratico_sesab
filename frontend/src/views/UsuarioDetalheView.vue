<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUsuarios, type Usuario } from '../composables/useUsuarios'

const router = useRouter()
const route = useRoute()
const { loading, error, buscar } = useUsuarios()

const usuario = ref<Usuario | null>(null)

onMounted(async () => {
  usuario.value = await buscar(Number(route.params.id))
})
</script>

<template>
  <v-container fluid class="py-6" style="max-width: 860px">

    <!-- Cabeçalho -->
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" class="mr-2" @click="router.back()" />
      <div>
        <h1 class="text-h5 font-weight-bold text-grey-darken-3">Detalhe do Usuário</h1>
        <p class="text-body-2 text-medium-emphasis">Visualização dos dados cadastrados</p>
      </div>
      <v-spacer />
      <v-btn
        v-if="usuario"
        color="warning"
        variant="tonal"
        prepend-icon="mdi-pencil"
        @click="router.push({ name: 'usuario-editar', params: { id: usuario.id } })"
      >
        Editar
      </v-btn>
    </div>

    <v-progress-circular v-if="loading" indeterminate color="primary" class="d-flex mx-auto my-8" />
    <v-alert v-else-if="error" type="error" variant="tonal">{{ error }}</v-alert>

    <template v-else-if="usuario">

      <!-- Dados principais -->
      <v-card class="mb-4" elevation="1" rounded="lg">
        <v-card-title class="text-subtitle-1 font-weight-medium px-5 pt-4">Dados do Usuário</v-card-title>
        <v-divider />
        <v-list density="comfortable" class="px-2">
          <v-list-item>
            <v-list-item-title class="text-caption text-medium-emphasis">ID</v-list-item-title>
            <v-list-item-subtitle class="text-body-1">{{ usuario.id }}</v-list-item-subtitle>
          </v-list-item>
          <v-divider inset />
          <v-list-item>
            <v-list-item-title class="text-caption text-medium-emphasis">Nome</v-list-item-title>
            <v-list-item-subtitle class="text-body-1">{{ usuario.nome }}</v-list-item-subtitle>
          </v-list-item>
          <v-divider inset />
          <v-list-item>
            <v-list-item-title class="text-caption text-medium-emphasis">E-mail</v-list-item-title>
            <v-list-item-subtitle class="text-body-1">{{ usuario.email }}</v-list-item-subtitle>
          </v-list-item>
          <v-divider inset />
          <v-list-item>
            <v-list-item-title class="text-caption text-medium-emphasis">CPF</v-list-item-title>
            <v-list-item-subtitle class="text-body-1">{{ usuario.cpf }}</v-list-item-subtitle>
          </v-list-item>
          <v-divider inset />
          <v-list-item>
            <v-list-item-title class="text-caption text-medium-emphasis">Perfil</v-list-item-title>
            <v-list-item-subtitle>
              <v-chip :color="usuario.perfil?.nome === 'ADMIN' ? 'error' : 'primary'" size="small" label class="mt-1">
                {{ usuario.perfil?.nome }}
              </v-chip>
            </v-list-item-subtitle>
          </v-list-item>
          <v-divider inset />
          <v-list-item>
            <v-list-item-title class="text-caption text-medium-emphasis">Data de Cadastro</v-list-item-title>
            <v-list-item-subtitle class="text-body-1">{{ usuario.created_at }}</v-list-item-subtitle>
          </v-list-item>
        </v-list>
      </v-card>

      <!-- Endereços -->
      <v-card elevation="1" rounded="lg">
        <v-card-title class="text-subtitle-1 font-weight-medium px-5 pt-4">
          Endereços
          <v-chip size="x-small" class="ml-2" color="primary" variant="tonal">
            {{ usuario.enderecos?.length ?? 0 }}
          </v-chip>
        </v-card-title>
        <v-divider />

        <v-list v-if="usuario.enderecos?.length" density="comfortable">
          <template v-for="(end, i) in usuario.enderecos" :key="end.id">
            <v-list-item :prepend-icon="'mdi-map-marker'">
              <v-list-item-title>{{ end.logradouro }}</v-list-item-title>
              <v-list-item-subtitle>CEP: {{ end.cep }}</v-list-item-subtitle>
            </v-list-item>
            <v-divider v-if="i < usuario.enderecos.length - 1" inset />
          </template>
        </v-list>

        <v-card-text v-else>
          <v-alert type="info" variant="tonal" density="compact">Nenhum endereço cadastrado.</v-alert>
        </v-card-text>
      </v-card>

    </template>

  </v-container>
</template>
