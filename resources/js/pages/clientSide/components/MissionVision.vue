<!-- components/MissionVision.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import MissionVisionCard from './MissionVisionCard.vue';

const cards = ref([
  {
    icon: '🎯',
    title: 'Our Mission',
    description: 'To empower businesses and individuals with innovative technology solutions that drive growth, efficiency, and success. We believe in making technology accessible and impactful for everyone.',
    bgColor: 'bg-blue-100'
  },
  {
    icon: '🚀',
    title: 'Our Vision',
    description: 'To be the leading catalyst for digital transformation, creating a future where technology seamlessly integrates with human potential to solve the world\'s most challenging problems.',
    bgColor: 'bg-purple-100'
  }
]);
</script>

<template>
  <section class="py-16 md:py-48">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid md:grid-cols-2 gap-12">
        <MissionVisionCard
          v-for="card in cards"
          :key="card.title"
          :icon="card.icon"
          :title="card.title"
          :description="card.description"
          :bg-color="card.bgColor"
        />
      </div>
    </div>
  </section>
</template>