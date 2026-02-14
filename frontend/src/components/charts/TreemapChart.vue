<template>
  <div v-if="labels?.length" class="min-h-[220px] w-full">
    <VueApexCharts
      type="treemap"
      height="260"
      :options="options"
      :series="seriesData"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
  labels: { type: Array, default: () => [] },
  series: { type: Array, default: () => [] },
  colors: { type: Array, default: () => ['#8b5cf6', '#06b6d4', '#22c55e', '#f59e0b', '#ef4444', '#ec4899', '#14b8a6'] },
  dark: Boolean,
});

const seriesData = computed(() => [
  {
    data: props.labels.map((label, i) => ({
      x: label,
      y: Number(props.series[i] ?? 0),
    })),
  },
]);

const options = computed(() => ({
  chart: {
    fontFamily: 'inherit',
    background: 'transparent',
    toolbar: { show: false },
  },
  legend: { show: false },
  colors: props.colors,
  plotOptions: {
    treemap: {
      enableShades: true,
      shadeIntensity: 0.35,
      distributed: true,
      useFillColorAsStroke: false,
      dataLabels: {
        format: 'truncate',
        style: {
          fontSize: '12px',
          colors: [props.dark ? '#e2e8f0' : '#1e293b'],
        },
      },
    },
  },
  dataLabels: {
    enabled: true,
  },
  tooltip: {
    y: {
      formatter: (val, opts) => {
        const label = opts?.w?.config?.series?.[opts.seriesIndex]?.data?.[opts.dataPointIndex]?.x ?? '';
        return label ? `${label}: ${val} aluno(s)` : `${val} aluno(s)`;
      },
    },
  },
  responsive: [
    {
      breakpoint: 640,
      options: {
        chart: { height: 280 },
        plotOptions: {
          treemap: {
            dataLabels: { style: { fontSize: '10px' } },
          },
        },
      },
    },
  ],
}));
</script>
