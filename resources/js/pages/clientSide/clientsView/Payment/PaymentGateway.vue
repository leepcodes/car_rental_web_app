<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import { 
  CreditCard, 
  Lock, 
  AlertCircle, 
  CheckCircle2, 
  X, 
  ArrowLeft, 
  Wallet, 
  CreditCard as CardIcon,
  Shield,
  Calendar,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button/index';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';


interface BookingData {
  id: number;
  reference_number: string;
  amount: number;
  vehicle_name: string;
  pickup_date: string;
  return_date: string;
}

interface User {
  id: number;
  name: string;
  email: string;
  contact_number?: string;
}

const props = defineProps<{
  canRegister?: boolean;
  booking: BookingData;
  payment_method?: 'credit_card' | 'gcash' | 'paymaya';
}>();

const page = usePage();
const user = computed(() => page.props.auth?.user as User | undefined);

// Use payment method from props (selected in previous page)
const selectedMethod = ref<'credit_card' | 'gcash' | 'paymaya'>(props.payment_method || 'credit_card');

// Card form fields
const cardNumber = ref('');
const cardName = ref('');
const expiryDate = ref('');
const cvv = ref('');

// E-wallet fields
const ewalletNumber = ref('');
const ewalletEmail = ref('');

// Processing states
const isProcessing = ref(false);
const paymentStatus = ref<'idle' | 'processing' | 'success' | 'failed'>('idle');
const errorMessage = ref('');

const formattedAmount = computed(() => {
  return `₱${props.booking.amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
});

// Initialize form with user data
onMounted(() => {
  console.log('=== PAYMENT GATEWAY MOUNTED ===');
  console.log('Props:', props);
  console.log('User:', user.value);
  console.log('Selected Method:', selectedMethod.value);
  
  // Pre-fill card name with user's name
  if (user.value?.name) {
    cardName.value = user.value.name.toUpperCase();
    console.log('Card name pre-filled:', cardName.value);
  }
  
  // Note: E-wallet number and email are NOT pre-filled
  // User should enter their payment account details (GCash/PayMaya)
  console.log('E-wallet fields ready for user input (payment account may differ from registered account)');
});

const validateCardForm = () => {
  console.log('=== VALIDATING CARD FORM ===');
  console.log('Card Number:', cardNumber.value, 'Length:', cardNumber.value.replace(/\s/g, '').length);
  console.log('Card Name:', cardName.value, 'Length:', cardName.value.length);
  console.log('Expiry Date:', expiryDate.value, 'Length:', expiryDate.value.length);
  console.log('CVV:', cvv.value, 'Length:', cvv.value.length);
  
  if (!cardNumber.value || cardNumber.value.replace(/\s/g, '').length !== 16) {
    errorMessage.value = 'Please enter a valid 16-digit card number';
    console.error('Validation failed: Invalid card number');
    return false;
  }
  if (!cardName.value || cardName.value.length < 3) {
    errorMessage.value = 'Please enter the cardholder name';
    console.error('Validation failed: Invalid cardholder name');
    return false;
  }
  if (!expiryDate.value || expiryDate.value.length !== 5) {
    errorMessage.value = 'Please enter a valid expiry date (MM/YY)';
    console.error('Validation failed: Invalid expiry date');
    return false;
  }
  if (!cvv.value || cvv.value.length !== 3) {
    errorMessage.value = 'Please enter a valid 3-digit CVV';
    console.error('Validation failed: Invalid CVV');
    return false;
  }
  
  console.log('✅ Card form validation passed');
  return true;
};

const validateEwalletForm = () => {
  console.log('=== VALIDATING E-WALLET FORM ===');
  console.log('E-wallet Number:', ewalletNumber.value, 'Length:', ewalletNumber.value.length);
  console.log('E-wallet Email:', ewalletEmail.value);
  
  if (!ewalletNumber.value || ewalletNumber.value.length !== 11) {
    errorMessage.value = 'Please enter a valid 11-digit mobile number';
    console.error('Validation failed: Invalid mobile number');
    return false;
  }
  if (!ewalletEmail.value || !ewalletEmail.value.includes('@')) {
    errorMessage.value = 'Please enter a valid email address';
    console.error('Validation failed: Invalid email');
    return false;
  }
  
  console.log('✅ E-wallet form validation passed');
  return true;
};

const processPayment = async () => {
  console.log('=== PROCESS PAYMENT START ===');
  console.log('Selected Method:', selectedMethod.value);
  console.log('Booking ID:', props.booking.id);
  
  errorMessage.value = '';
  
  // Validate form based on payment method
  if (selectedMethod.value === 'credit_card') {
    if (!validateCardForm()) return;
  } else {
    if (!validateEwalletForm()) return;
  }

  isProcessing.value = true;
  paymentStatus.value = 'processing';
  console.log('Payment status set to: processing');

  try {
    console.log('Simulating payment processing (2.5s delay)...');
    await new Promise(resolve => setTimeout(resolve, 2500));

    let isSuccess = false;
    
    if (selectedMethod.value === 'credit_card') {
      const cleanedNumber = cardNumber.value.replace(/\s/g, '');
      const lastDigit = parseInt(cleanedNumber.slice(-1));
      isSuccess = lastDigit % 2 === 0;
      console.log('Card payment simulation:', {
        cardNumber: cleanedNumber,
        lastDigit: lastDigit,
        isSuccess: isSuccess
      });
    } else {
      const lastDigit = parseInt(ewalletNumber.value.slice(-1));
      isSuccess = lastDigit % 2 === 0;
      console.log('E-wallet payment simulation:', {
        ewalletNumber: ewalletNumber.value,
        lastDigit: lastDigit,
        isSuccess: isSuccess
      });
    }

    if (isSuccess) {
      console.log('✅ Payment simulation SUCCESS');
      paymentStatus.value = 'success';
      await new Promise(resolve => setTimeout(resolve, 1500));
      
      const paymentData: any = {
        payment_method: selectedMethod.value,
      };

      if (selectedMethod.value === 'credit_card') {
        paymentData.card_last_four = cardNumber.value.replace(/\s/g, '').slice(-4);
        paymentData.card_brand = detectCardBrand(cardNumber.value);
        console.log('Card payment data:', paymentData);
      } else {
        paymentData.ewallet_number = ewalletNumber.value;
        paymentData.ewallet_email = ewalletEmail.value;
        console.log('E-wallet payment data:', paymentData);
      }

      console.log('=== SENDING TO BACKEND ===');
      
      // Use direct URL - most reliable approach
      const routeUrl = `/payment/complete/${props.booking.id}`;
      console.log('Route URL:', routeUrl);
      console.log('Payment Data:', paymentData);

      router.post(routeUrl, paymentData, {
        onStart: () => {
          console.log('Request started...');
        },
        onSuccess: (page) => {
          console.log('✅ Request SUCCESS');
          console.log('Response page:', page);
        },
        onError: (errors) => {
          console.error('❌ Request ERROR');
          console.error('Errors:', errors);
          paymentStatus.value = 'failed';
          errorMessage.value = errors.message || 'Payment processing failed. Please try again.';
          isProcessing.value = false;
        },
        onFinish: () => {
          console.log('Request finished');
        }
      });
      
    } else {
      console.log('❌ Payment simulation FAILED');
      paymentStatus.value = 'failed';
      errorMessage.value = 'Payment declined. Please check your details or try a different payment method.';
      isProcessing.value = false;
      
      setTimeout(() => {
        console.log('Resetting payment status to idle');
        paymentStatus.value = 'idle';
      }, 3000);
    }
  } catch (error) {
    console.error('=== PAYMENT PROCESSING ERROR ===');
    console.error(error);
    paymentStatus.value = 'failed';
    errorMessage.value = 'An error occurred while processing your payment.';
    isProcessing.value = false;
  }
};

const detectCardBrand = (number: string): string => {
  const cleaned = number.replace(/\s/g, '');
  if (cleaned.startsWith('4')) return 'Visa';
  if (cleaned.startsWith('5')) return 'Mastercard';
  if (cleaned.startsWith('3')) return 'Amex';
  return 'Unknown';
};

const handleCancel = () => {
  console.log('Cancel button clicked');
  if (!isProcessing.value && confirm('Are you sure you want to cancel this payment? Your booking will remain pending.')) {
    console.log('User confirmed cancellation, redirecting to bookings...');
    router.visit('/client/booking'); // Use direct path
  }
};

const goBack = () => {
  console.log('Back button clicked');
  if (!isProcessing.value) {
    window.history.back();
  }
};

const testCredentials = {
  card: {
    success: '4532 1234 5678 9010',
    failed: '4532 1234 5678 9011',
  },
  ewallet: {
    success: '09123456780',
    failed: '09123456781',
  }
};
</script>

<template>
  <Head title="Payment Gateway" />
  <AppHeader :can-register="canRegister" />

  <div class="min-h-screen py-15 bg-gradient-to-br from-neutral-50 to-neutral-100">
    <!-- Back Button -->
    <div class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-4">
        <button
          @click="goBack"
          :disabled="isProcessing"
          class="flex items-center gap-2 text-neutral-600 hover:text-[#0081A7] transition-colors group disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <ArrowLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
          <span class="font-medium">Back to Payment Details</span>
        </button>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8">
      <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-neutral-900 font-['Roboto'] mb-2">
          Secure Payment Gateway
        </h1>
        <p class="text-neutral-600 flex items-center gap-2">
          <Lock class="w-4 h-4" />
          Your payment is protected with SSL encryption
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Payment Form -->
        <div class="lg:col-span-2 space-y-6">
          <Card class="border-2 border-[#0081A7] bg-white">
            <!-- Processing State -->
            <div v-if="paymentStatus === 'processing'" class="p-12 text-center">
              <div class="inline-block animate-spin rounded-full h-20 w-20 border-4 border-[#0081A7] border-t-transparent mb-6"></div>
              <p class="text-2xl font-semibold text-neutral-700">Processing Payment...</p>
              <p class="text-sm text-neutral-500 mt-3">Please wait while we verify your payment</p>
              <p class="text-xs text-neutral-400 mt-2">Do not close this window</p>
            </div>

            <!-- Success State -->
            <div v-else-if="paymentStatus === 'success'" class="p-12 text-center">
              <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
                <CheckCircle2 class="w-12 h-12 text-green-600" />
              </div>
              <p class="text-2xl font-semibold text-green-700">Payment Successful!</p>
              <p class="text-sm text-neutral-500 mt-3">Completing your booking...</p>
            </div>

            <!-- Failed State -->
            <div v-else-if="paymentStatus === 'failed'" class="p-12 text-center">
              <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 mb-6">
                <X class="w-12 h-12 text-red-600" />
              </div>
              <p class="text-2xl font-semibold text-red-700">Payment Failed</p>
              <p class="text-sm text-neutral-600 mt-3 max-w-md mx-auto">{{ errorMessage }}</p>
              <Button
                @click="paymentStatus = 'idle'"
                class="mt-6 bg-[#0081A7] hover:bg-[#006b8f]"
              >
                Try Again
              </Button>
            </div>

            <!-- Payment Form -->
            <div v-else>
              <CardHeader class="bg-white border-b border-neutral-200">
                <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2 text-[#0081A7]">
                  <CreditCard class="w-5 h-5 text-[#0081A7]" />
                  Payment Information
                </CardTitle>
              </CardHeader>

              <CardContent class="pt-6 space-y-6">
                <!-- Payment Method Display (Read-only) -->
                <div>
                  <label class="block text-sm font-medium text-[#0081A7] mb-3">
                    Payment Method
                  </label>
                  <div class="p-4 bg-[#0081A7]/5 rounded-lg border-2 border-[#0081A7]">
                    <div class="flex items-center gap-3">
                      <component 
                        :is="selectedMethod === 'credit_card' ? CardIcon : Wallet"
                        class="w-6 h-6 text-[#0081A7]"
                      />
                      <div class="flex-1">
                        <p class="font-semibold text-neutral-900">
                          {{ selectedMethod === 'credit_card' ? 'Credit/Debit Card' : selectedMethod === 'gcash' ? 'GCash' : 'PayMaya' }}
                        </p>
                        <p class="text-sm text-neutral-600">Selected from previous page</p>
                      </div>
                      <Badge class="bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white border-0">Active</Badge>
                    </div>
                  </div>
                </div>

                <!-- Test Mode Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <p class="font-semibold text-blue-900 mb-2 text-sm">🧪 Test Mode Active</p>
                  <div class="text-xs text-blue-700 space-y-1">
                    <template v-if="selectedMethod === 'credit_card'">
                      <p>✅ Success: {{ testCredentials.card.success }}</p>
                      <p>❌ Decline: {{ testCredentials.card.failed }}</p>
                    </template>
                    <template v-else>
                      <p>✅ Success: {{ testCredentials.ewallet.success }}</p>
                      <p>❌ Decline: {{ testCredentials.ewallet.failed }}</p>
                    </template>
                  </div>
                </div>

                <!-- Credit/Debit Card Form -->
                <div v-if="selectedMethod === 'credit_card'" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-[#0081A7] mb-2">
                      Card Number
                    </label>
                    <div class="relative">
                      <input
                        v-model="cardNumber"
                        type="text"
                        placeholder="1234 5678 9012 3456"
                        maxlength="19"
                        class="w-full px-4 py-3 pl-12 text-nuetral-800 border border-[#0081A7]/30 rounded-lg focus:ring-2 focus:ring-[#0081A7] focus:border-[#0081A7] outline-none"
                        @input="e => cardNumber = (e.target as HTMLInputElement).value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim()"
                      />
                      <CreditCard class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-400" />
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-[#0081A7] mb-2">
                      Cardholder Name
                    </label>
                    <input
                      v-model="cardName"
                      type="text"
                      placeholder="JUAN DELA CRUZ"
                      class="w-full px-4 py-3 border border-[#0081A7]/30 rounded-lg focus:ring-2 focus:ring-[#0081A7] focus:border-[#0081A7] outline-none uppercase"
                    />
                    <p v-if="user?.name" class="text-xs text-neutral-500 mt-1">Pre-filled with your account name</p>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm  font-medium text-[#0081A7] mb-2">
                        Expiry Date
                      </label>
                      <input
                        v-model="expiryDate"
                        type="text"
                        placeholder="MM/YY"
                        maxlength="5"
                        class="w-full px-4 py-3 text-neutral-800 border border-[#0081A7]/30 rounded-lg focus:ring-2 focus:ring-[#0081A7] focus:border-[#0081A7] outline-none"
                        @input="e => {
                          const val = (e.target as HTMLInputElement).value.replace(/\D/g, '');
                          expiryDate = val.length >= 2 ? val.slice(0,2) + '/' + val.slice(2,4) : val;
                        }"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-[#0081A7] mb-2">
                        CVV
                      </label>
                      <input
                        v-model="cvv"
                        type="password"
                        placeholder="123"
                        maxlength="3"
                        class="w-full px-4 py-3 text-neutral-800 border border-[#0081A7]/30 rounded-lg focus:ring-2 focus:ring-[#0081A7] focus:border-[#0081A7] outline-none"
                        @input="e => cvv = (e.target as HTMLInputElement).value.replace(/\D/g, '').slice(0,3)"
                      />
                    </div>
                  </div>
                </div>

                <!-- E-Wallet Form -->
                <div v-else class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-[#0081A7] mb-2">
                      Mobile Number
                    </label>
                    <div class="relative">
                      <input
                        v-model="ewalletNumber"
                        type="text"
                        placeholder="09123456789"
                        maxlength="11"
                        class="w-full px-4 py-3 pl-12 text-neutral-800 border border-[#0081A7]/30 rounded-lg focus:ring-2 focus:ring-[#0081A7] focus:border-[#0081A7] outline-none"
                        @input="e => ewalletNumber = (e.target as HTMLInputElement).value.replace(/\D/g, '').slice(0,11)"
                      />
                      <Wallet class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-800" />
                    </div>
                    <p class="text-xs text-neutral-500 mt-1">11-digit Philippine mobile number</p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-[#0081A7] mb-2">
                      Email Address
                    </label>
                    <input
                      v-model="ewalletEmail"
                      type="email"
                      placeholder="juan@example.com"
                      class="w-full px-4 py-3 text-nuetral-800 border border-[#0081A7]/30 rounded-lg focus:ring-2 focus:ring-[#0081A7] focus:border-[#0081A7] outline-none"
                    />
                    <p class="text-xs text-neutral-500 mt-1">Registered {{ selectedMethod === 'gcash' ? 'GCash' : 'PayMaya' }} email</p>
                  </div>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage && paymentStatus === 'idle'" class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                  <AlertCircle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                  <p>{{ errorMessage }}</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t">
                  <Button
                    @click="handleCancel"
                    class="flex-1 py-6 text-base bg-neutral-500 text-white hover:bg-neutral-600 border-0"
                    :disabled="isProcessing"
                  >
                    Cancel Payment
                  </Button>
                  <Button
                    @click="processPayment"
                    class="flex-1 py-6 text-base bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white hover:from-[#0081A7]/90 hover:to-[#00AFB9]/90 border-0"
                    :disabled="isProcessing"
                  >
                    Pay {{ formattedAmount }}
                  </Button>
                </div>
              </CardContent>
            </div>
          </Card>
        </div>

        <!-- Right Column: Booking Summary -->
        <div class="lg:col-span-1">
          <div class="sticky top-24 space-y-4">
            <Card class="border-2 border-[#0081A7] shadow-xl bg-white">
              <CardHeader class="bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white">
                <CardTitle class="text-xl font-['Roboto']">Payment Summary</CardTitle>
              </CardHeader>
              <CardContent class="pt-6 space-y-4">
                <div>
                  <p class="text-sm text-neutral-500 mb-1">Vehicle</p>
                  <p class="font-semibold text-neutral-900">{{ booking.vehicle_name }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-neutral-500 mb-1 flex items-center gap-1">
                      <Calendar class="w-3 h-3" />
                      Pickup
                    </p>
                    <p class="text-sm font-medium text-neutral-900">{{ new Date(booking.pickup_date).toLocaleDateString() }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-neutral-500 mb-1 flex items-center gap-1">
                      <Calendar class="w-3 h-3" />
                      Return
                    </p>
                    <p class="text-sm font-medium text-neutral-900">{{ new Date(booking.return_date).toLocaleDateString() }}</p>
                  </div>
                </div>

                <div class="pt-4 border-t">
                  <p class="text-sm text-neutral-500 mb-1">Reference Number</p>
                  <p class="font-mono text-xs font-semibold text-neutral-900 bg-neutral-50 px-2 py-1 rounded">{{ booking.reference_number }}</p>
                </div>

                <div class="pt-4 border-t-2 border-dashed">
                  <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-neutral-900">Total Amount</span>
                    <span class="text-2xl font-bold text-[#0081A7]">{{ formattedAmount }}</span>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Security Notice -->
            <Card class="bg-neutral-50">
              <CardContent class="pt-6">
                <div class="flex items-start gap-3 text-xs text-neutral-600">
                  <Shield class="w-5 h-5 text-[#0081A7] flex-shrink-0" />
                  <p>Your payment information is encrypted and secured with industry-standard 256-bit SSL encryption.</p>
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