<template>
  <div>
    <h2 class="mb-4 text-lg font-semibold text-slate-800">Cadastro de aluno</h2>
    <form class="space-y-4" @submit.prevent="submit">
      <AppInput
        v-model="form.name"
        label="Nome"
        required
        placeholder="Seu nome"
        :error="errors.name"
      />
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
        placeholder="Mínimo 8 caracteres"
        :error="errors.password"
      />
      <AppInput
        v-model="form.password_confirmation"
        label="Confirmar senha"
        type="password"
        required
        placeholder="Repita a senha"
      />
      <AppInput
        v-model="form.birth_date"
        label="Data de nascimento"
        type="date"
        required
        :error="errors.birth_date"
      />
      <AppButton type="submit" class="w-full" :disabled="loading">
        {{ loading ? 'Cadastrando…' : 'Cadastrar' }}
      </AppButton>
      <p class="text-center text-sm text-slate-500">
        Já tem conta?
        <router-link to="/login" class="font-medium text-blue-600 hover:underline">Entrar</router-link>
      </p>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import AuthService from '../../services/AuthService';
import AppInput from '../../components/ui/AppInput.vue';
import AppButton from '../../components/ui/AppButton.vue';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  birth_date: '',
});
const errors = reactive({ name: '', email: '', password: '', birth_date: '' });
const loading = ref(false);

async function submit() {
  errors.name = '';
  errors.email = '';
  errors.password = '';
  errors.birth_date = '';
  if (form.password !== form.password_confirmation) {
    errors.password = 'As senhas não coincidem.';
    return;
  }
  loading.value = true;
  try {
    await AuthService.getCsrfCookie();
    await AuthService.register(form);
    router.push({ path: '/login', query: { registered: '1' } });
  } catch (err) {
    const data = err.response?.data;
    const status = err.response?.status;
    if (data && typeof data === 'object' && status === 422 && data.errors) {
      errors.name = data.errors.name?.[0] ?? '';
      errors.email = data.errors.email?.[0] ?? '';
      errors.password = data.errors.password?.[0] ?? '';
      errors.birth_date = data.errors.birth_date?.[0] ?? '';
    } else {
      const msg = (data && typeof data === 'object' && (data.message ?? data.errors?.email?.[0])) || 'Erro ao cadastrar.';
      errors.email = msg;
    }
  } finally {
    loading.value = false;
  }
}
</script>
