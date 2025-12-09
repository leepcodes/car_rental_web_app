<!-- VehiclesSection.vue Component -->
<script setup lang="ts">
import VehicleCard from '@/pages/clientSide/components/VehicleCard.vue';
import ProcessStep from '@/pages/clientSide/components/ProcessStep.vue';

const vehicleTypes = [
  { name: 'Sedan', image: '/images/SEDAN.png' },
  { name: 'SUV', image: '/images/SUV.png' },
  { name: 'Hatchback', image: '/images/HATCHBACK.png' },
  { name: 'MPV', image: '/images/MPV.png' },
  { name: 'Pickup', image: '/images/L300.webp' },
  { name: 'Van', image: '/images/VAN.png' },
];

const steps = [
  { number: 1, title: 'Search', description: 'Find your perfect vehicle' },
  { number: 2, title: 'Book', description: 'Reserve online in seconds' },
  { number: 3, title: 'Drive', description: 'Pick up and hit the road' },
];
</script>

<template>
  <section class="py-32 px-6 lg:px-8 bg-neutral-50">
    <div class="mx-auto max-w-7xl">
      <!-- Header -->
      <div class="text-center max-w-3xl mx-auto mb-24">
        <h2 class="text-5xl md:text-6xl font-bold tracking-tight text-neutral-800 mb-6 leading-tight">
          Our Fleet
        </h2>
        <p class="text-xl text-neutral-500 leading-relaxed">
          Choose from a diverse range of well-maintained vehicles
        </p>
      </div>

      <!-- Vehicle Grid -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 mb-32">
        <VehicleCard
          v-for="vehicle in vehicleTypes"
          :key="vehicle.name"
          :name="vehicle.name"
          :image="vehicle.image"
        />
      </div>

      <!-- How It Works -->
      <div class="grid md:grid-cols-3 gap-12 lg:gap-16 mb-20">
        <ProcessStep
          v-for="step in steps"
          :key="step.number"
          :number="step.number"
          :title="step.title"
          :description="step.description"
        />
      </div>

      <!-- CTA -->
      <div class="text-center">
        <button class="px-10 py-4 bg-neutral-800 text-white rounded-full hover:bg-neutral-700 active:scale-[0.98] transition-all font-semibold text-base shadow-md shadow-neutral-900/10">
          Start Booking
        </button>
      </div>
    </div>
  </section>
</template>
