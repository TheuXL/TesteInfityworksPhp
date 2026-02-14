<template>
  <div v-if="labels?.length" class="line-chart-wrap min-h-[200px] w-full min-w-0">
    <VueApexCharts
      type="area"
      :height="height"
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
  color: { type: String, default: '#06b6d4' },
  dark: Boolean,
  /** Oculta os números do eixo Y (lateral) */
  hideYAxis: Boolean,
});

const height = 200;

const safeCategories = computed(() =>
  props.labels.map((l) => {
    if (l == null) return '';
    if (typeof l === 'number') return '';
    const s = String(l).trim();
    return s.length > 12 ? s.slice(0, 12) : s;
  })
);

const options = computed(() => ({
  chart: {
    width: '100%',
    toolbar: { show: false },
    fontFamily: 'inherit',
    zoom: { enabled: false },
    background: 'transparent',
  },
  stroke: { curve: 'smooth', width: 2 },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.4,
      opacityTo: 0.05,
    },
  },
  dataLabels: { enabled: false },
  xaxis: {
    type: 'category',
    categories: safeCategories.value,
    labels: {
      rotate: -40,
      maxHeight: 80,
      hideOverlappingLabels: true,
      trim: true,
      formatter: (val) => {
        if (val == null || val === '') return '';
        if (typeof val === 'number') return '';
        const s = String(val).trim();
        if (!s || s.length > 10) return s.length > 10 ? s.slice(0, 10) : s;
        return s;
      },
      style: {
        colors: props.dark !== false ? '#94a3b8' : undefined,
        fontSize: '11px',
      },
    },
  },
  yaxis: props.hideYAxis
    ? { show: false }
    : {
        labels: {
          style: { colors: props.dark !== false ? '#94a3b8' : undefined },
          maxWidth: 36,
        },
      },
  colors: [props.color],
  grid: {
    borderColor: 'rgba(255,255,255,0.06)',
    strokeDashArray: 4,
    padding: { left: 16, right: 16, bottom: 32, top: 12 },
  },
  responsive: [
    {
      breakpoint: 640,
      options: {
        xaxis: {
          labels: {
            rotate: -45,
            style: { fontSize: '10px' },
          },
        },
        grid: { padding: { left: 12, right: 12, bottom: 36 } },
      },
    },
    {
      breakpoint: 380,
      options: {
        xaxis: {
          labels: {
            rotate: -50,
            style: { fontSize: '9px' },
          },
        },
        grid: { padding: { left: 8, right: 8, bottom: 40 } },
      },
    },
  ],
}));

const seriesData = computed(() => [{ name: 'Valor', data: props.series }]);
</script>

<style scoped>
.line-chart-wrap :deep(.apexcharts-canvas) {
  max-width: 100% !important;
}
.line-chart-wrap :deep(svg) {
  overflow: visible;
}
</style>
