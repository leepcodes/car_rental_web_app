
<script setup lang="ts">
import { computed } from 'vue';
import { Heart, Star, MapPin, Users, Settings, Fuel, Car } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

interface Vehicle {
  id: number;
  name: string;
  type: string;
  image: string;
  price: number | { amount?: number; value?: number } | any;
  location: string | { name: string } | any;
  passengers: number | { count?: number; capacity?: number } | any;
  transmission: string | { type?: string; name?: string } | any;
  fuel: string | { type?: string; name?: string } | any;
  rating: number | { score?: number; average?: number; value?: number } | any;
  reviews: number | { count?: number; total?: number } | any;
  host: string | { name: string; verified?: boolean } | any;
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

// Helper function to safely extract numeric values
const extractNumber = (value: any, defaultValue: number = 0): number => {
  if (typeof value === 'number') return value;
  if (value && typeof value === 'object') {
    // Try common property names
    return value.value ?? value.amount ?? value.count ?? value.total ?? value.score ?? value.average ?? value.capacity ?? defaultValue;
  }
  return defaultValue;
};

// Helper function to safely extract string values
const extractString = (value: any, defaultValue: string = ''): string => {
  if (typeof value === 'string') return value;
  if (value && typeof value === 'object') {
    // Try common property names
    return value.name ?? value.type ?? value.value ?? defaultValue;
  }
  return defaultValue;
};

// Computed properties to handle both flat and nested data structures
const vehicleLocation = computed(() => {
  if (typeof props.vehicle.location === 'string') {
    return props.vehicle.location;
  }
  if (props.vehicle.location && typeof props.vehicle.location === 'object' && 'name' in props.vehicle.location) {
    return props.vehicle.location.name;
  }
  return 'Location Not Set';
});

const vehicleHost = computed(() => {
  if (typeof props.vehicle.host === 'string') {
    return props.vehicle.host;
  }
  if (props.vehicle.host && typeof props.vehicle.host === 'object' && 'name' in props.vehicle.host) {
    return props.vehicle.host.name;
  }
  return 'Unknown Host';
});

const isHostVerified = computed(() => {
  if (typeof props.vehicle.hostVerified === 'boolean') {
    return props.vehicle.hostVerified;
  }
  if (props.vehicle.host && typeof props.vehicle.host === 'object' && 'verified' in props.vehicle.host) {
    return props.vehicle.host.verified;
  }
  return false;
});

const vehiclePrice = computed(() => extractNumber(props.vehicle.price, 0));
const vehicleRating = computed(() => extractNumber(props.vehicle.rating, 0));
const vehicleReviews = computed(() => extractNumber(props.vehicle.reviews, 0));
const vehiclePassengers = computed(() => extractNumber(props.vehicle.passengers, 0));
const vehicleTransmission = computed(() => extractString(props.vehicle.transmission, 'Manual'));
const vehicleFuel = computed(() => extractString(props.vehicle.fuel, 'Gasoline'));
</script>

<template>
  <Card class="overflow-hidden hover:shadow-xl transition-shadow group bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 rounded-lg">
    <!-- Image Section -->
    <Link :href="`/client/booking/${vehicle.id}`" class="block">
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
            <CardTitle class="text-lg truncate text-white">{{ vehicle.name }}</CardTitle>
            <CardDescription class="flex items-center gap-1 mt-1 text-neutral-300">
              <MapPin class="w-3 h-3" />
              {{ vehicleLocation }}
            </CardDescription>
          </div>
        </div>

        <!-- Rating -->
        <div class="flex items-center gap-2 mt-2">
          <div class="flex items-center gap-1">
            <Star class="w-4 h-4 fill-amber-400 text-amber-400" />
            <span class="font-semibold text-sm text-white">{{ vehicleRating.toFixed(1) }}</span>
          </div>
          <span class="text-xs text-neutral-300">
            ({{ vehicleReviews }} reviews)
          </span>
        </div>
      </CardHeader>

      <CardContent class="pb-3">
        <!-- Host Info -->
        <div class="flex items-center gap-2 mb-3 pb-3 border-b border-white/20">
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
            <Car class="w-4 h-4 text-white" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate text-white">{{ vehicleHost }}</p>
            <p v-if="isHostVerified" class="text-xs text-green-400">✓ Verified Host</p>
          </div>
        </div>

        <!-- Vehicle Specs -->
        <div class="grid grid-cols-3 gap-2 text-center">
          <div class="flex flex-col items-center">
            <Users class="w-4 h-4 text-white/80 mb-1" />
            <span class="text-xs text-neutral-300">{{ vehiclePassengers }}</span>
          </div>
          <div class="flex flex-col items-center">
            <Settings class="w-4 h-4 text-white/80 mb-1" />
            <span class="text-xs text-neutral-300 truncate">{{ vehicleTransmission }}</span>
          </div>
          <div class="flex flex-col items-center">
            <Fuel class="w-4 h-4 text-white/80 mb-1" />
            <span class="text-xs text-neutral-300">{{ vehicleFuel }}</span>
          </div>
        </div>
      </CardContent>
    </Link>

    <CardFooter class="pt-0 flex items-center justify-between">
      <div>
        <div class="flex items-baseline gap-1">
          <span class="text-2xl font-bold text-white">₱{{ vehiclePrice }}</span>
          <span class="text-sm text-neutral-300">/day</span>
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