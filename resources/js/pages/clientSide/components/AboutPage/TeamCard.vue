<!-- components/TeamCard.vue -->
<script setup lang="ts">
defineProps<{
  name: string;
  role: string;
  image: string;
}>();
</script>

<template>
  <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <img 
      :src="image" 
      :alt="name"
      class="w-full h-64 object-cover"
    />
    <div class="p-6 text-center">
      <h3 class="text-xl font-bold text-neutral-900 mb-1">
        {{ name }}
      </h3>
      <p class="text-neutral-400 font-medium">
        {{ role }}
      </p>
    </div>
  </div>
</template>
