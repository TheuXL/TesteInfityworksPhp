<template>
  <div>
    <h2 class="mb-4 text-2xl font-bold text-slate-900">{{ isEdit ? 'Editar curso' : 'Novo curso' }}</h2>
    <form class="max-w-md space-y-4" @submit.prevent="submit">
      <AppInput v-model="form.title" label="Título" required :error="errors.title" />
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Descrição</label>
        <textarea v-model="form.description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Área</label>
        <select v-model="form.area_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
          <option value="">Selecione</option>
          <option v-for="a in areas" :key="a.id" :value="a.id">{{ a.name }}</option>
        </select>
      </div>
      <AppInput v-model="form.start_date" label="Data início" type="date" />
      <AppInput v-model="form.end_date" label="Data fim" type="date" />
      <div class="flex gap-2">
        <AppButton type="submit" :disabled="saving">{{ saving ? 'Salvando…' : 'Salvar' }}</AppButton>
        <router-link to="/admin/courses"><AppButton variant="secondary">Cancelar</AppButton></router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import CourseService from '../../../services/CourseService';
import AreaService from '../../../services/AreaService';
import AppInput from '../../../components/ui/AppInput.vue';
import AppButton from '../../../components/ui/AppButton.vue';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => route.params.id);
const form = reactive({ title: '', description: '', area_id: '', start_date: '', end_date: '' });
const areas = ref([]);
const errors = reactive({});
const saving = ref(false);

onMounted(async () => {
  const resAreas = await AreaService.getAll();
  areas.value = resAreas.data?.data ?? resAreas.data ?? [];
  if (isEdit.value) {
    const res = await CourseService.getOne(route.params.id);
    const d = res.data?.data ?? res.data;
    if (d) {
      form.title = d.title ?? '';
      form.description = d.description ?? '';
      form.area_id = d.area_id ?? '';
      form.start_date = d.start_date ?? '';
      form.end_date = d.end_date ?? '';
    }
  }
});

async function submit() {
  saving.value = true;
  try {
    const payload = { ...form, area_id: Number(form.area_id) || undefined };
    if (isEdit.value) await CourseService.update(route.params.id, payload);
    else await CourseService.create(payload);
    router.push('/admin/courses');
  } catch (err) {
    Object.assign(errors, err.response?.data?.errors ?? {});
  } finally {
    saving.value = false;
  }
}
</script>
