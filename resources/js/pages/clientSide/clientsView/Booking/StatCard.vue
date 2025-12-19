<!-- components/StatCard.vue -->
<script setup lang="ts">
defineProps<{
  value: string;
  label: string;
}>();
</script>

<template>
  <div class="text-center">
    <div class="text-4xl md:text-5xl font-bold text-white mb-2">
      {{ value }}
    </div>
    <div class="text-blue-100 text-sm md:text-base">
      {{ label }}
    </div>
  </div>
</template>