<template>
  <div>
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-2xl font-bold text-slate-900">Alunos</h2>
      <router-link to="/admin/students/create"><AppButton>Novo aluno</AppButton></router-link>
    </div>
    <div class="mb-3 flex flex-wrap items-center gap-3">
      <input
        v-model="search"
        type="search"
        placeholder="Buscar por nome ou e-mail..."
        class="w-full max-w-sm rounded-lg border border-slate-300 px-3 py-2 text-sm"
      />
      <label class="flex items-center gap-2 text-sm text-slate-600">
        Ordenar:
        <select
          v-model="sort"
          class="rounded-lg border border-slate-300 px-3 py-2 text-sm"
          @change="fetch"
        >
          <option value="newest">Mais recentes primeiro</option>
          <option value="oldest">Mais antigos primeiro</option>
          <option value="name_asc">A–Z</option>
          <option value="name_desc">Z–A</option>
        </select>
      </label>
    </div>
    <div v-if="loading" class="text-slate-500">Carregando…</div>
    <div v-else class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
      <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 font-semibold text-slate-700">Nome</th>
            <th class="px-4 py-3 font-semibold text-slate-700">E-mail</th>
            <th class="px-4 py-3 font-semibold text-slate-700">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50">
            <td class="px-4 py-3">{{ item.name }}</td>
            <td class="px-4 py-3">{{ item.email }}</td>
            <td class="px-4 py-3">
              <router-link :to="`/admin/students/${item.id}/edit`" class="text-blue-600 hover:underline">Editar</router-link>
              <button type="button" class="ml-3 text-red-600 hover:underline" @click="remove(item)">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import StudentService from '../../../services/StudentService';
import AppButton from '../../../components/ui/AppButton.vue';

const items = ref([]);
const loading = ref(true);
const search = ref('');
const sort = ref('newest');

function fetch() {
  loading.value = true;
  StudentService.getAll({
    search: search.value || undefined,
    sort: sort.value,
    per_page: 5000,
  })
    .then(({ data }) => {
      items.value = data.data ?? data;
    })
    .finally(() => { loading.value = false; });
}

watch(search, () => fetch());
onMounted(fetch);

async function remove(item) {
  if (!confirm(`Excluir aluno "${item.name}"?`)) return;
  await StudentService.delete(item.id);
  items.value = items.value.filter((i) => i.id !== item.id);
}
</script>
