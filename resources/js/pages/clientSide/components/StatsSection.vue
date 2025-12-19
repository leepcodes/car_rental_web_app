<!-- components/StatsSection.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import StatCard from './StatCard.vue';

const stats = ref([
  { label: 'Years of Experience', value: '10+' },
  { label: 'Happy Clients', value: '500+' },
  { label: 'Projects Completed', value: '1,000+' },
  { label: 'Team Members', value: '50+' }
]);
</script>

<template>
  <section class="py-16 bg-neutral-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        <StatCard
          v-for="stat in stats"
          :key="stat.label"
          :value="stat.value"
          :label="stat.label"
        />
      </div>
    </div>
  </section>
</template>