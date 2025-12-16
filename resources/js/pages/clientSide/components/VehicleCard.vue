
// components/VehicleCard.vue
<script setup lang="ts">
import { Heart, Star, MapPin, Users, Settings, Fuel, Car } from 'lucide-react';
import { Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

interface Vehicle {
  id: number;
  name: string;
  type: string;
  image: string;
  price: number;
  location: string;
  passengers: number;
  transmission: string;
  fuel: string;
  rating: number;
  reviews: number;
  host: string;
  hostVerified: boolean;
  available: boolean;
  featured: boolean;
}

interface Props {
  vehicle: Vehicle;
  isFavorite: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  toggleFavorite: [id: number];
  book: [id: number];
}>();
</script>

<template>
  <Card class="overflow-hidden hover:shadow-xl transition-shadow group">
    <!-- Image Section -->
    <Link :href="`/client/booking/otp/${vehicle.id}`" class="block">
      <div class="relative aspect-[4/3] overflow-hidden">
        <img
          :src="vehicle.image"
          :alt="vehicle.name"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        />
        
        <!-- Badges -->
        <div class="absolute top-3 left-3 flex gap-2">
          <Badge class="bg-white text-neutral-900 hover:bg-white">
            {{ vehicle.type }}
          </Badge>
          <Badge v-if="vehicle.featured" class="bg-amber-500 hover:bg-amber-600">
            Featured
          </Badge>
        </div>

        <!-- Favorite Button -->
        <Button
          size="icon"
          variant="ghost"
          class="absolute top-3 right-3 bg-white/90 hover:bg-white z-10"
          @click.prevent="emit('toggleFavorite', vehicle.id)"
        >
          <Heart 
            :class="[
              'w-4 h-4',
              isFavorite ? 'fill-red-500 text-red-500' : 'text-neutral-600'
            ]"
          />
        </Button>

        <!-- Unavailable Overlay -->
        <div v-if="!vehicle.available" class="absolute inset-0 bg-black/60 flex items-center justify-center">
          <Badge variant="destructive" class="text-sm">
            Not Available
          </Badge>
        </div>
      </div>
    </Link>

    <Link :href="`/client/booking/otp/${vehicle.id}`" class="block">
      <CardHeader class="pb-3">
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1 min-w-0">
            <CardTitle class="text-lg truncate">{{ vehicle.name }}</CardTitle>
            <CardDescription class="flex items-center gap-1 mt-1">
              <MapPin class="w-3 h-3" />
              {{ vehicle.location }}
            </CardDescription>
          </div>
        </div>

        <!-- Rating -->
        <div class="flex items-center gap-2 mt-2">
          <div class="flex items-center gap-1">
            <Star class="w-4 h-4 fill-amber-400 text-amber-400" />
            <span class="font-semibold text-sm">{{ vehicle.rating }}</span>
          </div>
          <span class="text-xs text-neutral-500">
            ({{ vehicle.reviews }} reviews)
          </span>
        </div>
      </CardHeader>

      <CardContent class="pb-3">
        <!-- Host Info -->
        <div class="flex items-center gap-2 mb-3 pb-3 border-b">
          <div class="w-8 h-8 rounded-full bg-neutral-200 flex items-center justify-center">
            <Car class="w-4 h-4 text-neutral-600" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate">{{ vehicle.host }}</p>
            <p v-if="vehicle.hostVerified" class="text-xs text-green-600">✓ Verified Host</p>
          </div>
        </div>

        <!-- Vehicle Specs -->
        <div class="grid grid-cols-3 gap-2 text-center">
          <div class="flex flex-col items-center">
            <Users class="w-4 h-4 text-neutral-500 mb-1" />
            <span class="text-xs text-neutral-600">{{ vehicle.passengers }}</span>
          </div>
          <div class="flex flex-col items-center">
            <Settings class="w-4 h-4 text-neutral-500 mb-1" />
            <span class="text-xs text-neutral-600 truncate">{{ vehicle.transmission }}</span>
          </div>
          <div class="flex flex-col items-center">
            <Fuel class="w-4 h-4 text-neutral-500 mb-1" />
            <span class="text-xs text-neutral-600">{{ vehicle.fuel }}</span>
          </div>
        </div>
      </CardContent>
    </Link>

    <CardFooter class="pt-0 flex items-center justify-between">
      <div>
        <div class="flex items-baseline gap-1">
          <span class="text-2xl font-bold">₱{{ vehicle.price }}</span>
          <span class="text-sm text-neutral-500">/day</span>
        </div>
      </div>
      <Button 
        size="sm"
        :disabled="!vehicle.available"
        @click.stop="emit('book', vehicle.id)"
      >
        {{ vehicle.available ? 'Book Now' : 'Unavailable' }}
      </Button>
    </CardFooter>
  </Card>
</template>