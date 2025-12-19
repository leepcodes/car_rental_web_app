<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import { 
  ArrowLeft,
  Shield,
  CheckCircle,
  AlertCircle,
  Lock
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import axios from 'axios';

const props = withDefaults(
  defineProps<{
    canRegister?: boolean;
    vehicleId?: string | number;
    vehicleName?: string;
  }>(),
  { 
    canRegister: true
  }
);

// OTP input refs
const otp = ref(['', '', '', '', '', '']);
const inputRefs = ref<HTMLInputElement[]>([]);
const isVerifying = ref(false);
const isSending = ref(false);
const codeSent = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const canResend = ref(false);
const countdown = ref(0);
let countdownInterval: ReturnType<typeof setInterval> | null = null;

// Get CSRF token
const getCsrfToken = () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  return token || '';
};

// Send OTP code - Call backend API
const sendCode = async () => {
  isSending.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const response = await axios.post('/api/otp/generate', {}, {
      headers: {
        'X-CSRF-TOKEN': getCsrfToken(),
        'Accept': 'application/json',
      }
    });

    if (response.data.success) {
      codeSent.value = true;
      successMessage.value = 'Verification code sent to your email!';
      canResend.value = false;
      countdown.value = 60;

      // Start countdown
      if (countdownInterval) clearInterval(countdownInterval);
      countdownInterval = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
          if (countdownInterval) clearInterval(countdownInterval);
          canResend.value = true;
        }
      }, 1000);

      // Auto-focus first input after code is sent
      nextTick(() => {
        focusInput(0);
      });

      // Show debug code if available (development only)
      if (response.data.debug_code) {
        console.log('🔐 OTP Code (DEV ONLY):', response.data.debug_code);
      }
    }
  } catch (error: any) {
    console.error('Failed to send OTP:', error);
    errorMessage.value = error.response?.data?.message || 'Failed to send verification code. Please try again.';
  } finally {
    isSending.value = false;
  }
};

// Focus management
const focusInput = (index: number) => {
  nextTick(() => {
    if (inputRefs.value[index]) {
      inputRefs.value[index].focus();
    }
  });
};

// Handle input
const handleInput = (index: number, event: Event) => {
  const input = event.target as HTMLInputElement;
  const value = input.value;

  // Only allow numbers
  if (value && !/^\d$/.test(value)) {
    input.value = '';
    return;
  }

  otp.value[index] = value;

  // Move to next input if value entered
  if (value && index < 5) {
    focusInput(index + 1);
  }

  // Auto-verify when all digits entered
  if (isOtpComplete.value) {
    nextTick(() => {
      verifyOtp();
    });
  }
};

// Handle paste
const handlePaste = (event: ClipboardEvent) => {
  event.preventDefault();
  const pasteData = event.clipboardData?.getData('text');
  
  if (pasteData) {
    const digits = pasteData.replace(/\D/g, '').slice(0, 6).split('');
    digits.forEach((digit, index) => {
      if (index < 6) {
        otp.value[index] = digit;
      }
    });
    
    // Focus last filled input or first empty
    const lastIndex = Math.min(digits.length - 1, 5);
    focusInput(lastIndex);

    // Auto-verify if complete
    if (digits.length === 6) {
      nextTick(() => {
        verifyOtp();
      });
    }
  }
};

// Handle backspace
const handleKeydown = (index: number, event: KeyboardEvent) => {
  if (event.key === 'Backspace') {
    if (!otp.value[index] && index > 0) {
      // Move to previous input if current is empty
      focusInput(index - 1);
    } else {
      // Clear current input
      otp.value[index] = '';
    }
  }
};

// Check if OTP is complete
const isOtpComplete = computed(() => {
  return otp.value.every(digit => digit !== '');
});

// Get OTP code as string
const otpCode = computed(() => {
  return otp.value.join('');
});

// Verify OTP - Call backend API
const verifyOtp = async () => {
  if (!isOtpComplete.value || isVerifying.value) return;

  isVerifying.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const response = await axios.post('/api/otp/verify', {
      code: otpCode.value,
      vehicle_id: props.vehicleId || null
    }, {
      headers: {
        'X-CSRF-TOKEN': getCsrfToken(),
        'Accept': 'application/json',
      }
    });

    if (response.data.success) {
      successMessage.value = 'Email verified successfully!';
      
      // Wait a moment to show success message
      setTimeout(() => {
        // Redirect to the URL provided by backend
        if (response.data.redirect_url) {
          window.location.href = response.data.redirect_url;
        } else if (props.vehicleId) {
          router.visit(`/client/booking/${props.vehicleId}`);
        } else {
          router.visit('/client/booking');
        }
      }, 1000);
    }
  } catch (error: any) {
    console.error('OTP verification failed:', error);
    isVerifying.value = false;
    
    // Display error message
    errorMessage.value = error.response?.data?.message || 'Invalid verification code. Please try again.';
    
    // Clear OTP inputs but don't redirect
    otp.value = ['', '', '', '', '', ''];
    focusInput(0);
  }
};

