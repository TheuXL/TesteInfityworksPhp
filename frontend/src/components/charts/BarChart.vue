<template>
  <div v-if="labels?.length" class="bar-chart-wrap min-h-[200px] w-full">
    <VueApexCharts
      type="bar"
      height="200"
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
  color: { type: String, default: '#2563eb' },
  horizontal: Boolean,
  dark: Boolean,
  hideYAxis: Boolean,
});

function truncate(str, maxLen) {
  if (!str || str.length <= maxLen) return str;
  return str.slice(0, maxLen - 1) + '…';
}

const options = computed(() => {
  const isVertical = !props.horizontal;
  const labelColor = props.dark ? '#94a3b8' : undefined;
  const xaxisLabels = {
    style: { colors: labelColor, fontSize: '12px' },
  };
  if (isVertical) {
    xaxisLabels.rotate = -40;
    xaxisLabels.formatter = (val) => truncate(val, 24);
    xaxisLabels.maxHeight = 90;
  }

  const yaxisLabels = {
    style: { colors: labelColor, fontSize: '12px' },
  };
  if (!isVertical) {
    yaxisLabels.formatter = (val) => truncate(val, 20);
    yaxisLabels.maxWidth = 120;
  }

  return {
    chart: { toolbar: { show: false }, fontFamily: 'inherit', background: 'transparent' },
    plotOptions: {
      bar: {
        borderRadius: 6,
        columnWidth: '60%',
        horizontal: props.horizontal,
        barHeight: props.horizontal ? '60%' : undefined,
      },
    },
    dataLabels: { enabled: false },
    xaxis: {
      categories: props.labels,
      labels: xaxisLabels,
    },
    yaxis: props.hideYAxis ? { show: false } : { labels: yaxisLabels },
    colors: [props.color],
    grid: {
      ...(props.dark ? { borderColor: 'rgba(255,255,255,0.06)', strokeDashArray: 4 } : {}),
      ...(isVertical
        ? { padding: { left: 72, right: 16, bottom: 28 } }
        : {}),
    },
    tooltip: {
      custom: ({ series, seriesIndex, dataPointIndex }) => {
        const fullLabel = props.labels[dataPointIndex] ?? '';
        const value = series[seriesIndex]?.[dataPointIndex] ?? 0;
        const labelColor = props.dark ? '#94a3b8' : '#64748b';
        const valueColor = props.dark ? '#e2e8f0' : '#1e293b';
        return `<div class="rounded border border-slate-200 bg-white px-2 py-1.5 shadow-lg dark:border-white/20 dark:bg-slate-800" style="font-size: 12px;">
          <div style="color: ${labelColor}; margin-bottom: 2px;">${fullLabel}</div>
          <div style="color: ${valueColor}; font-weight: 600;">${value}</div>
        </div>`;
      },
    },
    responsive: [
      {
        breakpoint: 640,
        options: {
          xaxis: {
            categories: props.labels,
            labels: {
              ...xaxisLabels,
              formatter: (val) => truncate(val, 14),
              style: { ...xaxisLabels.style, fontSize: '10px' },
              maxHeight: 72,
            },
          },
        },
      },
      {
        breakpoint: 1024,
        options: {
          xaxis: {
            categories: props.labels,
            labels: {
              ...xaxisLabels,
              formatter: (val) => truncate(val, 20),
            },
          },
        },
      },
    ],
  };
});

const seriesData = computed(() => [{ name: 'Valor', data: props.series }]);
</script>

<style scoped>
.bar-chart-wrap {
  overflow: visible;
}
.bar-chart-wrap :deep(.apexcharts-inner) {
  overflow: visible;
}
</style>
