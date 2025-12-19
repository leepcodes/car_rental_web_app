<!-- VehicleCard.vue -->
<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

interface Vehicle {
  id?: number;
  model: string;
  name?: string;
  type: string;
  price: string;
  image: string;
  location?: string;
  passengers?: number;
  [key: string]: any; // Allow additional properties
}

defineProps<{
  vehicle: Vehicle;
}>();
</script>

<template>
  <Card class="group relative bg-white border border-neutral-200 hover:border-primary/50 transition-all duration-500 hover:scale-105 hover:shadow-xl overflow-hidden">
    <CardContent class="p-4 md:p-5">
      <!-- Vehicle Icon/Image -->
      <div class="text-4xl md:text-5xl mb-3 transform group-hover:scale-110 transition-transform duration-300 text-center">
        {{ vehicle.image }}
      </div>

      <!-- Vehicle Info -->
      <div class="text-center mb-3">
        <h4 class="text-base md:text-lg font-bold text-neutral-800 mb-1">
          {{ vehicle.model }}
        </h4>
        <p class="text-xs text-neutral-600">{{ vehicle.type }}</p>
      </div>

      <!-- Price & Button -->
      <div class="flex flex-col items-center gap-2">
        <span class="text-lg md:text-xl font-bold text-destructive">
          {{ vehicle.price }}
        </span>
        <Button 
          size="sm"
          class="w-full bg-destructive hover:bg-destructive/90 text-white rounded-md font-semibold transition-all duration-300"
        >
          Book Now
        </Button>
      </div>
    </CardContent>
  </Card>
</template>