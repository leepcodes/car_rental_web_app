<!-- VehicleCard.vue -->
<script setup lang="ts">
defineProps<{
  name: string;
  image: string;
}>();
</script>

<template>
  <div class="group cursor-pointer">
    <div class="aspect-square bg-white rounded-2xl p-8 mb-4 flex items-center justify-center group-hover:bg-neutral-100 group-hover:shadow-md group-hover:shadow-neutral-900/5 transition-all duration-300 border border-neutral-200">
      <img :src="image" :alt="name" class="w-full h-full object-contain" />
    </div>
    <p class="text-sm font-semibold text-neutral-700 text-center tracking-wide">{{ name }}</p>
  </div>
</template>