<template>
  <div>
    <h2 class="mb-4 text-2xl font-bold text-slate-900">{{ isEdit ? 'Editar área' : 'Nova área' }}</h2>
    <form class="max-w-md space-y-4" @submit.prevent="submit">
      <AppInput v-model="form.name" label="Nome" required :error="errors.name" />
      <div class="flex gap-2">
        <AppButton type="submit" :disabled="saving">{{ saving ? 'Salvando…' : 'Salvar' }}</AppButton>
        <router-link to="/admin/areas"><AppButton variant="secondary">Cancelar</AppButton></router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AreaService from '../../../services/AreaService';
import AppInput from '../../../components/ui/AppInput.vue';
import AppButton from '../../../components/ui/AppButton.vue';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => route.params.id);
const form = reactive({ name: '' });
const errors = reactive({});
const saving = ref(false);

onMounted(async () => {
  if (isEdit.value) {
    const { data } = await AreaService.getOne(route.params.id);
    const d = data.data ?? data;
    form.name = d.name ?? '';
  }
});

async function submit() {
  errors.name = '';
  saving.value = true;
  try {
    if (isEdit.value) {
      await AreaService.update(route.params.id, form);
    } else {
      await AreaService.create(form);
    }
    router.push('/admin/areas');
  } catch (err) {
    const e = err.response?.data?.errors ?? {};
    Object.assign(errors, e);
  } finally {
    saving.value = false;
  }
}
</script>
