<script setup lang="ts">
defineProps<{
  modelValue: number | string | null
  label?: string
  placeholder?: string
  error?: string
  opcoes: Array<{ value: number | string; label: string; disabled?: boolean }>
  required?: boolean
}>()

defineEmits<{
  (e: 'update:modelValue', value: number | string): void
}>()
</script>

<template>
  <label class="block">
    <span v-if="label" class="block text-sm font-medium text-slate-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </span>
    <select
      :value="modelValue ?? ''"
      :required="required"
      :class="[
        'w-full px-3 py-2 rounded-md border bg-white text-sm shadow-sm transition',
        'focus:outline-none focus:ring-2',
        error
          ? 'border-red-400 focus:border-red-500 focus:ring-red-200'
          : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200',
      ]"
      @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    >
      <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
      <option
        v-for="opt in opcoes"
        :key="opt.value"
        :value="opt.value"
        :disabled="opt.disabled"
      >
        {{ opt.label }}
      </option>
    </select>
    <p v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</p>
  </label>
</template>
