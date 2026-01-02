<!-- SearchForm.vue Component -->
<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

interface Props {
  filters?: {
    body_type?: string;
    fuel_type?: string;
    transmission?: string;
    seating_capacity?: number;
    search?: string;
  }
}

const props = defineProps<Props>();

// Initialize with existing filters if any
const bodyType = ref(props.filters?.body_type || '');
const fuelType = ref(props.filters?.fuel_type || '');
const transmission = ref(props.filters?.transmission || '');
const seatingCapacity = ref(props.filters?.seating_capacity || '');
const searchQuery = ref(props.filters?.search || '');

const handleSearch = () => {
  router.get('/client/booking', {
    body_type: bodyType.value || undefined,
    fuel_type: fuelType.value || undefined,
    transmission: transmission.value || undefined,
    seating_capacity: seatingCapacity.value || undefined,
    search: searchQuery.value || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const handleReset = () => {
  bodyType.value = '';
  fuelType.value = '';
  transmission.value = '';
  seatingCapacity.value = '';
  searchQuery.value = '';
  
  router.get('/client/booking', {}, {
    preserveState: true,
    preserveScroll: true,
  });
};
</script>

<template>
  <form @submit.prevent="handleSearch" class="bg-white rounded-3xl border border-neutral-200 p-8 shadow-lg shadow-neutral-900/5">
    <!-- First Row: Main Filters -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
      <!-- Body Type Filter -->
      <div>
        <label for="bodyType" class="text-xs font-semibold text-neutral-700 mb-3 block uppercase tracking-wide">
          Body Type
        </label>
        <select
          id="bodyType"
          v-model="bodyType"
          class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-700 focus:outline-none focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] transition-all"
        >
          <option value="">All Types</option>
          <option value="Sedan">Sedan</option>
          <option value="SUV">SUV</option>
          <option value="Van">Van</option>
          <option value="Hatchback">Hatchback</option>
          <option value="MPV">MPV</option>
          <option value="Pickup">Pickup</option>
        </select>
      </div>

      <!-- Fuel Type Filter -->
      <div>
        <label for="fuelType" class="text-xs font-semibold text-neutral-700 mb-3 block uppercase tracking-wide">
          Fuel Type
        </label>
        <select
          id="fuelType"
          v-model="fuelType"
          class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-700 focus:outline-none focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] transition-all"
        >
          <option value="">All Fuel Types</option>
          <option value="Gasoline">Gasoline</option>
          <option value="Diesel">Diesel</option>
          <option value="Electric">Electric</option>
          <option value="Hybrid">Hybrid</option>
        </select>
      </div>

      <!-- Transmission Filter -->
      <div>
        <label for="transmission" class="text-xs font-semibold text-neutral-700 mb-3 block uppercase tracking-wide">
          Transmission
        </label>
        <select
          id="transmission"
          v-model="transmission"
          class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-700 focus:outline-none focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] transition-all"
        >
          <option value="">All Transmissions</option>
          <option value="Automatic">Automatic</option>
          <option value="Manual">Manual</option>
        </select>
      </div>

      <!-- Seating Capacity Filter -->
      <div>
        <label for="seatingCapacity" class="text-xs font-semibold text-neutral-700 mb-3 block uppercase tracking-wide">
          Seats
        </label>
        <select
          id="seatingCapacity"
          v-model="seatingCapacity"
          class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-700 focus:outline-none focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] transition-all"
        >
          <option value="">All Capacities</option>
          <option value="4">4 Seats</option>
          <option value="5">5 Seats</option>
          <option value="6">6 Seats</option>
          <option value="7">7 Seats</option>
          <option value="8">8+ Seats</option>
        </select>
      </div>
    </div>

    <!-- Second Row: Search and Action Buttons -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto_auto] gap-5">
      <!-- Search Input -->
      <div>
        <label for="search" class="text-xs font-semibold text-neutral-700 mb-3 block uppercase tracking-wide">
          Search
        </label>
        <input
          id="search"
          v-model="searchQuery"
          type="text"
          placeholder="Search by brand, model, year, or color..."
          class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-700 placeholder:text-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] transition-all"
        />
      </div>

      <!-- Search Button -->
      <button
        type="submit"
        class="bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white rounded-xl hover:from-[#0081A7]/90 hover:to-[#00AFB9]/90 active:scale-[0.98] transition-all font-semibold mt-auto py-3.5 px-8 shadow-md shadow-neutral-900/10"
      >
        Search
      </button>

      <!-- Reset Button -->
      <button
        type="button"
        @click="handleReset"
        class="bg-white text-[#0081A7] border-2 border-[#0081A7] rounded-xl hover:bg-[#0081A7] hover:text-white active:scale-[0.98] transition-all font-semibold mt-auto py-3.5 px-6"
      >
        Reset
      </button>
    </div>
  </form>
</template>