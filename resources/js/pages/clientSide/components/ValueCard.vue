<!-- components/ValueCard.vue -->
<script setup lang="ts">
defineProps<{
  icon: string;
  title: string;
  description: string;
}>();
</script>

<template>
  <div class="text-center p-6 rounded-xl hover:bg-neutral-50 transition-colors duration-300">
    <div class="text-5xl mb-4">{{ icon }}</div>
    <h3 class="text-xl font-bold text-neutral-900 mb-3">
      {{ title }}
    </h3>
    <p class="text-neutral-600">
      {{ description }}
    </p>
  </div>
</template>