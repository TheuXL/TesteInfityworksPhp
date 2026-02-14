<template>
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <h3 v-if="$slots.title" class="mb-3 text-sm font-semibold text-slate-700">
      <slot name="title" />
    </h3>
    <slot />
  </div>
</template>

<script setup>
</script>
