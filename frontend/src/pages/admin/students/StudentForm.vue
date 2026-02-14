<template>
  <div>
    <h2 class="mb-4 text-2xl font-bold text-slate-900">{{ isEdit ? 'Editar aluno' : 'Novo aluno' }}</h2>
    <form class="max-w-md space-y-4" @submit.prevent="submit">
      <AppInput v-model="form.name" label="Nome" required :error="errors.name" />
      <AppInput v-model="form.email" label="E-mail" type="email" required :error="errors.email" />
      <AppInput v-model="form.birth_date" label="Data de nascimento" type="date" />
      <div class="flex gap-2">
        <AppButton type="submit" :disabled="saving">{{ saving ? 'Salvando…' : 'Salvar' }}</AppButton>
        <router-link to="/admin/students"><AppButton variant="secondary">Cancelar</AppButton></router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import StudentService from '../../../services/StudentService';
import AppInput from '../../../components/ui/AppInput.vue';
import AppButton from '../../../components/ui/AppButton.vue';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => route.params.id);
const form = reactive({ name: '', email: '', birth_date: '' });
const errors = reactive({});
const saving = ref(false);

onMounted(async () => {
  if (isEdit.value) {
    const { data } = await StudentService.getOne(route.params.id);
    const d = data?.data ?? data;
    if (d) {
      form.name = d.name ?? '';
      form.email = d.email ?? '';
      form.birth_date = d.birth_date ?? '';
    }
  }
});

async function submit() {
  saving.value = true;
  try {
    if (isEdit.value) await StudentService.update(route.params.id, form);
    else await StudentService.create(form);
    router.push('/admin/students');
  } catch (err) {
    Object.assign(errors, err.response?.data?.errors ?? {});
  } finally {
    saving.value = false;
  }
}
</script>
