<template>
  <div>
    <h2 class="mb-4 text-2xl font-bold text-slate-900">{{ isEdit ? 'Editar professor' : 'Novo professor' }}</h2>
    <form class="max-w-md space-y-4" @submit.prevent="submit">
      <AppInput v-model="form.name" label="Nome" required :error="errors.name" />
      <AppInput v-model="form.email" label="E-mail" type="email" :error="errors.email" />
      <div class="flex gap-2">
        <AppButton type="submit" :disabled="saving">{{ saving ? 'Salvando…' : 'Salvar' }}</AppButton>
        <router-link to="/admin/teachers"><AppButton variant="secondary">Cancelar</AppButton></router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import TeacherService from '../../../services/TeacherService';
import AppInput from '../../../components/ui/AppInput.vue';
import AppButton from '../../../components/ui/AppButton.vue';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => route.params.id);
const form = reactive({ name: '', email: '' });
const errors = reactive({});
const saving = ref(false);

onMounted(async () => {
  if (isEdit.value) {
    const { data } = await TeacherService.getOne(route.params.id);
    const d = data.data ?? data;
    form.name = d.name ?? '';
    form.email = d.email ?? '';
  }
});

async function submit() {
  saving.value = true;
  try {
    if (isEdit.value) await TeacherService.update(route.params.id, form);
    else await TeacherService.create(form);
    router.push('/admin/teachers');
  } catch (err) {
    Object.assign(errors, err.response?.data?.errors ?? {});
  } finally {
    saving.value = false;
  }
}
</script>