// Resend OTP - Call backend API
const resendOtp = async () => {
  if (!canResend.value || isSending.value) return;

  isSending.value = true;
  canResend.value = false;
  countdown.value = 60;
  errorMessage.value = '';
  successMessage.value = '';
  
  // Clear current OTP
  otp.value = ['', '', '', '', '', ''];

  try {
    const response = await axios.post('/api/otp/resend', {}, {
      headers: {
        'X-CSRF-TOKEN': getCsrfToken(),
        'Accept': 'application/json',
      }
    });

    if (response.data.success) {
      successMessage.value = 'New verification code sent!';
      focusInput(0);

      // Start countdown
      if (countdownInterval) clearInterval(countdownInterval);
      countdownInterval = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
          if (countdownInterval) clearInterval(countdownInterval);
          canResend.value = true;
        }
      }, 1000);

      // Show debug code if available (development only)
      if (response.data.debug_code) {
        console.log('🔐 New OTP Code (DEV ONLY):', response.data.debug_code);
      }
    }
  } catch (error: any) {
    console.error('Failed to resend OTP:', error);
    
    if (error.response?.status === 429) {
      // Rate limited
      const retryAfter = error.response.data.retry_after || 60;
      countdown.value = retryAfter;
      errorMessage.value = `Please wait ${retryAfter} seconds before requesting a new code.`;
      
      // Start countdown from retry_after
      if (countdownInterval) clearInterval(countdownInterval);
      countdownInterval = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
          if (countdownInterval) clearInterval(countdownInterval);
          canResend.value = true;
        }
      }, 1000);
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to resend code. Please try again.';
      canResend.value = true;
    }
  } finally {
    isSending.value = false;
  }
};

// Go back to listings
const goBack = () => {
  router.visit('/client/booking');
};

// Cleanup on unmount
const cleanup = () => {
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }
};

// Register cleanup
import { onUnmounted } from 'vue';
onUnmounted(() => {
  cleanup();
});
</script>

