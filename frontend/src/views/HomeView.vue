<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuth } from '../stores/auth'

const router = useRouter()
const { state, logout } = useAuth()

async function handleLogout() {
  await logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <v-app>
    <v-app-bar color="primary" elevation="2">
      <v-app-bar-title>
        <span class="font-weight-bold">SESAB</span>
        <span class="text-body-2 ml-2 opacity-80">Sistema de Gestão</span>
      </v-app-bar-title>

      <template #append>
        <v-chip class="mr-2" color="white" variant="tonal" size="small">
          <v-icon start icon="mdi-account-circle" />
          {{ state.user?.name }}
        </v-chip>
        <v-btn icon="mdi-logout" variant="text" color="white" @click="handleLogout" title="Sair" />
      </template>
    </v-app-bar>

    <v-main class="bg-grey-lighten-4">
      <v-container class="py-8">

        <v-row class="mb-6">
          <v-col>
            <h2 class="text-h5 font-weight-bold text-grey-darken-3">
              Bem-vindo, {{ state.user?.name }}!
            </h2>
            <p class="text-body-2 text-medium-emphasis">
              Você está autenticado com sucesso no sistema.
            </p>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" md="4">
            <v-card rounded="lg" elevation="2">
              <v-card-item>
                <template #prepend>
                  <v-avatar color="primary" variant="tonal">
                    <v-icon icon="mdi-account" />
                  </v-avatar>
                </template>
                <v-card-title>Dados do Usuário</v-card-title>
              </v-card-item>
              <v-divider />
              <v-list density="compact">
                <v-list-item>
                  <template #prepend>
                    <v-icon icon="mdi-badge-account" class="mr-2" color="grey" size="18" />
                  </template>
                  <v-list-item-title class="text-caption text-medium-emphasis">Nome</v-list-item-title>
                  <v-list-item-subtitle>{{ state.user?.name }}</v-list-item-subtitle>
                </v-list-item>
                <v-list-item>
                  <template #prepend>
                    <v-icon icon="mdi-email" class="mr-2" color="grey" size="18" />
                  </template>
                  <v-list-item-title class="text-caption text-medium-emphasis">E-mail</v-list-item-title>
                  <v-list-item-subtitle>{{ state.user?.email }}</v-list-item-subtitle>
                </v-list-item>
                <v-list-item>
                  <template #prepend>
                    <v-icon icon="mdi-shield-check" class="mr-2" color="grey" size="18" />
                  </template>
                  <v-list-item-title class="text-caption text-medium-emphasis">Perfil</v-list-item-title>
                  <v-list-item-subtitle>
                    <v-chip :color="state.user?.role === 'admin' ? 'error' : 'success'" size="x-small" label>
                      {{ state.user?.role }}
                    </v-chip>
                  </v-list-item-subtitle>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>

          <v-col cols="12" md="8">
            <v-card rounded="lg" elevation="2">
              <v-card-item>
                <template #prepend>
                  <v-avatar color="success" variant="tonal">
                    <v-icon icon="mdi-key-variant" />
                  </v-avatar>
                </template>
                <v-card-title>Token de Acesso</v-card-title>
                <v-card-subtitle>Bearer Token (em memória)</v-card-subtitle>
              </v-card-item>
              <v-divider />
              <v-card-text>
                <v-textarea
                  :model-value="state.token"
                  readonly
                  variant="outlined"
                  density="compact"
                  rows="3"
                  label="Token ativo"
                  hide-details
                  class="font-weight-mono"
                />
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

      </v-container>
    </v-main>
  </v-app>
</template>
