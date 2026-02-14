<template>
  <div class="dashboard-tech">
    <h2 class="mb-4 text-xl font-bold tracking-tight text-white/95 sm:mb-6 sm:text-2xl">
      Dashboard
    </h2>
    <div v-if="loading" class="flex items-center justify-center py-12 text-cyan-400">
      Carregando…
    </div>
    <template v-else>
      <!-- Resumo em cards -->
      <div class="mb-4 grid grid-cols-2 gap-2 sm:mb-6 sm:gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <div
          v-for="(value, key) in (chartData?.summary ?? {})"
          :key="key"
          class="card-glass min-w-0 rounded-xl border border-white/10 bg-white/5 px-3 py-3 backdrop-blur-sm sm:px-4 sm:py-4"
        >
          <p class="truncate text-[10px] font-medium uppercase tracking-wider text-slate-400 sm:text-xs">
            {{ summaryLabels[key] ?? key }}
          </p>
          <p class="mt-0.5 text-lg font-bold text-cyan-400 sm:mt-1 sm:text-2xl">
            {{ value }}
          </p>
        </div>
      </div>

      <!-- Grid de gráficos -->
      <div class="grid grid-cols-1 gap-3 sm:gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="card-glass chart-card min-w-0 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
          <h3 class="mb-2 text-xs font-semibold text-slate-300 sm:mb-3 sm:text-sm">Alunos por curso</h3>
          <BarChart
            :labels="chartData?.students_per_course?.labels ?? []"
            :series="chartData?.students_per_course?.data ?? []"
            color="#06b6d4"
            dark
            hide-y-axis
          />
        </div>
        <div class="card-glass chart-card min-w-0 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
          <h3 class="mb-2 text-xs font-semibold text-slate-300 sm:mb-3 sm:text-sm">Idade média por curso</h3>
          <BarChart
            :labels="chartData?.average_age_per_course?.labels ?? []"
            :series="chartData?.average_age_per_course?.data ?? []"
            color="#22c55e"
            dark
            hide-y-axis
          />
        </div>
        <div class="card-glass chart-card min-w-0 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
          <h3 class="mb-2 text-xs font-semibold text-slate-300 sm:mb-3 sm:text-sm">Alunos por faixa etária</h3>
          <DonutChart
            :labels="chartData?.students_by_age_range?.labels ?? []"
            :series="chartData?.students_by_age_range?.data ?? []"
            :colors="donutColors"
            dark
          />
        </div>
        <div class="card-glass chart-card min-w-0 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
          <h3 class="mb-2 text-xs font-semibold text-slate-300 sm:mb-3 sm:text-sm">Matrículas por curso</h3>
          <PieChart
            :labels="chartData?.enrollments_per_course?.labels ?? []"
            :series="chartData?.enrollments_per_course?.data ?? []"
            :colors="pieColors"
            dark
            compact-labels
          />
        </div>
        <div class="card-glass chart-card min-w-0 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
          <h3 class="mb-2 text-xs font-semibold text-slate-300 sm:mb-3 sm:text-sm">Alunos por área</h3>
          <TreemapChart
            :labels="chartData?.students_per_area?.labels ?? []"
            :series="chartData?.students_per_area?.data ?? []"
            :colors="treemapColors"
            dark
          />
        </div>
        <div class="card-glass chart-card chart-card-line min-w-0 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
          <h3 class="mb-2 text-xs font-semibold text-slate-300 sm:mb-3 sm:text-sm">Matrículas nos últimos 6 meses</h3>
          <LineChart
            :labels="formatMonthChartLabels(chartData?.enrollments_per_month?.labels)"
            :series="chartData?.enrollments_per_month?.data ?? []"
            color="#06b6d4"
            dark
            hide-y-axis
          />
        </div>
        <div class="card-glass chart-card chart-card-line min-w-0 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
          <h3 class="mb-2 text-xs font-semibold text-slate-300 sm:mb-3 sm:text-sm">Novos alunos por mês</h3>
          <LineChart
            :labels="formatMonthChartLabels(chartData?.students_per_month?.labels)"
            :series="chartData?.students_per_month?.data ?? []"
            color="#a855f7"
            dark
            hide-y-axis
          />
        </div>
        <div class="card-glass chart-card min-w-0 rounded-xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm sm:p-4">
          <h3 class="mb-2 text-xs font-semibold text-slate-300 sm:mb-3 sm:text-sm">Disciplinas por curso (top 10)</h3>
          <BarChart
            :labels="chartData?.disciplines_per_course?.labels ?? []"
            :series="chartData?.disciplines_per_course?.data ?? []"
            color="#f59e0b"
            dark
            hide-y-axis
          />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminDashboardService from '../../services/AdminDashboardService';
import BarChart from '../../components/charts/BarChart.vue';
import DonutChart from '../../components/charts/DonutChart.vue';
import PieChart from '../../components/charts/PieChart.vue';
import LineChart from '../../components/charts/LineChart.vue';
import TreemapChart from '../../components/charts/TreemapChart.vue';

const chartData = ref(null);
const loading = ref(true);

/** Formata labels de mês (ex: "out 25" → "out/25") para evitar quebra no eixo X. */
function formatMonthChartLabels(labels) {
  if (!Array.isArray(labels)) return [];
  return labels.map((l) => {
    if (l == null) return '';
    const s = String(l).trim();
    if (!s) return '';
    const parts = s.split(/\s+/);
    if (parts.length >= 2) {
      const mes = parts[0].replace(/\W/g, '').slice(0, 3);
      const ano = parts[1].replace(/\D/g, '').slice(-2);
      return ano ? `${mes}/${ano}` : mes || s.slice(0, 6);
    }
    return s.length > 6 ? s.slice(0, 3) + '/' + s.slice(-2) : s;
  });
}

const summaryLabels = {
  students: 'Alunos',
  enrollments: 'Matrículas',
  courses: 'Cursos',
  teachers: 'Professores',
  areas: 'Áreas',
  disciplines: 'Disciplinas',
};

const donutColors = ['#06b6d4', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'];
const pieColors = ['#06b6d4', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
const treemapColors = ['#8b5cf6', '#06b6d4', '#22c55e', '#f59e0b', '#ef4444', '#ec4899', '#14b8a6'];

onMounted(async () => {
  try {
    const { data } = await AdminDashboardService.getChartData();
    chartData.value = data.chart_data ?? data;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.dashboard-tech {
  min-height: 100%;
  background: linear-gradient(145deg, #0f172a 0%, #1e293b 40%, #0f172a 100%);
  background-attachment: fixed;
  padding: 0.75rem;
  position: relative;
  overflow-x: hidden;
}
@media (min-width: 640px) {
  .dashboard-tech {
    padding: 1rem;
  }
}
@media (min-width: 1024px) {
  .dashboard-tech {
    padding: 1.25rem;
  }
}
.dashboard-tech::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(6, 182, 212, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(6, 182, 212, 0.03) 1px, transparent 1px);
  background-size: 24px 24px;
  pointer-events: none;
}
.card-glass {
  position: relative;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
}
.chart-card {
  overflow: hidden;
  max-width: 100%;
  min-width: 0;
}
.chart-card :deep(.apexcharts-canvas) {
  max-width: 100% !important;
}
/* Espaço extra para rótulos do eixo X nos gráficos de linha (evita corte em PC e celular) */
.chart-card.chart-card-line {
  padding-bottom: 1.5rem;
}
@media (max-width: 640px) {
  .chart-card.chart-card-line {
    padding-bottom: 1.75rem;
  }
}
</style>
