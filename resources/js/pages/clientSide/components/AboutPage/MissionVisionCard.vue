<!-- components/MissionVisionCard.vue -->
<script setup lang="ts">
defineProps<{
  icon: string;
  title: string;
  description: string;
  bgColor: string;
}>();
</script>

<template>
  <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
    <div :class="`w-16 h-16 ${bgColor} rounded-full flex items-center justify-center mb-6`">
      <span class="text-3xl">{{ icon }}</span>
    </div>
    <h2 class="text-3xl font-bold text-neutral-900 mb-4">{{ title }}</h2>
    <p class="text-lg text-neutral-600 leading-relaxed">
      {{ description }}
    </p>
  </div>
</template>