<template>
  <div>
    <h2 class="mb-4 text-2xl font-bold text-slate-900">Meu Dashboard</h2>
    <div v-if="loading" class="text-slate-500">Carregando…</div>
    <div v-else class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
      <AppCard>
        <template #title>Meus cursos</template>
        <DonutChart
          :labels="chartData?.my_courses?.labels ?? []"
          :series="chartData?.my_courses?.data ?? []"
        />
      </AppCard>
      <AppCard>
        <template #title>Minha idade</template>
        <p class="text-3xl font-bold text-slate-800">
          {{ chartData?.my_age?.value ?? 0 }}
          <span class="text-lg font-normal text-slate-500">anos</span>
        </p>
        <BarChart
          v-if="chartData?.my_age?.value != null"
          :labels="['Idade']"
          :series="[chartData.my_age.value]"
          color="#059669"
        />
      </AppCard>
      <AppCard>
        <template #title>Minhas matrículas</template>
        <PieChart
          :labels="chartData?.my_enrollments?.labels ?? []"
          :series="chartData?.my_enrollments?.data ?? []"
        />
      </AppCard>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AlunoService from '../../services/AlunoService';
import AppCard from '../../components/ui/AppCard.vue';
import BarChart from '../../components/charts/BarChart.vue';
import DonutChart from '../../components/charts/DonutChart.vue';
import PieChart from '../../components/charts/PieChart.vue';

const chartData = ref(null);
const loading = ref(true);

onMounted(async () => {
  try {
    const { data } = await AlunoService.getDashboardChart();
    chartData.value = data.chart_data ?? data;
  } finally {
    loading.value = false;
  }
});
</script>
