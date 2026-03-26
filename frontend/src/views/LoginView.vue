<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../stores/auth'

const router = useRouter()
const { state, login } = useAuth()

const email = ref('')
const password = ref('')
const showPassword = ref(false)

async function handleLogin() {
  const success = await login(email.value, password.value)
  if (success) {
    router.push({ name: 'usuarios' })
  }
}
</script>

<template>
  <v-app>
    <v-main class="bg-grey-lighten-3">
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="8" md="5" lg="4">

            <v-card class="pa-6" elevation="6" rounded="lg">
              <div class="d-flex flex-column align-center mb-6">
                <v-avatar color="primary" size="64" class="mb-3">
                  <v-icon icon="mdi-shield-account" size="36" />
                </v-avatar>
                <h1 class="text-h5 font-weight-bold text-grey-darken-3">
                  SESAB
                </h1>
                <p class="text-body-2 text-medium-emphasis mt-1">
                  Secretaria da Saúde do Estado da Bahia
                </p>
              </div>

              <v-alert
                v-if="state.error"
                type="error"
                variant="tonal"
                class="mb-4"
                closable
                @click:close="state.error = null"
              >
                {{ state.error }}
              </v-alert>

              <v-form @submit.prevent="handleLogin">
                <v-text-field
                  v-model="email"
                  label="E-mail"
                  type="email"
                  prepend-inner-icon="mdi-email-outline"
                  variant="outlined"
                  density="comfortable"
                  class="mb-3"
                  autocomplete="email"
                  :disabled="state.loading"
                  required
                />

                <v-text-field
                  v-model="password"
                  label="Senha"
                  :type="showPassword ? 'text' : 'password'"
                  prepend-inner-icon="mdi-lock-outline"
                  :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  variant="outlined"
                  density="comfortable"
                  class="mb-5"
                  autocomplete="current-password"
                  :disabled="state.loading"
                  required
                  @click:append-inner="showPassword = !showPassword"
                />

                <v-btn
                  type="submit"
                  color="primary"
                  size="large"
                  block
                  :loading="state.loading"
                >
                  Entrar
                </v-btn>
              </v-form>

              <v-divider class="my-4" />

              <v-card variant="tonal" color="info" rounded="lg" class="pa-3">
                <p class="text-caption text-medium-emphasis mb-1 font-weight-medium">
                  Credenciais de acesso:
                </p>
                <p class="text-caption">
                  <strong>Admin:</strong> admin@sesab.ba.gov.br / admin123
                </p>
                <p class="text-caption">
                  <strong>Usuário:</strong> usuario@sesab.ba.gov.br / usuario123
                </p>
              </v-card>
            </v-card>

          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>
