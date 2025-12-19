<!-- components/ValuesSection.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import ValueCard from './ValueCard.vue';

const values = ref([
  {
    title: 'Innovation',
    description: 'We constantly push boundaries and embrace new technologies to deliver cutting-edge solutions.',
    icon: '💡'
  },
  {
    title: 'Quality',
    description: 'Excellence is not an act but a habit. We maintain the highest standards in everything we do.',
    icon: '⭐'
  },
  {
    title: 'Integrity',
    description: 'We build trust through transparency, honesty, and ethical business practices.',
    icon: '🤝'
  },
  {
    title: 'Collaboration',
    description: 'Together we achieve more. We value teamwork and foster a culture of mutual support.',
    icon: '🎯'
  }
]);
</script>

<template>
  <section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl md:text-4xl font-bold text-neutral-900 mb-12 text-center">
        Our Core Values
      </h2>
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
        <ValueCard
          v-for="value in values"
          :key="value.title"
          :icon="value.icon"
          :title="value.title"
          :description="value.description"
        />
      </div>
    </div>
  </section>
</template>