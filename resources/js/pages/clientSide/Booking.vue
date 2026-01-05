// pages/booking.vue
<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import VehicleCard from '@/pages/clientSide/components/VehicleCard.vue';
import SearchFilters from '@/pages/clientSide/components/SearchForm.vue';
import { Button } from '@/components/ui/button';

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
  active: boolean;
  featured: boolean;
}

interface Filters {
  body_type?: string;
  fuel_type?: string;
  transmission?: string;
  seating_capacity?: number;
  search?: string;
  [key: string]: string | number | undefined;
}

interface Props {
  canRegister?: boolean;
  vehicles: Vehicle[];
  filters?: Filters;
}

const props = withDefaults(defineProps<Props>(), { 
  canRegister: true,
  vehicles: () => [],
  filters: () => ({})
});

const favorites = ref<Set<number>>(new Set());
const sortBy = ref('featured');
const showInactive = ref(true);

const toggleFavorite = (id: number) => {
  if (favorites.value.has(id)) {
    favorites.value.delete(id);
  } else {
    favorites.value.add(id);
  }
};

const handleBook = (id: number) => {
  const vehicle = props.vehicles.find(v => v.id === id);
  
  if (!vehicle?.active) {
    alert('This vehicle is currently inactive and cannot be booked.');
    return;
  }
  
  console.log('Booking vehicle:', vehicle?.name);
  router.visit(`/client/booking/${id}`);
};

const sortedVehicles = computed(() => {
  let result = [...props.vehicles];

  // Filter inactive if toggle is off
  if (!showInactive.value) {
    result = result.filter(v => v.active);
  }

  // Sort
  result.sort((a, b) => {
    // Always put inactive vehicles last
    if (a.active !== b.active) {
      return a.active ? -1 : 1;
    }

    // Then sort by selected criteria
    switch(sortBy.value) {
      case 'price-low':
        return a.price - b.price;
      case 'price-high':
        return b.price - a.price;
      case 'rating':
        return b.rating - a.rating;
      case 'featured':
      default:
        return (b.featured ? 1 : 0) - (a.featured ? 1 : 0);
    }
  });

  return result;
});

const activeCount = computed(() => {
  return props.vehicles.filter(v => v.active).length;
});

const inactiveCount = computed(() => {
  return props.vehicles.filter(v => !v.active).length;
});
</script>

<template>
<Head title="Booking" />
<AppHeader :can-register="canRegister" />
  <div class="min-h-screen bg-neutral-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-[#0081A7] to-[#00AFB9] border-b">
      <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 pt-26 pb-12">
        <div class="flex flex-col gap-4">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
              Find Your Perfect Ride
            </h1>

            <p class="text-white/90">
              {{ activeCount }} active vehicle{{ activeCount !== 1 ? 's' : '' }}{{ inactiveCount > 0 ? `, ${inactiveCount} inactive` : '' }}
            </p>
          </div>

          <!-- Search and Filters -->
          <SearchFilters :filters="filters" />
        </div>
      </div>
    </div>

    <!-- Listings Grid -->
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8">
      <!-- Active Filters Display -->
      <div v-if="filters && Object.keys(filters).some(key => filters[key])" class="mb-6">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="text-sm font-medium text-neutral-600">Active Filters:</span>
          <span v-if="filters.body_type" class="inline-flex items-center gap-1 px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-sm">
            Body: {{ filters.body_type }}
          </span>
          <span v-if="filters.fuel_type" class="inline-flex items-center gap-1 px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-sm">
            Fuel: {{ filters.fuel_type }}
          </span>
          <span v-if="filters.transmission" class="inline-flex items-center gap-1 px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-sm">
            Trans: {{ filters.transmission }}
          </span>
          <span v-if="filters.seating_capacity" class="inline-flex items-center gap-1 px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-sm">
            Seats: {{ filters.seating_capacity }}
          </span>
          <span v-if="filters.search" class="inline-flex items-center gap-1 px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-sm">
            Search: "{{ filters.search }}"
          </span>
        </div>
      </div>

      <!-- Sort and Filter Options -->
      <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-4">
          <p class="text-sm text-neutral-600">
            Showing {{ sortedVehicles.length }} {{ sortedVehicles.length === 1 ? 'vehicle' : 'vehicles' }}
          </p>
          
          <!-- Toggle for showing inactive -->
          <label class="flex items-center gap-2 cursor-pointer">
            <input 
              type="checkbox" 
              v-model="showInactive"
              class="w-4 h-4 text-[#0081A7] border-neutral-300 rounded focus:ring-[#0081A7]"
            />
            <span class="text-sm text-neutral-600">Show inactive vehicles</span>
          </label>
        </div>
        
        <div class="flex items-center gap-2">
          <label for="sort" class="text-sm text-neutral-600">Sort by:</label>
          <select
            id="sort"
            v-model="sortBy"
            class="px-3 py-1.5 bg-white border border-neutral-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-neutral-400"
          >
            <option value="featured">Featured</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="rating">Highest Rated</option>
          </select>
        </div>
      </div>

      <div v-if="sortedVehicles.length === 0" class="text-center py-12">
        <p class="text-neutral-600 text-lg">No vehicles found matching your criteria.</p>
        <p class="text-neutral-500 text-sm mt-2">Try adjusting your filters or search terms.</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <VehicleCard
          v-for="vehicle in sortedVehicles"
          :key="vehicle.id"
          :vehicle="vehicle"
          :is-favorite="favorites.has(vehicle.id)"
          @toggle-favorite="toggleFavorite"
          @book="handleBook"
        />
      </div>

      <!-- Load More -->
      <div v-if="sortedVehicles.length > 0" class="mt-12 text-center">
        <Button variant="outline" size="lg">
          Load More Vehicles
        </Button>
      </div>
    </div>
  </div>
</template>