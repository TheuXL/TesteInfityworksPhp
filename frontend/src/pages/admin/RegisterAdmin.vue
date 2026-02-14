<template>
  <div class="max-w-md">
    <h2 class="mb-4 text-xl font-semibold text-slate-800">Cadastrar administrador</h2>
    <p v-if="successMessage" class="mb-4 rounded-lg bg-green-100 p-3 text-sm text-green-800">
      {{ successMessage }}
    </p>
    <form class="space-y-4" @submit.prevent="submit">
      <AppInput
        v-model="form.name"
        label="Nome"
        required
        placeholder="Nome do administrador"
        :error="errors.name"
      />
      <AppInput
        v-model="form.email"
        label="E-mail"
        type="email"
        required
        placeholder="admin@exemplo.com"
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
      <AppButton type="submit" :disabled="loading">
        {{ loading ? 'Cadastrando…' : 'Cadastrar administrador' }}
      </AppButton>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import AuthService from '../../services/AuthService';
import AppInput from '../../components/ui/AppInput.vue';
import AppButton from '../../components/ui/AppButton.vue';

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const errors = reactive({ name: '', email: '', password: '' });
const loading = ref(false);
const successMessage = ref('');

async function submit() {
  errors.name = '';
  errors.email = '';
  errors.password = '';
  successMessage.value = '';
  if (form.password !== form.password_confirmation) {
    errors.password = 'As senhas não coincidem.';
    return;
  }
  loading.value = true;
  try {
    await AuthService.registerAdmin(form);
    successMessage.value = 'Administrador cadastrado com sucesso. Ele já pode fazer login na tela de entrada.';
    form.name = '';
    form.email = '';
    form.password = '';
    form.password_confirmation = '';
  } catch (err) {
    const data = err.response?.data;
    const status = err.response?.status;
    if (data && typeof data === 'object' && status === 422 && data.errors) {
      errors.name = data.errors.name?.[0] ?? '';
      errors.email = data.errors.email?.[0] ?? '';
      errors.password = data.errors.password?.[0] ?? '';
    } else {
      const msg = (data && typeof data === 'object' && (data.message ?? data.errors?.email?.[0])) || 'Erro ao cadastrar.';
      errors.email = msg;
    }
  } finally {
    loading.value = false;
  }
}
</script>
