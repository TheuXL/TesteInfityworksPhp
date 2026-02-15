<template>
  <div>
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
      <h2 class="text-2xl font-bold text-slate-900">Relatório: Idade por curso</h2>
      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 disabled:opacity-50"
        :disabled="loadingPdf"
        @click="handleDownloadPdf"
      >
        <span v-if="loadingPdf">Gerando…</span>
        <span v-else>Gerar relatório PDF completo</span>
      </button>
    </div>
    <div v-if="loading" class="text-slate-500">Carregando…</div>
    <template v-else>
      <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 font-semibold text-slate-700">Curso</th>
              <th class="px-4 py-3 font-semibold text-slate-700">Média de idade</th>
              <th class="px-4 py-3 font-semibold text-slate-700">Aluno mais novo</th>
              <th class="px-4 py-3 font-semibold text-slate-700">Aluno mais velho</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <tr v-for="row in report" :key="row.course?.id" class="hover:bg-slate-50">
              <td class="px-4 py-3 text-slate-800">{{ row.course?.title }}</td>
              <td class="px-4 py-3">{{ row.average_age }}</td>
              <td class="px-4 py-3">{{ youngestText(row) }}</td>
              <td class="px-4 py-3">{{ oldestText(row) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-6">
        <AppCard>
          <template #title>Idade média por curso</template>
          <BarChart
            :labels="chartLabels"
            :series="chartSeries"
            color="#2563eb"
          />
        </AppCard>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import ReportService from '../../services/ReportService';
import AppCard from '../../components/ui/AppCard.vue';
import BarChart from '../../components/charts/BarChart.vue';

const report = ref([]);
const loading = ref(true);
const loadingPdf = ref(false);

const chartLabels = computed(() => report.value.map((r) => r.course?.title).filter(Boolean));
const chartSeries = computed(() => report.value.map((r) => r.average_age));

function youngestText(row) {
  const y = row.youngest;
  return y ? `${y.name} (${y.age} anos)` : '–';
}
function oldestText(row) {
  const o = row.oldest;
  return o ? `${o.name} (${o.age} anos)` : '–';
}

async function handleDownloadPdf() {
  loadingPdf.value = true;
  try {
    await ReportService.downloadPdf();
  } catch (err) {
    console.error('Erro ao gerar PDF', err);
  } finally {
    loadingPdf.value = false;
  }
}

onMounted(async () => {
  try {
    const { data } = await ReportService.getCourseAges();
    report.value = data.report ?? [];
  } finally {
    loading.value = false;
  }
});
</script>
