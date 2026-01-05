<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
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
  Wallet,
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
    errors?: Record<string, string>;
  }>(),
  { 
    canRegister: true,
    errors: () => ({})
  }
);

// Get page props for reactive errors
const page = usePage();

// Log component mount and props
onMounted(() => {
  console.log('=== PAYMENT COMPONENT MOUNTED ===');
  console.log('All Props:', {
    vehicleId: props.vehicleId,
    vehicleName: props.vehicleName,
    vehicleImage: props.vehicleImage,
    vehicleType: props.vehicleType,
    pricePerDay: props.pricePerDay,
    pickupDate: props.pickupDate,
    returnDate: props.returnDate,
    pickupTime: props.pickupTime,
    returnTime: props.returnTime,
    totalDays: props.totalDays,
    subtotal: props.subtotal,
    serviceFee: props.serviceFee,
    totalPrice: props.totalPrice,
    operatorId: props.operatorId,
  });
  console.log('Initial Errors:', props.errors);
  console.log('Page Props:', page.props);
});

// Mock location data
const mockLocation = {
  address: "SM Mall of Asia, Seaside Blvd, Pasay, Metro Manila",
  lat: 14.5352,
  lng: 120.9818,
};

// Convert string props to numbers for calculations
const vehicleIdNum = computed(() => {
  const id = typeof props.vehicleId === 'string' ? parseInt(props.vehicleId) : props.vehicleId;
  console.log('vehicleIdNum computed:', id);
  return id;
});

const pricePerDayNum = computed(() => typeof props.pricePerDay === 'string' ? parseFloat(props.pricePerDay) : props.pricePerDay);
const totalDaysNum = computed(() => typeof props.totalDays === 'string' ? parseInt(props.totalDays) : props.totalDays);
const subtotalNum = computed(() => typeof props.subtotal === 'string' ? parseFloat(props.subtotal) : props.subtotal);
const serviceFeeNum = computed(() => typeof props.serviceFee === 'string' ? parseFloat(props.serviceFee) : props.serviceFee);
const totalPriceNum = computed(() => typeof props.totalPrice === 'string' ? parseFloat(props.totalPrice) : props.totalPrice);

// Form data
const notes = ref('');
const agreeToTerms = ref(false);
const selectedPaymentMethod = ref<'credit_card' | 'gcash' | 'paymaya'>('credit_card');
const isSubmitting = ref(false);
const validationErrors = ref<string[]>([]);

