<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Calendar, CheckCircle } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from '@/components/ui/card';

interface Props {
  vehicleId: number;
  price: number;
  available: boolean;
}

const props = defineProps<Props>();

const selectedPickupDate = ref('');
const selectedReturnDate = ref('');
const selectedPickupTime = ref('09:00');
const selectedReturnTime = ref('09:00');

const totalDays = computed(() => {
  if (!selectedPickupDate.value || !selectedReturnDate.value) return 0;
  const pickup = new Date(selectedPickupDate.value);
  const returnDate = new Date(selectedReturnDate.value);
  const diffTime = Math.abs(returnDate.getTime() - pickup.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays || 1;
});

const totalPrice = computed(() => {
  return props.price * totalDays.value;
});

const serviceFee = computed(() => {
  return totalPrice.value * 0.05;
});

const grandTotal = computed(() => {
  return totalPrice.value + serviceFee.value;
});

const handleBooking = () => {
  if (!selectedPickupDate.value || !selectedReturnDate.value) {
    alert('Please select pickup and return dates');
    return;
  }
  
  // Use GET request with query parameters
  router.get(`/client/booking/${props.vehicleId}/form`, {
    pickup_date: selectedPickupDate.value,
    return_date: selectedReturnDate.value,
    pickup_time: selectedPickupTime.value,
    return_time: selectedReturnTime.value,
  });
};
</script>

<template>
  <div class="sticky top-24">
    <Card class="border-2 border-neutral-600 shadow-xl bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 overflow-hidden">
      <CardHeader class="bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 text-white">
        <div class="flex items-baseline gap-2">
          <span class="text-4xl font-bold font-['Roboto']">₱{{ price.toLocaleString() }}</span>
          <span class="text-lg">/day</span>
        </div>
        <CardDescription class="text-white/90">
          Best price guarantee
        </CardDescription>
      </CardHeader>
      
      <CardContent class="pt-6 space-y-4 bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
        <!-- Pickup Date & Time -->
        <div>
          <label class="block text-sm font-medium text-white mb-2">
            <Calendar class="w-4 h-4 inline mr-1 text-white" />
            Pickup Date & Time
          </label>
          <div class="grid grid-cols-2 gap-2">
            <input
              v-model="selectedPickupDate"
              type="date"
              class="px-3 py-2 bg-neutral-800 border border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none text-sm text-white"
              :min="new Date().toISOString().split('T')[0]"
            />
            <input
              v-model="selectedPickupTime"
              type="time"
              class="px-3 py-2 bg-neutral-800 border border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none text-sm text-white"
            />
          </div>
        </div>

        <!-- Return Date & Time -->
        <div>
          <label class="block text-sm font-medium text-white mb-2">
            <Calendar class="w-4 h-4 inline mr-1 text-white" />
            Return Date & Time
          </label>
          <div class="grid grid-cols-2 gap-2">
            <input
              v-model="selectedReturnDate"
              type="date"
              class="px-3 py-2 bg-neutral-800 border border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none text-sm text-white"
              :min="selectedPickupDate || new Date().toISOString().split('T')[0]"
            />
            <input
              v-model="selectedReturnTime"
              type="time"
              class="px-3 py-2 bg-neutral-800 border border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none text-sm text-white"
            />
          </div>
        </div>

        <!-- Price Breakdown -->
        <div v-if="totalDays > 0" class="pt-4 border-t border-white/20 space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-white/80">₱{{ price.toLocaleString() }} × {{ totalDays }} day{{ totalDays > 1 ? 's' : '' }}</span>
            <span class="font-semibold text-white">₱{{ totalPrice.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-white/80">Service fee</span>
            <span class="font-semibold text-white">₱{{ serviceFee.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between pt-2 border-t border-white/20 font-bold text-lg">
            <span class="text-white">Total</span>
            <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">₱{{ grandTotal.toLocaleString() }}</span>
          </div>
        </div>
      </CardContent>
      
      <CardFooter class="flex flex-col gap-2 bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
        <button
          @click="handleBooking"
          :disabled="!available"
          class="w-full py-3 bg-gradient-to-r from-blue-400 to-cyan-400 text-white rounded-lg font-bold text-lg hover:shadow-xl hover:from-blue-500 hover:to-cyan-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-['Roboto']"
        >
          {{ available ? 'Book Now' : 'Not Available' }}
        </button>
        <p class="text-xs text-center text-white/70">
          You won't be charged yet
        </p>
      </CardFooter>
    </Card>

    <!-- Quick Info -->
    <Card class="mt-4 bg-neutral-50">
      <CardContent class="pt-6">
        <div class="space-y-3 text-sm">
          <div class="flex items-center gap-2 text-neutral-700">
            <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
            <span>Free cancellation within 24 hours</span>
          </div>
          <div class="flex items-center gap-2 text-neutral-700">
            <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
            <span>Instant booking confirmation</span>
          </div>
          <div class="flex items-center gap-2 text-neutral-700">
            <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
            <span>24/7 customer support</span>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<style>
* {
  font-family: 'Roboto', sans-serif;
}

/* Style date and time input calendar icons to white */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
  filter: invert(100%) brightness(100%);
  cursor: pointer;
  opacity: 1 !important;
  width: 20px;
  height: 20px;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover,
input[type="time"]::-webkit-calendar-picker-indicator:hover {
  filter: invert(100%) brightness(120%);
}

/* For Firefox */
input[type="date"],
input[type="time"] {
  color-scheme: dark;
}

/* Ensure icons are visible on webkit browsers */
input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-clear-button {
  display: none;
}

input[type="time"]::-webkit-inner-spin-button,
input[type="time"]::-webkit-clear-button {
  display: none;
}
</style>