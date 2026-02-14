<template>
  <div v-if="labels?.length" class="min-h-[200px] w-full">
    <VueApexCharts
      type="donut"
      height="200"
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
  colors: { type: Array, default: () => ['#2563eb', '#059669', '#d97706', '#dc2626', '#7c3aed'] },
  dark: Boolean,
});

const options = computed(() => ({
  chart: { fontFamily: 'inherit', background: 'transparent' },
  labels: props.labels,
  colors: props.colors,
  legend: {
    position: 'bottom',
    fontSize: '12px',
    labels: { colors: props.dark ? '#94a3b8' : undefined },
  },
}));
</script>
