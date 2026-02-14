<template>
  <div>
    <h2 class="mb-4 text-2xl font-bold text-slate-900">{{ isEdit ? 'Editar disciplina' : 'Nova disciplina' }}</h2>
    <form class="max-w-md space-y-4" @submit.prevent="submit">
      <AppInput v-model="form.title" label="Título" required :error="errors.title" />
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Curso</label>
        <select v-model="form.course_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
          <option value="">Selecione</option>
          <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Professor</label>
        <select v-model="form.teacher_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
          <option value="">Selecione</option>
          <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
        </select>
      </div>
      <div class="flex gap-2">
        <AppButton type="submit" :disabled="saving">{{ saving ? 'Salvando…' : 'Salvar' }}</AppButton>
        <router-link to="/admin/disciplines"><AppButton variant="secondary">Cancelar</AppButton></router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import DisciplineService from '../../../services/DisciplineService';
import CourseService from '../../../services/CourseService';
import TeacherService from '../../../services/TeacherService';
import AppInput from '../../../components/ui/AppInput.vue';
import AppButton from '../../../components/ui/AppButton.vue';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => route.params.id);
const form = reactive({ title: '', course_id: '', teacher_id: '' });
const courses = ref([]);
const teachers = ref([]);
const errors = reactive({});
const saving = ref(false);

onMounted(async () => {
  const [cRes, tRes] = await Promise.all([CourseService.getAll(), TeacherService.getAll()]);
  courses.value = cRes.data?.data ?? cRes.data ?? [];
  teachers.value = tRes.data?.data ?? tRes.data ?? [];
  if (isEdit.value) {
    const res = await DisciplineService.getOne(route.params.id);
    const d = res.data?.data ?? res.data;
    if (d) {
      form.title = d.title ?? '';
      form.course_id = d.course_id ?? '';
      form.teacher_id = d.teacher_id ?? '';
    }
  }
});

async function submit() {
  saving.value = true;
  try {
    const payload = {
      title: form.title,
      course_id: Number(form.course_id),
      teacher_id: Number(form.teacher_id),
    };
    if (isEdit.value) await DisciplineService.update(route.params.id, payload);
    else await DisciplineService.create(payload);
    router.push('/admin/disciplines');
  } catch (err) {
    Object.assign(errors, err.response?.data?.errors ?? {});
  } finally {
    saving.value = false;
  }
}
</script>
