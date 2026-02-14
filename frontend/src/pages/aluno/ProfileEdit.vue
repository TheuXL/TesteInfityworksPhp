<template>
  <div>
    <h2 class="mb-4 text-2xl font-bold text-slate-900">Editar cadastro {{ form.name ? `– ${form.name}` : '' }}</h2>
    <div v-if="loading" class="text-slate-500">Carregando…</div>
    <form v-else class="max-w-md space-y-4" @submit.prevent="submit">
      <AppInput
        v-model="form.name"
        label="Nome"
        required
        :error="errors.name"
      />
      <AppInput
        v-model="form.email"
        label="E-mail"
        type="email"
        required
        :error="errors.email"
      />
      <AppInput
        v-model="form.birth_date"
        label="Data de nascimento"
        type="date"
      />
      <AppButton type="submit" :disabled="saving">
        {{ saving ? 'Salvando…' : 'Salvar' }}
      </AppButton>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import AlunoService from '../../services/AlunoService';
import AppInput from '../../components/ui/AppInput.vue';
import AppButton from '../../components/ui/AppButton.vue';

const authStore = useAuthStore();

const form = reactive({ name: '', email: '', birth_date: '' });
const errors = reactive({ name: '', email: '' });
const loading = ref(true);
const saving = ref(false);

onMounted(async () => {
  try {
    const { data } = await AlunoService.getProfile();
    const user = data.user?.data ?? data.user;
    const student = data.student?.data ?? data.student;
    if (user) {
      form.name = user.name ?? '';
      form.email = user.email ?? '';
    }
    if (student) {
      form.name = student.name ?? form.name;
      form.email = student.email ?? form.email;
      form.birth_date = student.birth_date ?? '';
    }
  } finally {
    loading.value = false;
  }
});

async function submit() {
  errors.name = '';
  errors.email = '';
  saving.value = true;
  try {
    const { data } = await AlunoService.updateProfile(form);
    const user = data?.user?.data ?? data?.user;
    if (user) authStore.setUser(user);
  } catch (err) {
    const d = err.response?.data?.errors ?? {};
    errors.name = d.name?.[0] ?? '';
    errors.email = d.email?.[0] ?? '';
  } finally {
    saving.value = false;
  }
}
</script>
