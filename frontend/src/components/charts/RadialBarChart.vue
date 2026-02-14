<template>
  <div v-if="labels?.length" class="min-h-[200px] w-full">
    <VueApexCharts
      type="radialBar"
      height="280"
      :options="options"
      :series="normalizedSeries"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
  labels: { type: Array, default: () => [] },
  series: { type: Array, default: () => [] },
  colors: { type: Array, default: () => ['#8b5cf6', '#06b6d4', '#22c55e', '#f59e0b', '#ef4444', '#ec4899'] },
  dark: Boolean,
});

const maxVal = computed(() => {
  const arr = props.series;
  if (!arr?.length) return 100;
  const m = Math.max(...arr);
  return m > 0 ? m : 100;
});

const normalizedSeries = computed(() =>
  props.series.map((v) => (maxVal.value > 0 ? Math.round((Number(v) / maxVal.value) * 100) : 0))
);

const options = computed(() => ({
  chart: {
    fontFamily: 'inherit',
    background: 'transparent',
    sparkline: { enabled: false },
  },
  plotOptions: {
    radialBar: {
      dataLabels: {
        name: {
          show: true,
          fontSize: '11px',
          color: props.dark ? '#94a3b8' : '#64748b',
          offsetY: -2,
          formatter: (_, opts) => props.labels[opts.seriesIndex] ?? '',
        },
        value: {
          show: true,
          fontSize: '14px',
          fontWeight: 600,
          color: props.dark ? '#e2e8f0' : '#1e293b',
          offsetY: 4,
          formatter: (val, opts) => props.series[opts.seriesIndex] ?? val,
        },
        total: {
          show: false,
        },
      },
      hollow: {
        size: '40%',
      },
      track: {
        background: props.dark ? 'rgba(255,255,255,0.06)' : '#e2e8f0',
      },
    },
  },
  colors: props.colors.slice(0, Math.max(props.series.length, 1)),
  labels: props.labels,
  legend: {
    show: false,
  },
  responsive: [
    {
      breakpoint: 640,
      options: {
        chart: { height: 320 },
        plotOptions: {
          radialBar: {
            dataLabels: {
              name: { fontSize: '10px' },
              value: { fontSize: '12px' },
            },
          },
        },
      },
    },
  ],
}));
</script>
