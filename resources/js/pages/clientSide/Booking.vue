// pages/booking.vue
<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import VehicleCard from '@/pages/clientSide/components/VehicleCard.vue';
import SearchFilters from '@/pages/clientSide/components/SearchForm.vue';
import { Button } from '@/components/ui/button';
withDefaults(
  defineProps<{
    canRegister?: boolean;
  }>(),
  { canRegister: true }
);
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

const favorites = ref<Set<number>>(new Set());
const sortBy = ref('featured');
const filterType = ref('all');
const searchQuery = ref('');

const vehicles = ref<Vehicle[]>([
  {
    id: 1,
    name: 'Toyota Camry 2023',
    type: 'Sedan',
    image: 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=600&h=400&fit=crop',
    price: 45,
    location: 'Quezon City',
    passengers: 5,
    transmission: 'Automatic',
    fuel: 'Gasoline',
    rating: 4.8,
    reviews: 127,
    host: 'Premium Rentals',
    hostVerified: true,
    available: true,
    featured: true
  },
  {
    id: 2,
    name: 'Honda CR-V 2022',
    type: 'SUV',
    image: 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=600&h=400&fit=crop',
    price: 3000,
    location: 'Makati',
    passengers: 7,
    transmission: 'Automatic',
    fuel: 'Gasoline',
    rating: 4.9,
    reviews: 89,
    host: 'Elite Cars',
    hostVerified: true,
    available: true,
    featured: true
  },
  {
    id: 3,
    name: 'Toyota Wigo 2021',
    type: 'Hatchback',
    image: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=600&h=400&fit=crop',
    price: 1500,
    location: 'Manila',
    passengers: 5,
    transmission: 'Manual',
    fuel: 'Gasoline',
    rating: 4.6,
    reviews: 203,
    host: 'Budget Wheels',
    hostVerified: false,
    available: true,
    featured: false
  },
  {
    id: 4,
    name: 'Toyota Innova 2023',
    type: 'MPV',
    image: 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=600&h=400&fit=crop',
    price: 2000,
    location: 'Pasig',
    passengers: 8,
    transmission: 'Automatic',
    fuel: 'Diesel',
    rating: 4.7,
    reviews: 156,
    host: 'Family Rides',
    hostVerified: true,
    available: false,
    featured: false
  },
  {
    id: 5,
    name: 'Mitsubishi L300 2020',
    type: 'Van',
    image: 'https://images.unsplash.com/photo-1527786356703-4b100091cd2c?w=600&h=400&fit=crop',
    price: 3500,
    location: 'Mandaluyong',
    passengers: 12,
    transmission: 'Manual',
    fuel: 'Diesel',
    rating: 4.5,
    reviews: 78,
    host: 'Group Transport',
    hostVerified: true,
    available: true,
    featured: false
  },
  {
    id: 6,
    name: 'Ford Ranger Wildtrak',
    type: 'Pickup',
    image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&h=400&fit=crop',
    price: 3500,
    location: 'Taguig',
    passengers: 5,
    transmission: 'Automatic',
    fuel: 'Diesel',
    rating: 4.9,
    reviews: 94,
    host: 'Adventure Rentals',
    hostVerified: true,
    available: true,
    featured: true
  },
  {
    id: 7,
    name: 'Hyundai Accent 2022',
    type: 'Sedan',
    image: 'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=600&h=400&fit=crop',
    price: 2000,
    location: 'Quezon City',
    passengers: 5,
    transmission: 'Automatic',
    fuel: 'Gasoline',
    rating: 4.7,
    reviews: 112,
    host: 'City Drives',
    hostVerified: true,
    available: true,
    featured: false
  },
  {
    id: 8,
    name: 'Mazda CX-5 2023',
    type: 'SUV',
    image: 'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=600&h=400&fit=crop',
    price: 3000,
    location: 'BGC',
    passengers: 5,
    transmission: 'Automatic',
    fuel: 'Gasoline',
    rating: 4.8,
    reviews: 145,
    host: 'Luxury Fleet',
    hostVerified: true,
    available: true,
    featured: true
  }
]);

const toggleFavorite = (id: number) => {
  if (favorites.value.has(id)) {
    favorites.value.delete(id);
  } else {
    favorites.value.add(id);
  }
};

const handleBook = (id: number) => {
  const vehicle = vehicles.value.find(v => v.id === id);
  console.log('Booking vehicle:', vehicle?.name);
  // Navigate to booking details page or open modal
};

const handleSearch = (query: string) => {
  searchQuery.value = query;
};

const filteredAndSortedVehicles = computed(() => {
  let result = vehicles.value;

  // Filter by type
  if (filterType.value !== 'all') {
    result = result.filter(v => v.type.toLowerCase() === filterType.value.toLowerCase());
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(v => 
      v.name.toLowerCase().includes(query) || 
      v.location.toLowerCase().includes(query)
    );
  }

  // Sort
  result = [...result].sort((a, b) => {
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
</script>

<template>
<Head title="Booking" />
<AppHeader :can-register="canRegister" />
  <div class="min-h-screen bg-neutral-50">
    <!-- Header Section -->
    <div class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 pt-26 pb-12">
        <div class="flex flex-col gap-4">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-neutral-900 mb-2">
              Find Your Perfect Ride
            </h1>
            <p class="text-neutral-600">
              Discover {{ vehicles.length }} vehicles available for rent in Metro Manila
            </p>
          </div>

          <!-- Search and Filters -->
          <SearchFilters 
            v-model:filter-type="filterType"
            v-model:sort-by="sortBy"
            @search="handleSearch"
          />
        </div>
      </div>
    </div>

    <!-- Listings Grid -->
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8">
      <div v-if="filteredAndSortedVehicles.length === 0" class="text-center py-12">
        <p class="text-neutral-600 text-lg">No vehicles found matching your criteria.</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <VehicleCard
          v-for="vehicle in filteredAndSortedVehicles"
          :key="vehicle.id"
          :vehicle="vehicle"
          :is-favorite="favorites.has(vehicle.id)"
          @toggle-favorite="toggleFavorite"
          @book="handleBook"
        />
      </div>

      <!-- Load More -->
      <div class="mt-12 text-center">
        <Button variant="outline" size="lg">
          Load More Vehicles
        </Button>
      </div>
    </div>
  </div>
</template>