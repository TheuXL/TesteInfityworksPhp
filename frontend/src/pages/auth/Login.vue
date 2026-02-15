<template>
  <div>
    <h2 class="mb-4 text-lg font-semibold text-slate-800">Entrar</h2>
    <p v-if="route.query.registered === '1'" class="mb-4 rounded-lg bg-green-100 p-3 text-sm text-green-800">
      Cadastro realizado com sucesso. Faça login com suas credenciais.
    </p>
    <form class="space-y-4" @submit.prevent="submit">
      <AppInput
        v-model="form.email"
        label="E-mail"
        type="email"
        required
        placeholder="seu@email.com"
        :error="errors.email"
      />
      <AppInput
        v-model="form.password"
        label="Senha"
        type="password"
        required
        placeholder="••••••••"
        :error="errors.password"
      />
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-sm text-slate-600">
          <input v-model="form.remember" type="checkbox" class="rounded border-slate-300" />
          Lembrar de mim
        </label>
      </div>
      <AppButton type="submit" class="w-full" :disabled="loading">
        {{ loading ? 'Entrando…' : 'Entrar' }}
      </AppButton>
      <p class="text-center text-sm text-slate-500">
        Não tem conta?
        <router-link to="/register" class="font-medium text-blue-600 hover:underline">Cadastre-se como aluno</router-link>
      </p>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import AuthService from '../../services/AuthService';
import AppInput from '../../components/ui/AppInput.vue';
import AppButton from '../../components/ui/AppButton.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const form = reactive({ email: '', password: '', remember: false });
const errors = reactive({ email: '', password: '' });
const loading = ref(false);

async function submit() {
  errors.email = '';
  errors.password = '';
  loading.value = true;
  try {
    await AuthService.getCsrfCookie();
    const user = await AuthService.login(form);
    authStore.setUser(user);
    const redirect = route.query.redirect ?? (authStore.isAdmin ? '/admin/dashboard' : '/aluno/dashboard');
    router.push(redirect);
  } catch (err) {
    const data = err.response?.data;
    const msg = data?.message ?? data?.errors?.email?.[0] ?? 'Credenciais inválidas.';
    errors.email = msg;
  } finally {
    loading.value = false;
  }
}
</script>
