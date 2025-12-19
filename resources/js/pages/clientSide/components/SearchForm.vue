<!-- SearchForm.vue Component -->
<script setup lang="ts">
import { ref } from 'vue';

const vehicleType = ref('');
const pickupDate = ref('');
const returnDate = ref('');

const handleSearch = () => {
  console.log('Search:', { vehicleType: vehicleType.value, pickupDate: pickupDate.value, returnDate: returnDate.value });
};
</script>

<template>
  <form @submit.prevent="handleSearch" class="bg-white rounded-3xl border border-neutral-200 p-8 shadow-lg shadow-neutral-900/5">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <div>
        <label for="vehicle" class="text-xs font-semibold text-neutral-700 mb-3 block uppercase tracking-wide">Vehicle</label>
        <select
          id="vehicle"
          v-model="vehicleType"
          class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-400 focus:bg-white focus:border-neutral-400 transition-all"
        >
          <option value="">All Types</option>
          <option value="sedan">Sedan</option>
          <option value="suv">SUV</option>
          <option value="van">Van</option>
        </select>
      </div>

      <div>
        <label for="pickup" class="text-xs font-semibold text-neutral-700 mb-3 block uppercase tracking-wide">Pickup</label>
        <input
          id="pickup"
          v-model="pickupDate"
          type="datetime-local"
          class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-400 focus:bg-white focus:border-neutral-400 transition-all"
        />
      </div>

      <div>
        <label for="return" class="text-xs font-semibold text-neutral-700 mb-3 block uppercase tracking-wide">Return</label>
        <input
          id="return"
          v-model="returnDate"
          type="datetime-local"
          class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm text-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-400 focus:bg-white focus:border-neutral-400 transition-all"
        />
      </div>

      <button
        type="submit"
        class="bg-neutral-800 text-white rounded-xl hover:bg-neutral-700 active:scale-[0.98] transition-all font-semibold mt-auto py-3.5 shadow-md shadow-neutral-900/10"
      >
        Search
      </button>
    </div>
  </form>
</template>