<template>
  <Head title="OTP Verification" />
  <AppHeader :can-register="canRegister" />
  
  <div class="min-h-screen bg-gradient-to-br from-[#0081A7] via-[#006d91] to-[#00AFB9] flex items-center justify-center py-12 px-4 relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute w-96 h-96 bg-white/5 rounded-full -top-48 -left-48 animate-pulse"></div>
      <div class="absolute w-[500px] h-[500px] bg-white/5 rounded-full -bottom-48 -right-48 animate-pulse delay-1000"></div>
      <div class="absolute w-72 h-72 bg-white/10 rounded-full top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
      <!-- Back Button -->
      <button
        @click="goBack"
        class="flex items-center gap-2 text-white hover:text-white/80 transition-colors group mb-6 backdrop-blur-sm bg-white/10 px-4 py-2 rounded-lg"
      >
        <ArrowLeft class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
        <span class="font-medium">Back to Listings</span>
      </button>

      <!-- OTP Card -->
      <Card class="border-0 shadow-2xl overflow-hidden backdrop-blur-sm bg-white/95">
        <!-- Header -->
        <CardHeader class="bg-gradient-to-br from-[#0081A7] to-[#00AFB9] text-white text-center pb-8">
          <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
            <Lock class="w-10 h-10 text-white" />
          </div>
          <CardTitle class="text-2xl font-bold font-['Roboto'] mb-2">
            Verify Your Email
          </CardTitle>
          <CardDescription class="text-white/90 text-base">
            We'll send a 6-digit code to your registered email
          </CardDescription>
        </CardHeader>

        <CardContent class="pt-8 pb-8">
          <!-- Vehicle Info (optional) -->
          <div v-if="vehicleName" class="mb-6 p-3 bg-[#00AFB9]/10 rounded-lg border border-[#00AFB9]/30 text-center">
            <p class="text-sm text-neutral-600">Booking for:</p>
            <p class="font-semibold text-neutral-900">{{ vehicleName }}</p>
          </div>

          <!-- Send Code Section (Before OTP is sent) -->
          <div v-if="!codeSent" class="text-center">
            <p class="text-neutral-600 mb-6">
              Click the button below to receive a verification code via email.
            </p>
            <button
              @click="sendCode"
              :disabled="isSending"
              class="w-full py-4 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white rounded-lg font-bold text-lg hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed font-['Roboto'] flex items-center justify-center gap-2"
            >
              <div v-if="isSending" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              <span v-if="isSending">Sending Code...</span>
              <span v-else>Send Verification Code</span>
            </button>
          </div>

          <!-- OTP Input Section (After code is sent) -->
          <div v-else>
            <!-- Success Message -->
            <div v-if="successMessage && !errorMessage" class="mb-6 p-3 bg-green-50 border border-green-200 rounded-lg text-center">
              <p class="text-sm text-green-800 flex items-center justify-center gap-2">
                <CheckCircle class="w-4 h-4" />
                {{ successMessage }}
              </p>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="mb-6 p-3 bg-red-50 border border-red-200 rounded-lg text-center">
              <p class="text-sm text-red-800 flex items-center justify-center gap-2">
                <AlertCircle class="w-4 h-4" />
                {{ errorMessage }}
              </p>
            </div>

            <!-- OTP Input -->
            <div class="mb-6">
              <div class="flex justify-center gap-2 mb-4">
                <input
                  v-for="(digit, index) in otp"
                  :key="index"
                  :ref="el => { if (el) inputRefs[index] = el as HTMLInputElement }"
                  v-model="otp[index]"
                  type="text"
                  inputmode="numeric"
                  maxlength="1"
                  @input="handleInput(index, $event)"
                  @keydown="handleKeydown(index, $event)"
                  @paste="index === 0 ? handlePaste($event) : null"
                  :disabled="isVerifying"
                  :class="[
                    'w-12 h-14 text-center text-2xl font-bold border-2 rounded-lg transition-all outline-none',
                    errorMessage 
                      ? 'border-red-500 bg-red-50 text-red-600' 
                      : digit 
                        ? 'border-[#00AFB9] bg-[#00AFB9]/5 text-[#0081A7]' 
                        : 'border-neutral-300 hover:border-[#0081A7] focus:border-[#00AFB9] focus:ring-2 focus:ring-[#00AFB9]/20',
                    isVerifying && 'opacity-50 cursor-not-allowed'
                  ]"
                />
              </div>

              <!-- Loading State -->
              <div v-if="isVerifying" class="text-center">
                <div class="inline-flex items-center gap-2 text-[#0081A7]">
                  <div class="w-5 h-5 border-2 border-[#0081A7] border-t-transparent rounded-full animate-spin"></div>
                  <span class="text-sm font-medium">Verifying code...</span>
                </div>
              </div>
            </div>

            <!-- Resend OTP -->
            <div class="text-center">
              <p class="text-sm text-neutral-600 mb-2">Didn't receive the code?</p>
              <button
                @click="resendOtp"
                :disabled="!canResend || isSending"
                :class="[
                  'text-sm font-semibold transition-colors inline-flex items-center gap-2',
                  canResend && !isSending
                    ? 'text-[#0081A7] hover:text-[#00AFB9] cursor-pointer' 
                    : 'text-neutral-400 cursor-not-allowed'
                ]"
              >
                <div v-if="isSending" class="w-4 h-4 border-2 border-[#0081A7] border-t-transparent rounded-full animate-spin"></div>
                <span v-if="isSending">Sending...</span>
                <span v-else-if="canResend">Resend Code</span>
                <span v-else>Resend in {{ countdown }}s</span>
              </button>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Security Note -->
      <div class="mt-6 p-4 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg">
        <div class="flex gap-3">
          <Shield class="w-5 h-5 text-[#0081A7] flex-shrink-0 mt-0.5" />
          <div>
            <p class="text-sm font-semibold text-neutral-900 mb-1">Secure Verification</p>
            <p class="text-xs text-neutral-600">
              This OTP is valid for 10 minutes. Never share this code with anyone.
            </p>
          </div>
        </div>
      </div>

      <!-- Help Text -->
      <div class="mt-4 text-center">
        <p class="text-xs text-white/80">
          Having trouble? Contact our support team at
          <a href="#" class="text-white hover:underline font-semibold">support@carrental.com</a>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Ensure Roboto font is used */
* {
  font-family: 'Roboto', sans-serif;
}

/* Remove number input spinners */
input[type="text"]::-webkit-outer-spin-button,
input[type="text"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="text"] {
  -moz-appearance: textfield;
}
</style>