const goBack = () => {
  console.log('=== GO BACK CLICKED ===');
  console.log('Navigating to:', `/client/booking/${vehicleIdNum.value}`);
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
  console.log('╔═══════════════════════════════════════════════════════════╗');
  console.log('║           PAY NOW BUTTON CLICKED - START                  ║');
  console.log('╚═══════════════════════════════════════════════════════════╝');
  
  // Log current state
  console.log('📋 Current Form State:', {
    notes: notes.value,
    agreeToTerms: agreeToTerms.value,
    selectedPaymentMethod: selectedPaymentMethod.value,
    isSubmitting: isSubmitting.value,
  });
  
  // Clear previous errors
  validationErrors.value = [];
  console.log('✓ Cleared previous validation errors');
  
  // Frontend validation
  if (!agreeToTerms.value) {
    console.error('❌ FRONTEND VALIDATION FAILED: Terms not agreed');
    validationErrors.value = ['Please agree to the terms and conditions'];
    return;
  }
  console.log('✓ Frontend validation passed');
  
  isSubmitting.value = true;
  console.log('✓ Set isSubmitting = true');
  
  // Prepare booking data
  const bookingData = {
    pickup_date: props.pickupDate,
    return_date: props.returnDate,
    pickup_time: props.pickupTime,
    return_time: props.returnTime,
    notes: notes.value || '',
    agree_to_terms: 1,
    payment_method: selectedPaymentMethod.value,
  };

  const targetUrl = `/client/booking/${vehicleIdNum.value}/form`;
  
  console.log('📦 Booking Data Prepared:', bookingData);
  console.log('🎯 Target URL:', targetUrl);
  console.log('🚀 Initiating Inertia POST request...');

  // Submit booking
  router.post(targetUrl, bookingData, {
    preserveScroll: true,
    
    onBefore: (visit) => {
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║                    onBefore Hook                          ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
      console.log('Visit details:', visit);
    },
    
    onStart: (visit) => {
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║                     onStart Hook                          ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
      console.log('Request initiated:', visit);
    },
    
    onProgress: (progress) => {
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║                    onProgress Hook                        ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
      console.log('Progress:', progress);
    },
    
    onSuccess: (page) => {
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║                    onSuccess Hook                         ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
      console.log('✅ Request completed successfully');
      console.log('Page component:', page.component);
      console.log('Page props:', page.props);
      console.log('Page URL:', page.url);
      
      // Check if there are validation errors
      const errors = page.props.errors as Record<string, string> | undefined;
      if (errors && Object.keys(errors).length > 0) {
        console.warn('⚠️ Validation errors found despite success:', errors);
        validationErrors.value = Object.values(errors);
        isSubmitting.value = false;
      } else {
        console.log('✓ No validation errors found');
        console.log('✓ Booking created successfully');
        console.log('✓ Backend will handle redirect to payment gateway');
      }
    },
    
    onError: (errors) => {
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║                     onError Hook                          ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
      console.error('❌ REQUEST FAILED');
      console.error('Error type:', typeof errors);
      console.error('Error keys:', Object.keys(errors));
      console.error('All errors:', errors);
      
      // Log each error individually
      Object.entries(errors).forEach(([key, value]) => {
        console.error(`  ❌ ${key}:`, value);
      });
      
      isSubmitting.value = false;
      validationErrors.value = Object.values(errors);
      
      console.log('✓ Reset isSubmitting = false');
      console.log('✓ Updated validationErrors:', validationErrors.value);
      console.log('🔝 Scrolling to top to show errors...');
      
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    
    onCancelToken: (cancelToken) => {
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║                  onCancelToken Hook                       ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
      console.log('Cancel token:', cancelToken);
    },
    
    onCancel: () => {
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║                    onCancel Hook                          ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
      console.warn('⚠️ Request was cancelled');
      isSubmitting.value = false;
    },
    
    onFinish: (visit) => {
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║                    onFinish Hook                          ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
      console.log('Request finished:', visit);
      console.log('Final isSubmitting state:', isSubmitting.value);
      console.log('Final validationErrors:', validationErrors.value);
      console.log('╔═══════════════════════════════════════════════════════════╗');
      console.log('║           PAY NOW BUTTON CLICKED - END                    ║');
      console.log('╚═══════════════════════════════════════════════════════════╝');
    },
  });
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
          :disabled="isSubmitting"
          class="flex items-center gap-2 text-neutral-600 hover:text-[#0081A7] transition-colors group disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <ArrowLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
          <span class="font-medium">Back to Vehicle Details</span>
        </button>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8">
      <!-- Validation Errors Display -->
      <div v-if="validationErrors.length > 0" class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
        <div class="flex items-start gap-3">
          <AlertCircle class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
          <div class="flex-1">
            <h3 class="font-semibold text-red-900 mb-2">Please correct the following errors:</h3>
            <ul class="list-disc list-inside space-y-1">
              <li v-for="(error, index) in validationErrors" :key="index" class="text-sm text-red-700">
                {{ error }}
              </li>
            </ul>
          </div>
        </div>
      </div>

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
          <Card class="border-2 border-[#0081A7] bg-white">
            <CardHeader class="bg-white border-b border-neutral-200">
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2 text-[#0081A7]">
                <Calendar class="w-5 h-5 text-[#0081A7]" />
                Rental Details
              </CardTitle>
            </CardHeader>
            <CardContent class="pt-6">
              <div class="flex gap-4 mb-6 p-4 bg-[#0081A7]/5 rounded-lg border border-[#0081A7]/20">
                <img
                  :src="vehicleImage"
                  :alt="vehicleName"
                  class="w-24 h-24 rounded-lg object-cover"
                />
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold text-neutral-900">{{ vehicleName }}</h3>
                    <Badge class="bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white border-0">{{ vehicleType }}</Badge>
                  </div>
                  <p class="text-sm text-neutral-600">
                    ₱{{ pricePerDayNum?.toLocaleString() }} per day
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-[#0081A7]/5 rounded-lg border border-[#0081A7]/20">
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
                <div class="p-4 bg-[#0081A7]/5 rounded-lg border border-[#0081A7]/20">
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
          <Card class="bg-white border-2 border-[#0081A7]">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2 text-[#0081A7]">
                <MapPin class="w-5 h-5 text-[#0081A7]" />
                Pickup Location
              </CardTitle>
              <CardDescription class="text-neutral-600">Vehicle will be available at this location</CardDescription>
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
              <div class="p-4 bg-[#0081A7]/5 rounded-lg border border-[#0081A7]/20">
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
                <label class="block text-sm font-medium text-[#0081A7] mb-2">
                  Additional Notes (Optional)
                </label>
                <textarea
                  v-model="notes"
                  rows="3"
                  placeholder="Any special requests or requirements?"
                  class="w-full px-4 py-3 bg-white border border-[#0081A7]/30 text-neutral-900 placeholder:text-neutral-400 rounded-lg focus:ring-2 focus:ring-[#0081A7] focus:border-[#0081A7] outline-none resize-none"
                  :disabled="isSubmitting"
                ></textarea>
              </div>
            </CardContent>
          </Card>

          <!-- Payment Method Card -->
          <Card class="border-2 border-[#0081A7] bg-white">
            <CardHeader class="bg-white border-b border-neutral-200">
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2 text-[#0081A7]">
                <CreditCard class="w-5 h-5 text-[#0081A7]" />
                Select Payment Method
              </CardTitle>
              <CardDescription class="text-neutral-600">Choose how you want to pay</CardDescription>
            </CardHeader>
            <CardContent class="pt-6">
              <div class="space-y-3">
                <!-- Credit/Debit Card Option -->
                <div 
                  @click="!isSubmitting && (selectedPaymentMethod = 'credit_card')"
                  :class="[
                    'relative p-4 border-2 rounded-lg transition-all',
                    isSubmitting ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                    selectedPaymentMethod === 'credit_card'
                      ? 'border-[#0081A7] bg-[#0081A7]/5'
                      : 'border-neutral-300 hover:border-[#0081A7]/50'
                  ]"
                >
                  <div class="flex items-center gap-3">
                    <div :class="[
                      'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all',
                      selectedPaymentMethod === 'credit_card'
                        ? 'border-[#0081A7] bg-[#0081A7]'
                        : 'border-neutral-400'
                    ]">
                      <div v-if="selectedPaymentMethod === 'credit_card'" class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                    <div class="flex-1">
                      <p class="font-semibold text-neutral-900">Credit/Debit Card</p>
                      <p class="text-sm text-neutral-600">Visa, Mastercard, American Express</p>
                    </div>
                    <CreditCard class="w-6 h-6" :class="selectedPaymentMethod === 'credit_card' ? 'text-[#0081A7]' : 'text-neutral-400'" />
                  </div>
                  <Badge v-if="selectedPaymentMethod === 'credit_card'" class="absolute top-2 right-2 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white border-0">
                    Selected
                  </Badge>
                </div>

                <!-- GCash Option -->
                <div 
                  @click="!isSubmitting && (selectedPaymentMethod = 'gcash')"
                  :class="[
                    'relative p-4 border-2 rounded-lg transition-all',
                    isSubmitting ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                    selectedPaymentMethod === 'gcash'
                      ? 'border-[#0081A7] bg-[#0081A7]/5'
                      : 'border-neutral-300 hover:border-[#0081A7]/50'
                  ]"
                >
                  <div class="flex items-center gap-3">
                    <div :class="[
                      'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all',
                      selectedPaymentMethod === 'gcash'
                        ? 'border-[#0081A7] bg-[#0081A7]'
                        : 'border-neutral-400'
                    ]">
                      <div v-if="selectedPaymentMethod === 'gcash'" class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                    <div class="flex-1">
                      <p class="font-semibold text-neutral-900">GCash</p>
                      <p class="text-sm text-neutral-600">Pay via GCash e-wallet</p>
                    </div>
                    <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center text-white text-xs font-bold">
                      G
                    </div>
                  </div>
                  <Badge v-if="selectedPaymentMethod === 'gcash'" class="absolute top-2 right-2 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white border-0">
                    Selected
                  </Badge>
                </div>

                <!-- PayMaya Option -->
                <div 
                  @click="!isSubmitting && (selectedPaymentMethod = 'paymaya')"
                  :class="[
                    'relative p-4 border-2 rounded-lg transition-all',
                    isSubmitting ? 'cursor-not-allowed opacity-50' : 'cursor-pointer',
                    selectedPaymentMethod === 'paymaya'
                      ? 'border-[#0081A7] bg-[#0081A7]/5'
                      : 'border-neutral-300 hover:border-[#0081A7]/50'
                  ]"
                >
                  <div class="flex items-center gap-3">
                    <div :class="[
                      'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all',
                      selectedPaymentMethod === 'paymaya'
                        ? 'border-[#0081A7] bg-[#0081A7]'
                        : 'border-neutral-400'
                    ]">
                      <div v-if="selectedPaymentMethod === 'paymaya'" class="w-2 h-2 bg-white rounded-full"></div>
                    </div>
                    <div class="flex-1">
                      <p class="font-semibold text-neutral-900">PayMaya</p>
                      <p class="text-sm text-neutral-600">Pay via PayMaya e-wallet</p>
                    </div>
                    <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs font-bold">
                      PM
                    </div>
                  </div>
                  <Badge v-if="selectedPaymentMethod === 'paymaya'" class="absolute top-2 right-2 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white border-0">
                    Selected
                  </Badge>
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
          <Card :class="{ 'border-red-500': validationErrors.length > 0 && !agreeToTerms.valueOf() }" class="border-2 border-[#0081A7] bg-white">
            <CardContent class="pt-6">
              <div class="flex items-start gap-3">
                <input
                  v-model="agreeToTerms"
                  type="checkbox"
                  id="terms"
                  class="mt-1 w-4 h-4 text-[#0081A7] border-neutral-400 rounded focus:ring-[#0081A7] bg-white"
                  :disabled="isSubmitting"
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
            <Card class="border-2 border-[#0081A7] shadow-xl bg-white">
              <CardHeader class="bg-white border-b border-neutral-200">
                <CardTitle class="text-xl font-['Roboto'] text-[#0081A7]">Booking Summary</CardTitle>
              </CardHeader>
              <CardContent class="pt-6 space-y-4">
                <div class="space-y-3">
                  <div class="flex justify-between text-sm">
                    <span class="text-neutral-600">Vehicle Rental</span>
                    <span class="font-semibold text-neutral-900">₱{{ subtotalNum?.toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-neutral-600">Service Fee (5%)</span>
                    <span class="font-semibold text-neutral-900">₱{{ serviceFeeNum?.toLocaleString() }}</span>
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

                <div class="pt-4 border-t-2 border-dashed border-neutral-300">
                  <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-neutral-900">Total Amount</span>
                    <span class="text-2xl font-bold text-[#0081A7]">₱{{ totalPriceNum?.toLocaleString() }}</span>
                  </div>
                </div>

                <div class="pt-4 space-y-2">
                  <div class="flex items-center gap-2 text-sm text-neutral-700">
                    <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
                    <span>Free cancellation within 24 hours</span>
                  </div>
                  <div class="flex items-center gap-2 text-sm text-neutral-700">
                    <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
                    <span>Instant booking confirmation</span>
                  </div>
                  <div class="flex items-center gap-2 text-sm text-neutral-700">
                    <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
                    <span>Insurance coverage included</span>
                  </div>
                </div>
              </CardContent>
              <CardFooter class="flex flex-col gap-3 border-t border-neutral-200">
                <button
                  @click="handlePayNow"
                  :disabled="isSubmitting"
                  class="w-full py-4 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white rounded-lg font-bold text-lg hover:shadow-lg hover:from-[#0081A7]/90 hover:to-[#00AFB9]/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-['Roboto'] flex items-center justify-center gap-2"
                >
                  <div v-if="isSubmitting" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  <CreditCard v-else class="w-5 h-5" />
                  <span v-if="isSubmitting">Processing...</span>
                  <span v-else>Proceed to Payment</span>
                </button>
                <p class="text-xs text-center text-neutral-600">
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
</template>

<style scoped>
* {
  font-family: 'Roboto', sans-serif;
}
</style>