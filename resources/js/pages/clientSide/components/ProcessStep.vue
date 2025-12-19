<!-- ProcessStep.vue -->
<script setup lang="ts">
defineProps<{
  number: number;
  title: string;
  description: string;
}>();
</script>

<template>
  <div class="text-center">
    <div class="w-16 h-16 bg-neutral-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-md shadow-neutral-900/10">
      <span class="text-white font-bold text-xl">{{ number }}</span>
    </div>
    <h3 class="text-xl font-bold mb-3 text-neutral-800">{{ title }}</h3>
    <p class="text-base text-neutral-500 leading-relaxed">{{ description }}</p>
  </div>
</template>
