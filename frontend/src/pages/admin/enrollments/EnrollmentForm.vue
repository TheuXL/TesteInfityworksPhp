<template>
  <div>
    <h2 class="mb-4 text-2xl font-bold text-slate-900">{{ isEdit ? 'Editar matrícula' : 'Nova matrícula' }}</h2>
    <form class="max-w-md space-y-4" @submit.prevent="submit">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Aluno</label>
        <select v-model="form.student_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
          <option value="">Selecione</option>
          <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }} ({{ s.email }})</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Curso</label>
        <select v-model="form.course_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
          <option value="">Selecione</option>
          <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
        </select>
      </div>
      <div class="flex gap-2">
        <AppButton type="submit" :disabled="saving">{{ saving ? 'Salvando…' : 'Salvar' }}</AppButton>
        <router-link to="/admin/enrollments"><AppButton variant="secondary">Cancelar</AppButton></router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import EnrollmentService from '../../../services/EnrollmentService';
import AppButton from '../../../components/ui/AppButton.vue';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => route.params.id);
const form = reactive({ student_id: '', course_id: '' });
const students = ref([]);
const courses = ref([]);
const errors = reactive({});
const saving = ref(false);

onMounted(async () => {
  if (isEdit.value) {
    const [createData, one] = await Promise.all([
      EnrollmentService.getCreateData(),
      EnrollmentService.getOne(route.params.id),
    ]);
    students.value = createData.data?.students ?? createData.data?.data?.students ?? [];
    courses.value = createData.data?.courses ?? createData.data?.data?.courses ?? [];
    const d = one.data?.data ?? one.data;
    if (d) {
      form.student_id = d.student_id ?? '';
      form.course_id = d.course_id ?? '';
    }
  } else {
    const { data } = await EnrollmentService.getCreateData();
    students.value = data.students ?? data.data?.students ?? [];
    courses.value = data.courses ?? data.data?.courses ?? [];
  }
});

async function submit() {
  saving.value = true;
  try {
    const payload = { student_id: Number(form.student_id), course_id: Number(form.course_id) };
    if (isEdit.value) await EnrollmentService.update(route.params.id, payload);
    else await EnrollmentService.create(payload);
    router.push('/admin/enrollments');
  } catch (err) {
    Object.assign(errors, err.response?.data?.errors ?? {});
  } finally {
    saving.value = false;
  }
}
</script>
