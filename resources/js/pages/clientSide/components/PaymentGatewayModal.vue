<script setup lang="ts">
import { ref, computed } from 'vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { CreditCard, Lock, AlertCircle, CheckCircle2, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button/index';

const props = defineProps<{
  isOpen: boolean;
  amount: number;
  onSuccess: () => void;
  onCancel: () => void;
}>();

const cardNumber = ref('');
const cardName = ref('');
const expiryDate = ref('');
const cvv = ref('');
const isProcessing = ref(false);
const paymentStatus = ref<'idle' | 'processing' | 'success' | 'failed'>('idle');
const errorMessage = ref('');

const formattedAmount = computed(() => {
  return `₱${props.amount.toLocaleString()}`;
});

const formatCardNumber = (value: string) => {
  const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
  const matches = v.match(/\d{4,16}/g);
  const match = (matches && matches[0]) || '';
  const parts = [];

  for (let i = 0, len = match.length; i < len; i += 4) {
    parts.push(match.substring(i, i + 4));
  }

  if (parts.length) {
    return parts.join(' ');
  } else {
    return value;
  }
};

const handleCardNumberInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  cardNumber.value = formatCardNumber(target.value);
};

const handleExpiryInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  let value = target.value.replace(/\D/g, '');
  
  if (value.length >= 2) {
    value = value.slice(0, 2) + '/' + value.slice(2, 4);
  }
  
  expiryDate.value = value;
};

const handleCvvInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  cvv.value = target.value.replace(/\D/g, '').slice(0, 3);
};

const validateForm = () => {
  if (!cardNumber.value || cardNumber.value.replace(/\s/g, '').length !== 16) {
    errorMessage.value = 'Please enter a valid 16-digit card number';
    return false;
  }
  if (!cardName.value || cardName.value.length < 3) {
    errorMessage.value = 'Please enter the cardholder name';
    return false;
  }
  if (!expiryDate.value || expiryDate.value.length !== 5) {
    errorMessage.value = 'Please enter a valid expiry date (MM/YY)';
    return false;
  }
  if (!cvv.value || cvv.value.length !== 3) {
    errorMessage.value = 'Please enter a valid 3-digit CVV';
    return false;
  }
  return true;
};

const processPayment = async () => {
  errorMessage.value = '';
  
  if (!validateForm()) {
    return;
  }

  isProcessing.value = true;
  paymentStatus.value = 'processing';

  // Simulate payment gateway processing (2-3 seconds)
  await new Promise(resolve => setTimeout(resolve, 2500));

  // DUMMY LOGIC: Success if card number ends with even number, fail if odd
  const lastDigit = parseInt(cardNumber.value.replace(/\s/g, '').slice(-1));
  const isSuccess = lastDigit % 2 === 0;

  if (isSuccess) {
    paymentStatus.value = 'success';
    
    // Wait a moment to show success state
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Call the success callback to proceed with booking
    props.onSuccess();
  } else {
    paymentStatus.value = 'failed';
    errorMessage.value = 'Payment declined. Please check your card details or try a different card.';
    isProcessing.value = false;
    
    // Reset status after 3 seconds
    setTimeout(() => {
      paymentStatus.value = 'idle';
    }, 3000);
  }
};

const handleCancel = () => {
  if (!isProcessing.value) {
    cardNumber.value = '';
    cardName.value = '';
    expiryDate.value = '';
    cvv.value = '';
    errorMessage.value = '';
    paymentStatus.value = 'idle';
    props.onCancel();
  }
};

// Test card numbers hint
const testCards = {
  success: '4532 1234 5678 9010', // ends with 0 (even)
  failed: '4532 1234 5678 9011', // ends with 1 (odd)
};
</script>

