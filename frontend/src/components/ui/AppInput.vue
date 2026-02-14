<template>
  <div class="w-full">
    <label v-if="label" class="mb-1 block text-sm font-medium text-slate-700">
      {{ label }}
    </label>
    <input
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-slate-100"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  type: { type: String, default: 'text' },
  modelValue: [String, Number],
  placeholder: String,
  required: Boolean,
  disabled: Boolean,
  error: String,
});
defineEmits(['update:modelValue']);
</script>
