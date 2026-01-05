// components/VehicleCard.vue
<script setup lang="ts">
import { Heart, Star, MapPin, Users, Settings, Fuel, Car, XCircle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button/index';
import { Badge } from '@/components/ui/badge/index';

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
  active: boolean; // Changed from 'available' to 'active'
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
  <Card 
    :class="[
      'overflow-hidden transition-all duration-300 bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900',
      vehicle.active ? 'hover:shadow-xl group cursor-pointer' : 'opacity-75 cursor-not-allowed'
    ]"
  >
    <!-- Image Section -->
    <component 
      :is="vehicle.active ? Link : 'div'" 
      :href="vehicle.active ? `/client/booking/${vehicle.id}` : undefined"
      :class="vehicle.active ? 'block' : 'block pointer-events-none'"
    >
      <div class="relative aspect-[4/3] overflow-hidden">
        <img
          :src="vehicle.image"
          :alt="vehicle.name"
          :class="[
            'w-full h-full object-cover transition-transform duration-300',
            vehicle.active ? 'group-hover:scale-105' : 'grayscale-[50%]'
          ]"
        />
        
        <!-- Badges -->
        <div class="absolute top-3 left-3 flex flex-col gap-2">
          <!-- Inactive Badge - Priority -->
          <Badge v-if="!vehicle.active" class="bg-red-500 text-white hover:bg-red-600 shadow-lg">
            <XCircle class="w-3 h-3 mr-1" />
            Not Active
          </Badge>
          
          <!-- Type Badge -->
          <Badge v-else class="bg-white text-neutral-900 hover:bg-white shadow-md">
            {{ vehicle.type }}
          </Badge>
          
          <!-- Featured Badge -->
          <Badge v-if="vehicle.featured && vehicle.active" class="bg-amber-500 hover:bg-amber-600 shadow-md">
            ⭐ Featured
          </Badge>
        </div>

        <!-- Favorite Button - Only if active -->
        <Button
          v-if="vehicle.active"
          size="icon"
          variant="ghost"
          class="absolute top-3 right-3 bg-white/90 hover:bg-white z-10 shadow-lg hover:scale-110 transition-transform"
          @click.prevent="emit('toggleFavorite', vehicle.id)"
        >
          <Heart 
            :class="[
              'w-4 h-4',
              isFavorite ? 'fill-red-500 text-red-500' : 'text-neutral-600'
            ]"
          />
        </Button>
      </div>
    </component>

    <component 
      :is="vehicle.active ? Link : 'div'" 
      :href="vehicle.active ? `/client/booking/${vehicle.id}` : undefined"
      :class="vehicle.active ? 'block' : 'block pointer-events-none'"
    >
      <CardHeader class="pb-3">
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1 min-w-0">
            <CardTitle :class="[
              'text-lg truncate transition-colors',
              vehicle.active ? 'text-white group-hover:text-blue-400' : 'text-neutral-400'
            ]">
              {{ vehicle.name }}
            </CardTitle>
            <CardDescription class="flex items-center gap-1 mt-1 text-neutral-300">
              <MapPin class="w-3 h-3" />
              {{ vehicle.location }}
            </CardDescription>
          </div>
        </div>

        <!-- Rating - Only show if active -->
        <div v-if="vehicle.active" class="flex items-center gap-2 mt-2">
          <div class="flex items-center gap-1">
            <Star class="w-4 h-4 fill-amber-400 text-amber-400" />
            <span class="font-semibold text-sm text-white">{{ vehicle.rating }}</span>
          </div>
          <span class="text-xs text-neutral-300">
            ({{ vehicle.reviews }} reviews)
          </span>
        </div>
      </CardHeader>

      <CardContent class="pb-3">
        <!-- Inactive Message -->
        <div 
          v-if="!vehicle.active" 
          class="flex items-center justify-center p-6 bg-red-50 border border-red-200 rounded-lg"
        >
          <div class="text-center">
            <XCircle class="w-8 h-8 text-red-500 mx-auto mb-2" />
            <p class="text-sm font-semibold text-red-900">Vehicle Not Active</p>
            <p class="text-xs text-red-700 mt-1">This vehicle is currently inactive</p>
          </div>
        </div>

        <!-- Active Vehicle Details -->
        <template v-else>
          <!-- Host Info -->
          <div class="flex items-center gap-2 mb-3 pb-3 border-b">
            <div class="w-8 h-8 rounded-full bg-neutral-200 flex items-center justify-center">
              <Car class="w-4 h-4 text-neutral-600" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium truncate text-white">{{ vehicle.host }}</p>
              <p v-if="vehicle.hostVerified" class="text-xs text-green-400">✓ Verified Host</p>
            </div>
          </div>

          <!-- Vehicle Specs -->
          <div class="grid grid-cols-3 gap-2 text-center">
            <div class="flex flex-col items-center">
              <Users class="w-4 h-4 text-white mb-1" />
              <span class="text-xs text-neutral-300">{{ vehicle.passengers }}</span>
            </div>
            <div class="flex flex-col items-center">
              <Settings class="w-4 h-4 text-white mb-1" />
              <span class="text-xs text-neutral-300 truncate">{{ vehicle.transmission }}</span>
            </div>
            <div class="flex flex-col items-center">
              <Fuel class="w-4 h-4 text-white mb-1" />
              <span class="text-xs text-neutral-300">{{ vehicle.fuel }}</span>
            </div>
          </div>
        </template>
      </CardContent>
    </component>

    <CardFooter class="pt-0 flex items-center justify-between">
      <div>
        <div class="flex items-baseline gap-1">
          <span :class="[
            'text-2xl font-bold',
            vehicle.active ? 'bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent' : 'text-neutral-400'
          ]">
            ₱{{ vehicle.price.toLocaleString() }}
          </span>
          <span class="text-sm text-neutral-300">/day</span>
        </div>
      </div>
      <Button 
        size="sm"
        :disabled="!vehicle.active"
        :class="[
          'transition-all',
          vehicle.active 
            ? 'bg-gradient-to-r from-blue-400 to-cyan-400 hover:shadow-lg hover:scale-105 hover:from-blue-500 hover:to-cyan-500' 
            : 'bg-neutral-300 cursor-not-allowed'
        ]"
        @click.stop="vehicle.active && emit('book', vehicle.id)"
      >
        {{ vehicle.active ? 'Book Now' : 'Inactive' }}
      </Button>
    </CardFooter>
  </Card>
</template>