<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import PaymentGatewayModal from '@/pages/clientSide/components/PaymentGatewayModal.vue';
import { 
  ArrowLeft,
  Calendar,
  Clock,
  MapPin,
  CreditCard,
  CheckCircle,
  Shield,
  AlertCircle,
  Navigation,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

const props = withDefaults(
  defineProps<{
    canRegister?: boolean;
    vehicleId?: string | number;
    vehicleName?: string;
    vehicleImage?: string;
    vehicleType?: string;
    pricePerDay?: string | number;
    pickupDate?: string;
    returnDate?: string;
    pickupTime?: string;
    returnTime?: string;
    totalDays?: string | number;
    subtotal?: string | number;
    serviceFee?: string | number;
    totalPrice?: string | number;
    operatorId?: string | number;
  }>(),
  { 
    canRegister: true
  }
);

// Mock location data
const mockLocation = {
  address: "SM Mall of Asia, Seaside Blvd, Pasay, Metro Manila",
  lat: 14.5352,
  lng: 120.9818,
};

// Convert string props to numbers for calculations
const vehicleIdNum = computed(() => typeof props.vehicleId === 'string' ? parseInt(props.vehicleId) : props.vehicleId);
const pricePerDayNum = computed(() => typeof props.pricePerDay === 'string' ? parseFloat(props.pricePerDay) : props.pricePerDay);
const totalDaysNum = computed(() => typeof props.totalDays === 'string' ? parseInt(props.totalDays) : props.totalDays);
const subtotalNum = computed(() => typeof props.subtotal === 'string' ? parseFloat(props.subtotal) : props.subtotal);
const serviceFeeNum = computed(() => typeof props.serviceFee === 'string' ? parseFloat(props.serviceFee) : props.serviceFee);
const totalPriceNum = computed(() => typeof props.totalPrice === 'string' ? parseFloat(props.totalPrice) : props.totalPrice);

// Form data
const additionalNotes = ref('');
const agreeToTerms = ref(false);
const showPaymentModal = ref(false);
const isSubmitting = ref(false);

const goBack = () => {
  router.visit(`/client/booking/${vehicleIdNum.value}`);
};

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    weekday: 'short', 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};

const handlePayNow = () => {
  console.log('=== PAY NOW CLICKED ===');
  console.log('Agree to Terms:', agreeToTerms.value);
  
  // Validate terms first
  if (!agreeToTerms.value) {
    alert('Please agree to the terms and conditions');
    return;
  }
  
  console.log('Opening payment modal...');
  // Open payment gateway modal
  showPaymentModal.value = true;
};

// Handle successful payment
const handlePaymentSuccess = () => {
  console.log('=== PAYMENT SUCCESS - CREATING BOOKING ===');
  
  isSubmitting.value = true;
  
  // Prepare data that matches controller validation
  const bookingData = {
    pickup_date: props.pickupDate,
    return_date: props.returnDate,
    pickup_time: props.pickupTime,
    return_time: props.returnTime,
    additional_notes: additionalNotes.value || '',
    agree_to_terms: 1, // Always 1 when payment succeeds
  };

  console.log('Vehicle ID:', vehicleIdNum.value);
  console.log('Booking Data:', bookingData);
  console.log('Submitting to:', `/client/booking/${vehicleIdNum.value}/form`);

  // Submit booking
  router.post(`/client/booking/${vehicleIdNum.value}/form`, bookingData, {
    preserveScroll: true,
    onBefore: () => {
      console.log('onBefore: Starting booking creation...');
    },
    onStart: () => {
      console.log('onStart: Request initiated');
    },
    onSuccess: (page) => {
      console.log('onSuccess: Booking created!', page);
      showPaymentModal.value = false;
      isSubmitting.value = false;
    },
    onError: (errors) => {
      console.error('onError: Booking failed!', errors);
      showPaymentModal.value = false;
      isSubmitting.value = false;
      
      // Show first error
      const errorMessages = Object.values(errors);
      if (errorMessages.length > 0) {
        alert('Booking failed: ' + errorMessages[0]);
      }
    },
    onFinish: () => {
      console.log('onFinish: Request completed');
    },
  });
};

// Handle payment cancellation
const handlePaymentCancel = () => {
  console.log('Payment cancelled');
  showPaymentModal.value = false;
};
</script>

