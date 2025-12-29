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
    <Card class="border-2 border-[#0081A7]/20 shadow-xl">
      <CardHeader class="bg-gradient-to-br from-[#0081A7] to-[#00AFB9] text-white">
        <div class="flex items-baseline gap-2">
          <span class="text-4xl font-bold font-['Roboto']">₱{{ price.toLocaleString() }}</span>
          <span class="text-lg">/day</span>
        </div>
        <CardDescription class="text-white/90">
          Best price guarantee
        </CardDescription>
      </CardHeader>
      
      <CardContent class="pt-6 space-y-4">
        <!-- Pickup Date & Time -->
        <div>
          <label class="block text-sm font-medium text-neutral-700 mb-2">
            <Calendar class="w-4 h-4 inline mr-1" />
            Pickup Date & Time
          </label>
          <div class="grid grid-cols-2 gap-2">
            <input
              v-model="selectedPickupDate"
              type="date"
              class="px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none text-sm"
              :min="new Date().toISOString().split('T')[0]"
            />
            <input
              v-model="selectedPickupTime"
              type="time"
              class="px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none text-sm"
            />
          </div>
        </div>

        <!-- Return Date & Time -->
        <div>
          <label class="block text-sm font-medium text-neutral-700 mb-2">
            <Calendar class="w-4 h-4 inline mr-1" />
            Return Date & Time
          </label>
          <div class="grid grid-cols-2 gap-2">
            <input
              v-model="selectedReturnDate"
              type="date"
              class="px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none text-sm"
              :min="selectedPickupDate || new Date().toISOString().split('T')[0]"
            />
            <input
              v-model="selectedReturnTime"
              type="time"
              class="px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none text-sm"
            />
          </div>
        </div>

        <!-- Price Breakdown -->
        <div v-if="totalDays > 0" class="pt-4 border-t space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-neutral-600">₱{{ price.toLocaleString() }} × {{ totalDays }} day{{ totalDays > 1 ? 's' : '' }}</span>
            <span class="font-semibold">₱{{ totalPrice.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-neutral-600">Service fee</span>
            <span class="font-semibold">₱{{ serviceFee.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between pt-2 border-t font-bold text-lg">
            <span>Total</span>
            <span class="text-[#0081A7]">₱{{ grandTotal.toLocaleString() }}</span>
          </div>
        </div>
      </CardContent>
      
      <CardFooter class="flex flex-col gap-2">
        <button
          @click="handleBooking"
          :disabled="!available"
          class="w-full py-3 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white rounded-lg font-semibold hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed font-['Roboto']"
        >
          {{ available ? 'Book Now' : 'Not Available' }}
        </button>
        <p class="text-xs text-center text-neutral-500">
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

<style scoped>
* {
  font-family: 'Roboto', sans-serif;
}
</style>