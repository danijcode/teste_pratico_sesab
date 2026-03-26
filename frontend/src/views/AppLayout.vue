<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuth } from '../stores/auth'

const router = useRouter()
const { state, logout } = useAuth()

async function handleLogout() {
  await logout()
  router.push({ name: 'login' })
}

const menuItems = [
  { title: 'Usuários',   icon: 'mdi-account-group',  to: { name: 'usuarios' } },
  { title: 'Perfis',     icon: 'mdi-shield-account',  to: { name: 'perfis' } },
  { title: 'Endereços',  icon: 'mdi-map-marker',      to: { name: 'enderecos' } },
]
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
          {{ state.user?.name ?? state.user?.email }}
        </v-chip>
        <v-btn icon="mdi-logout" variant="text" color="white" title="Sair" @click="handleLogout" />
      </template>
    </v-app-bar>

    <v-navigation-drawer permanent width="220">
      <v-list density="compact" nav class="mt-2">
        <v-list-item
          v-for="item in menuItems"
          :key="item.title"
          :prepend-icon="item.icon"
          :title="item.title"
          :to="item.to"
          rounded="lg"
        />
      </v-list>
    </v-navigation-drawer>

    <v-main class="bg-grey-lighten-4">
      <router-view />
    </v-main>
  </v-app>
</template>
