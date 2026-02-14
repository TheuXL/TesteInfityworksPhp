<template>
  <div v-if="labels?.length" class="min-h-[240px] w-full">
    <VueApexCharts
      type="radar"
      height="280"
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
  color: { type: String, default: '#8b5cf6' },
  dark: Boolean,
});

const options = computed(() => ({
  chart: {
    fontFamily: 'inherit',
    background: 'transparent',
    toolbar: { show: false },
  },
  xaxis: {
    categories: props.labels,
    labels: {
      style: {
        colors: props.dark ? '#94a3b8' : '#64748b',
        fontSize: '11px',
      },
    },
  },
  yaxis: {
    show: true,
    labels: {
      style: { colors: props.dark ? '#94a3b8' : '#64748b' },
    },
  },
  colors: [props.color],
  stroke: { width: 2 },
  fill: {
    opacity: 0.25,
  },
  markers: {
    size: 4,
  },
  grid: {
    borderColor: props.dark ? 'rgba(255,255,255,0.08)' : '#e2e8f0',
  },
  plotOptions: {
    radar: {
      polygons: {
        strokeColors: props.dark ? 'rgba(255,255,255,0.06)' : '#e2e8f0',
        connectorColors: props.dark ? 'rgba(255,255,255,0.06)' : '#e2e8f0',
      },
    },
  },
  legend: {
    show: false,
  },
  dataLabels: {
    enabled: true,
    style: { fontSize: '11px' },
    background: { enabled: false },
  },
  responsive: [
    {
      breakpoint: 640,
      options: {
        chart: { height: 300 },
        xaxis: {
          categories: props.labels,
          labels: { style: { fontSize: '10px' } },
        },
      },
    },
  ],
}));

const seriesData = computed(() => [
  { name: 'Alunos', data: props.series.map((v) => Number(v)) },
]);
</script>
