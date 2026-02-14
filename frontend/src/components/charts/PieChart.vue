<template>
  <div v-if="labels?.length" class="chart-container w-full">
    <VueApexCharts
      type="pie"
      :height="chartHeight"
      :options="options"
      :series="series"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
  labels: { type: Array, default: () => [] },
  series: { type: Array, default: () => [] },
  colors: { type: Array, default: () => ['#2563eb', '#059669', '#d97706', '#dc2626'] },
  dark: Boolean,
  /** Se true, otimiza para leitura: sem texto em cima da fatia, legenda clara com nome + valor + % */
  compactLabels: { type: Boolean, default: false },
});

const chartHeight = 280;

const options = computed(() => {
  const total = props.series.reduce((a, b) => a + b, 0);
  return {
    chart: { fontFamily: 'inherit', background: 'transparent' },
    labels: props.labels,
    colors: props.colors,
    dataLabels: {
      enabled: !props.compactLabels,
      formatter: (val) => (total > 0 && val >= 8 ? `${Math.round(val)}%` : ''),
      style: { fontSize: '11px' },
    },
    legend: {
      position: 'bottom',
      horizontalAlign: 'center',
      fontSize: '12px',
      itemMargin: { horizontal: 10, vertical: 6 },
      labels: { colors: props.dark ? '#94a3b8' : undefined },
      formatter: (name, opts) => {
        if (!props.compactLabels) return name;
        const val = opts.w.globals.series[opts.seriesIndex];
        const pct = total > 0 ? Math.round((val / total) * 100) : 0;
        const short = name.length > 28 ? name.slice(0, 25) + '…' : name;
        return `${short}: ${val} (${pct}%)`;
      },
    },
    plotOptions: {
      pie: {
        dataLabels: {
          minAngleToShowLabel: props.compactLabels ? 360 : 15,
        },
      },
    },
    responsive: [
      { breakpoint: 640, options: { legend: { fontSize: '11px' } } },
    ],
  };
});
</script>

<style scoped>
.chart-container {
  min-height: 200px;
}
@media (max-width: 640px) {
  .chart-container {
    min-height: 280px;
  }
}
</style>