<template>
  <Dialog :open="isOpen" @update:open="handleCancel">
    <DialogContent class="max-w-md">
      <DialogHeader>
        <DialogTitle class="text-2xl font-bold flex items-center gap-2">
          <CreditCard class="w-6 h-6 text-[#0081A7]" />
          Secure Payment
        </DialogTitle>
        <DialogDescription>
          Complete your payment to confirm your booking
        </DialogDescription>
      </DialogHeader>

      <!-- Processing State -->
      <div v-if="paymentStatus === 'processing'" class="py-8 text-center">
        <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-[#0081A7] border-t-transparent mb-4"></div>
        <p class="text-lg font-semibold text-neutral-700">Processing Payment...</p>
        <p class="text-sm text-neutral-500 mt-2">Please wait while we verify your payment</p>
      </div>

      <!-- Success State -->
      <div v-else-if="paymentStatus === 'success'" class="py-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
          <CheckCircle2 class="w-10 h-10 text-green-600" />
        </div>
        <p class="text-lg font-semibold text-green-700">Payment Successful!</p>
        <p class="text-sm text-neutral-500 mt-2">Creating your booking...</p>
      </div>

      <!-- Failed State -->
      <div v-else-if="paymentStatus === 'failed'" class="py-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
          <X class="w-10 h-10 text-red-600" />
        </div>
        <p class="text-lg font-semibold text-red-700">Payment Failed</p>
        <p class="text-sm text-neutral-600 mt-2">{{ errorMessage }}</p>
      </div>

      <!-- Payment Form -->
      <div v-else class="space-y-4">
        <!-- Amount Display -->
        <div class="bg-gradient-to-r from-[#0081A7]/10 to-[#00AFB9]/10 p-4 rounded-lg border border-[#0081A7]/30">
          <p class="text-sm text-neutral-600 mb-1">Amount to Pay</p>
          <p class="text-3xl font-bold text-[#0081A7]">{{ formattedAmount }}</p>
        </div>

        <!-- Test Card Notice -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs">
          <p class="font-semibold text-blue-900 mb-1">🧪 Test Mode - Use these cards:</p>
          <p class="text-blue-700">✅ Success: {{ testCards.success }}</p>
          <p class="text-blue-700">❌ Decline: {{ testCards.failed }}</p>
        </div>

        <!-- Card Number -->
        <div>
          <label class="block text-sm font-medium text-neutral-700 mb-2">
            Card Number
          </label>
          <div class="relative">
            <input
              type="text"
              :value="cardNumber"
              @input="handleCardNumberInput"
              placeholder="1234 5678 9012 3456"
              maxlength="19"
              class="w-full px-4 py-3 pl-12 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none"
            />
            <CreditCard class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-400" />
          </div>
        </div>

        <!-- Cardholder Name -->
        <div>
          <label class="block text-sm font-medium text-neutral-700 mb-2">
            Cardholder Name
          </label>
          <input
            v-model="cardName"
            type="text"
            placeholder="John Doe"
            class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none"
          />
        </div>

        <!-- Expiry and CVV -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">
              Expiry Date
            </label>
            <input
              :value="expiryDate"
              @input="handleExpiryInput"
              type="text"
              placeholder="MM/YY"
              maxlength="5"
              class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-neutral-700 mb-2">
              CVV
            </label>
            <input
              :value="cvv"
              @input="handleCvvInput"
              type="text"
              placeholder="123"
              maxlength="3"
              class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-[#00AFB9] focus:border-[#00AFB9] outline-none"
            />
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage && paymentStatus === 'idle'" class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
          <AlertCircle class="w-4 h-4 flex-shrink-0" />
          <p>{{ errorMessage }}</p>
        </div>

        <!-- Security Notice -->
        <div class="flex items-center gap-2 text-xs text-neutral-500">
          <Lock class="w-3 h-3" />
          <span>Your payment is secured with 256-bit SSL encryption</span>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4">
          <Button
            @click="handleCancel"
            variant="outline"
            class="flex-1"
            :disabled="isProcessing"
          >
            Cancel
          </Button>
          <Button
            @click="processPayment"
            class="flex-1 bg-gradient-to-r from-[#0081A7] to-[#00AFB9]"
            :disabled="isProcessing"
          >
            Pay {{ formattedAmount }}
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>