<template>
  <Head title="Booking & Payment" />
  <AppHeader :can-register="canRegister" />
  
  <div class="min-h-screen bg-gradient-to-br from-neutral-50 to-neutral-100">
    <!-- Back Button -->
    <div class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-4">
        <button
          @click="goBack"
          class="flex items-center gap-2 text-neutral-600 hover:text-[#0081A7] transition-colors group"
        >
          <ArrowLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
          <span class="font-medium">Back to Vehicle Details</span>
        </button>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8">
      <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-neutral-900 font-['Roboto'] mb-2">
          Complete Your Booking
        </h1>
        <p class="text-neutral-600">
          Review your rental details and proceed with payment
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Forms -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Rental Details Card -->
          <Card class="border-2 border-[#0081A7]/20">
            <CardHeader class="bg-gradient-to-r from-[#0081A7]/10 to-[#00AFB9]/10">
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2">
                <Calendar class="w-5 h-5 text-[#0081A7]" />
                Rental Details
              </CardTitle>
            </CardHeader>
            <CardContent class="pt-6">
              <div class="flex gap-4 mb-6 p-4 bg-neutral-50 rounded-lg">
                <img
                  :src="vehicleImage"
                  :alt="vehicleName"
                  class="w-24 h-24 rounded-lg object-cover"
                />
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold text-neutral-900">{{ vehicleName }}</h3>
                    <Badge class="bg-[#0081A7] text-white border-0">{{ vehicleType }}</Badge>
                  </div>
                  <p class="text-sm text-neutral-600">
                    ₱{{ pricePerDayNum?.toLocaleString() }} per day
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg">
                  <div class="flex items-center gap-2 text-[#0081A7] mb-2">
                    <Calendar class="w-4 h-4" />
                    <span class="text-sm font-semibold">Pickup</span>
                  </div>
                  <p class="font-bold text-neutral-900">{{ formatDate(pickupDate || '') }}</p>
                  <p class="text-sm text-neutral-600 flex items-center gap-1 mt-1">
                    <Clock class="w-3 h-3" />
                    {{ pickupTime }}
                  </p>
                </div>
                <div class="p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg">
                  <div class="flex items-center gap-2 text-[#0081A7] mb-2">
                    <Calendar class="w-4 h-4" />
                    <span class="text-sm font-semibold">Return</span>
                  </div>
                  <p class="font-bold text-neutral-900">{{ formatDate(returnDate || '') }}</p>
                  <p class="text-sm text-neutral-600 flex items-center gap-1 mt-1">
                    <Clock class="w-3 h-3" />
                    {{ returnTime }}
                  </p>
                </div>
              </div>

              <div class="mt-4 p-3 bg-[#00AFB9]/10 rounded-lg border border-[#00AFB9]/30">
                <p class="text-sm text-neutral-700">
                  <span class="font-semibold">Total Duration:</span> {{ totalDaysNum || 0 }} day{{ (totalDaysNum || 0) > 1 ? 's' : '' }}
                </p>
              </div>
            </CardContent>
          </Card>

          <!-- Pickup Location Card -->
          <Card>
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2">
                <MapPin class="w-5 h-5 text-[#0081A7]" />
                Pickup Location
              </CardTitle>
              <CardDescription>Vehicle will be available at this location</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Mock Map Display -->
              <div class="relative w-full h-64 bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg overflow-hidden border-2 border-blue-200">
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="relative">
                    <div class="absolute w-96 h-1 bg-gray-300 top-20 left-1/2 -translate-x-1/2 rotate-45"></div>
                    <div class="absolute w-96 h-1 bg-gray-300 top-20 left-1/2 -translate-x-1/2 -rotate-45"></div>
                    <div class="relative z-10">
                      <MapPin class="w-12 h-12 text-red-500 fill-red-500 drop-shadow-lg animate-bounce" />
                    </div>
                  </div>
                </div>
                <div class="absolute top-4 right-4 flex flex-col gap-2">
                  <button class="w-8 h-8 bg-white rounded shadow-md flex items-center justify-center text-gray-700 hover:bg-gray-50">+</button>
                  <button class="w-8 h-8 bg-white rounded shadow-md flex items-center justify-center text-gray-700 hover:bg-gray-50">−</button>
                </div>
                <div class="absolute bottom-2 left-2 text-xs text-gray-500 bg-white/80 px-2 py-1 rounded">Map Preview</div>
              </div>

              <!-- Location Details -->
              <div class="p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg border border-[#0081A7]/30">
                <div class="flex items-start gap-3">
                  <Navigation class="w-5 h-5 text-[#0081A7] flex-shrink-0 mt-0.5" />
                  <div class="flex-1">
                    <p class="font-semibold text-neutral-900 mb-1">{{ mockLocation.address }}</p>
                    <p class="text-xs text-neutral-600">
                      Coordinates: {{ mockLocation.lat }}, {{ mockLocation.lng }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Additional Notes -->
              <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">
                  Additional Notes (Optional)
                </label>
                <textarea
                  v-model="additionalNotes"
                  rows="3"
                  placeholder="Any special requests or requirements?"
                  class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none resize-none"
                ></textarea>
              </div>
            </CardContent>
          </Card>

          <!-- Payment Method Card -->
          <Card class="border-2 border-[#0081A7]/20">
            <CardHeader class="bg-gradient-to-r from-[#0081A7]/10 to-[#00AFB9]/10">
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2">
                <CreditCard class="w-5 h-5 text-[#0081A7]" />
                Payment Method
              </CardTitle>
            </CardHeader>
            <CardContent class="pt-6">
              <div class="space-y-3">
                <div class="relative p-4 border-2 border-[#0081A7] bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg cursor-pointer">
                  <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-full border-2 border-[#0081A7] bg-[#0081A7] flex items-center justify-center">
                      <div class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                    <div class="flex-1">
                      <p class="font-semibold text-neutral-900">Credit/Debit Card</p>
                      <p class="text-sm text-neutral-600">Pay securely with your card</p>
                    </div>
                    <CreditCard class="w-6 h-6 text-[#0081A7]" />
                  </div>
                  <Badge class="absolute top-2 right-2 bg-[#00AFB9] text-white border-0">Recommended</Badge>
                </div>
              </div>

              <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg flex gap-3">
                <Shield class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                <div>
                  <p class="text-sm font-semibold text-blue-900">Secure Payment</p>
                  <p class="text-xs text-blue-700 mt-1">
                    Your payment information is encrypted and secure. We never store your card details.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Terms and Conditions -->
          <Card>
            <CardContent class="pt-6">
              <div class="flex items-start gap-3">
                <input
                  v-model="agreeToTerms"
                  type="checkbox"
                  id="terms"
                  class="mt-1 w-4 h-4 text-[#0081A7] border-neutral-300 rounded focus:ring-[#00AFB9]"
                />
                <label for="terms" class="text-sm text-neutral-700 cursor-pointer">
                  I agree to the <a href="#" class="text-[#0081A7] hover:underline font-semibold">Terms and Conditions</a> and 
                  <a href="#" class="text-[#0081A7] hover:underline font-semibold">Rental Agreement</a>. 
                  I understand the security deposit and cancellation policy.
                </label>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Right Column: Price Summary -->
        <div class="lg:col-span-1">
          <div class="sticky top-24">
            <Card class="border-2 border-[#0081A7]/20 shadow-xl">
              <CardHeader class="bg-gradient-to-br from-[#0081A7] to-[#00AFB9] text-white">
                <CardTitle class="text-xl font-['Roboto']">Booking Summary</CardTitle>
              </CardHeader>
              <CardContent class="pt-6 space-y-4">
                <div class="space-y-3">
                  <div class="flex justify-between text-sm">
                    <span class="text-neutral-600">Vehicle Rental</span>
                    <span class="font-semibold">₱{{ subtotalNum?.toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-neutral-600">Service Fee (5%)</span>
                    <span class="font-semibold">₱{{ serviceFeeNum?.toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between text-sm text-neutral-500">
                    <span>Price per day</span>
                    <span>₱{{ pricePerDayNum?.toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between text-sm text-neutral-500">
                    <span>Number of days</span>
                    <span>{{ totalDaysNum || 0 }} day{{ (totalDaysNum || 0) > 1 ? 's' : '' }}</span>
                  </div>
                </div>

                <div class="pt-4 border-t-2 border-dashed">
                  <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-neutral-900">Total Amount</span>
                    <span class="text-2xl font-bold text-[#0081A7]">₱{{ totalPriceNum?.toLocaleString() }}</span>
                  </div>
                </div>

                <div class="pt-4 space-y-2">
                  <div class="flex items-center gap-2 text-sm text-neutral-600">
                    <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
                    <span>Free cancellation within 24 hours</span>
                  </div>
                  <div class="flex items-center gap-2 text-sm text-neutral-600">
                    <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
                    <span>Instant booking confirmation</span>
                  </div>
                  <div class="flex items-center gap-2 text-sm text-neutral-600">
                    <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
                    <span>Insurance coverage included</span>
                  </div>
                </div>
              </CardContent>
              <CardFooter class="flex flex-col gap-3">
                <button
                  @click="handlePayNow"
                  :disabled="isSubmitting"
                  class="w-full py-4 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white rounded-lg font-bold text-lg hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed font-['Roboto'] flex items-center justify-center gap-2"
                >
                  <CreditCard v-if="!isSubmitting" class="w-5 h-5" />
                  <span v-if="isSubmitting">Processing...</span>
                  <span v-else>Confirm Booking</span>
                </button>
                <p class="text-xs text-center text-neutral-500">
                  <Shield class="w-3 h-3 inline mr-1" />
                  Secured by 256-bit SSL encryption
                </p>
              </CardFooter>
            </Card>

            <!-- Important Notice -->
            <Card class="mt-4 bg-amber-50 border-amber-200">
              <CardContent class="pt-6">
                <div class="flex gap-3">
                  <AlertCircle class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                  <div>
                    <p class="text-sm font-semibold text-amber-900 mb-2">Important Notice</p>
                    <ul class="text-xs text-amber-800 space-y-1">
                      <li>• Security deposit required upon pickup</li>
                      <li>• Valid driver's license must be presented</li>
                      <li>• Vehicle must be returned with same fuel level</li>
                    </ul>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment Gateway Modal -->
  <PaymentGatewayModal
    :is-open="showPaymentModal"
    :amount="totalPriceNum || 0"
    :on-success="handlePaymentSuccess"
    :on-cancel="handlePaymentCancel"
  />
</template>

<style scoped>
* {
  font-family: 'Roboto', sans-serif;
}
</style>