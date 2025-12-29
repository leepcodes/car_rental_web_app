<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import { 
  CheckCircle2,
  Calendar,
  Clock,
  MapPin,
  Car,
  Download,
  Mail,
  Phone,
  CreditCard,
  ArrowRight,
  Share2,
  Printer,
  FileText,
  AlertCircle,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

const props = withDefaults(
  defineProps<{
    canRegister?: boolean;
    booking?: {
      id: number;
      reference_number?: string;
      start_date: string;
      end_date: string;
      total_price: number;
      status: string;
      notes?: string;
      created_at: string;
      vehicle: {
        id: number;
        brand: string;
        model: string;
        year: number;
        body_type: string;
        plate_number?: string;
        color?: string;
      };
      payment: {
        id: number;
        reference_number: string;
        payment_method: string;
        amount: number;
        payment_status: string;
        created_at: string;
      };
      operator: {
        id: number;
        name: string;
        email?: string;
        phone?: string;
      };
      client: {
        id: number;
        name: string;
        email?: string;
        phone?: string;
      };
    };
  }>(),
  { 
    canRegister: true
  }
);

// Animation state
const showConfetti = ref(true);

onMounted(() => {
  // Hide confetti after 5 seconds
  setTimeout(() => {
    showConfetti.value = false;
  }, 5000);
});

const vehicleName = computed(() => {
  if (!props.booking?.vehicle) return 'Vehicle';
  const v = props.booking.vehicle;
  return `${v.brand} ${v.model} (${v.year})`;
});

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

const formatDateTime = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const rentalDuration = computed(() => {
  if (!props.booking) return 0;
  const start = new Date(props.booking.start_date);
  const end = new Date(props.booking.end_date);
  return Math.max(1, Math.ceil((end.getTime() - start.getTime()) / (1000 * 60 * 60 * 24)));
});

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    pending: 'bg-yellow-500',
    confirmed: 'bg-green-500',
    completed: 'bg-blue-500',
    cancelled: 'bg-red-500',
  };
  return colors[status] || 'bg-gray-500';
};

const getPaymentStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    completed: 'bg-green-100 text-green-800 border-green-300',
    failed: 'bg-red-100 text-red-800 border-red-300',
  };
  return colors[status] || 'bg-gray-100 text-gray-800 border-gray-300';
};

const handleDownloadReceipt = () => {
  window.print();
};

const handleEmailReceipt = () => {
  alert('Receipt will be sent to your email: ' + (props.booking?.client.email || 'your email'));
};

const handleShareBooking = async () => {
  const shareData = {
    title: 'Car Rental Booking Confirmation',
    text: `Booking Reference: ${props.booking?.payment.reference_number}`,
    url: window.location.href,
  };

  try {
    if (navigator.share) {
      await navigator.share(shareData);
    } else {
      // Fallback: copy to clipboard
      await navigator.clipboard.writeText(window.location.href);
      alert('Booking link copied to clipboard!');
    }
  } catch (err) {
    console.error('Error sharing:', err);
  }
};

const goToMyBookings = () => {
  router.visit('/client/booking');
};

const goBackToHome = () => {
  router.visit('/client/home');
};
</script>

<template>
  <Head title="Booking Confirmation" />
  <AppHeader :can-register="canRegister" />
  
  <div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-neutral-50 relative overflow-hidden">
    <!-- Confetti Animation -->
    <div v-if="showConfetti" class="fixed inset-0 pointer-events-none z-50">
      <div class="confetti">🎉</div>
      <div class="confetti" style="left: 20%; animation-delay: 0.2s;">🎊</div>
      <div class="confetti" style="left: 40%; animation-delay: 0.4s;">✨</div>
      <div class="confetti" style="left: 60%; animation-delay: 0.6s;">🎉</div>
      <div class="confetti" style="left: 80%; animation-delay: 0.8s;">🎊</div>
    </div>

    <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-12">
      <!-- Success Header -->
      <div class="text-center mb-12 animate-fade-in">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-green-400 to-green-600 mb-6 shadow-xl animate-bounce-slow">
          <CheckCircle2 class="w-12 h-12 text-white" />
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-neutral-900 mb-4 font-['Roboto']">
          Booking Confirmed! 🎉
        </h1>
        <p class="text-lg text-neutral-600 max-w-2xl mx-auto">
          Your rental has been successfully confirmed. We've sent a confirmation email with all the details.
        </p>
      </div>

      <!-- Reference Number Banner -->
      <Card class="mb-8 border-2 border-[#0081A7] shadow-xl bg-gradient-to-r from-[#0081A7]/10 to-[#00AFB9]/10">
        <CardContent class="pt-6">
          <div class="text-center">
            <p class="text-sm text-neutral-600 mb-2">Booking Reference Number</p>
            <div class="flex items-center justify-center gap-3">
              <p class="text-3xl md:text-4xl font-bold text-[#0081A7] font-mono tracking-wider">
                {{ booking?.payment.reference_number || 'N/A' }}
              </p>
              <Badge :class="getPaymentStatusColor(booking?.payment.payment_status || 'pending')">
                {{ booking?.payment.payment_status || 'Pending' }}
              </Badge>
            </div>
            <p class="text-xs text-neutral-500 mt-2">
              Please save this reference number for your records
            </p>
          </div>
        </CardContent>
      </Card>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Vehicle Details -->
          <Card class="shadow-lg">
            <CardHeader class="bg-gradient-to-r from-[#0081A7]/10 to-[#00AFB9]/10">
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2">
                <Car class="w-5 h-5 text-[#0081A7]" />
                Vehicle Information
              </CardTitle>
            </CardHeader>
            <CardContent class="pt-6">
              <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#0081A7] to-[#00AFB9] flex items-center justify-center">
                  <Car class="w-8 h-8 text-white" />
                </div>
                <div class="flex-1">
                  <h3 class="text-xl font-bold text-neutral-900">{{ vehicleName }}</h3>
                  <div class="flex items-center gap-2 mt-1">
                    <Badge class="bg-[#0081A7] text-white border-0">
                      {{ booking?.vehicle.body_type || 'Vehicle' }}
                    </Badge>
                    <span v-if="booking?.vehicle.plate_number" class="text-sm text-neutral-600">
                      Plate: {{ booking.vehicle.plate_number }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg">
                  <div class="flex items-center gap-2 text-[#0081A7] mb-2">
                    <Calendar class="w-4 h-4" />
                    <span class="text-sm font-semibold">Pickup Date</span>
                  </div>
                  <p class="font-bold text-neutral-900">{{ formatDate(booking?.start_date || '') }}</p>
                </div>
                <div class="p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg">
                  <div class="flex items-center gap-2 text-[#0081A7] mb-2">
                    <Calendar class="w-4 h-4" />
                    <span class="text-sm font-semibold">Return Date</span>
                  </div>
                  <p class="font-bold text-neutral-900">{{ formatDate(booking?.end_date || '') }}</p>
                </div>
              </div>

              <div class="mt-4 p-3 bg-[#00AFB9]/10 rounded-lg border border-[#00AFB9]/30">
                <p class="text-sm text-neutral-700">
                  <span class="font-semibold">Rental Duration:</span> 
                  {{ rentalDuration }} day{{ rentalDuration > 1 ? 's' : '' }}
                </p>
              </div>
            </CardContent>
          </Card>

          <!-- Operator Information -->
          <Card class="shadow-lg">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2">
                <MapPin class="w-5 h-5 text-[#0081A7]" />
                Operator Information
              </CardTitle>
              <CardDescription>Contact details for pickup and support</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div>
                  <p class="text-sm text-neutral-600 mb-1">Operator Name</p>
                  <p class="font-semibold text-neutral-900">{{ booking?.operator.name || 'N/A' }}</p>
                </div>
                <div v-if="booking?.operator.email" class="flex items-center gap-2">
                  <Mail class="w-4 h-4 text-neutral-500" />
                  <a :href="`mailto:${booking.operator.email}`" class="text-[#0081A7] hover:underline">
                    {{ booking.operator.email }}
                  </a>
                </div>
                <div v-if="booking?.operator.phone" class="flex items-center gap-2">
                  <Phone class="w-4 h-4 text-neutral-500" />
                  <a :href="`tel:${booking.operator.phone}`" class="text-[#0081A7] hover:underline">
                    {{ booking.operator.phone }}
                  </a>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Additional Notes -->
          <Card v-if="booking?.notes" class="shadow-lg">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] flex items-center gap-2">
                <FileText class="w-5 h-5 text-[#0081A7]" />
                Additional Notes
              </CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-neutral-700 whitespace-pre-wrap">{{ booking.notes }}</p>
            </CardContent>
          </Card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Payment Summary -->
          <Card class="shadow-xl border-2 border-[#0081A7]/20 sticky top-24">
            <CardHeader class="bg-gradient-to-br from-[#0081A7] to-[#00AFB9] text-white">
              <CardTitle class="text-xl font-['Roboto']">Payment Summary</CardTitle>
            </CardHeader>
            <CardContent class="pt-6 space-y-4">
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-neutral-600">Payment Method</span>
                  <span class="font-semibold flex items-center gap-1">
                    <CreditCard class="w-4 h-4" />
                    {{ booking?.payment.payment_method || 'Card' }}
                  </span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-neutral-600">Transaction ID</span>
                  <span class="font-mono text-xs">{{ booking?.payment.id || 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-neutral-600">Payment Date</span>
                  <span class="text-xs">{{ formatDateTime(booking?.payment.created_at || '') }}</span>
                </div>
              </div>

              <div class="pt-4 border-t-2 border-dashed">
                <div class="flex justify-between items-center">
                  <span class="text-lg font-bold text-neutral-900">Total Paid</span>
                  <span class="text-2xl font-bold text-[#0081A7]">
                    ₱{{ booking?.payment.amount.toLocaleString() || '0' }}
                  </span>
                </div>
              </div>
            </CardContent>
            <CardFooter class="flex flex-col gap-3 bg-neutral-50">
              <Button
                @click="handleDownloadReceipt"
                variant="outline"
                class="w-full"
              >
                <Download class="w-4 h-4 mr-2" />
                Download Receipt
              </Button>
              <Button
                @click="handleEmailReceipt"
                variant="outline"
                class="w-full"
              >
                <Mail class="w-4 h-4 mr-2" />
                Email Receipt
              </Button>
              <Button
                @click="handleShareBooking"
                variant="outline"
                class="w-full"
              >
                <Share2 class="w-4 h-4 mr-2" />
                Share Booking
              </Button>
            </CardFooter>
          </Card>

          <!-- Important Reminders -->
          <Card class="bg-amber-50 border-amber-200">
            <CardHeader>
              <CardTitle class="text-lg flex items-center gap-2 text-amber-900">
                <AlertCircle class="w-5 h-5" />
                Important Reminders
              </CardTitle>
            </CardHeader>
            <CardContent>
              <ul class="space-y-2 text-sm text-amber-800">
                <li class="flex items-start gap-2">
                  <span class="text-amber-600 mt-0.5">•</span>
                  <span>Arrive 15 minutes early for vehicle inspection</span>
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-amber-600 mt-0.5">•</span>
                  <span>Bring valid driver's license and ID</span>
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-amber-600 mt-0.5">•</span>
                  <span>Security deposit required at pickup</span>
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-amber-600 mt-0.5">•</span>
                  <span>Return with same fuel level</span>
                </li>
                <li class="flex items-start gap-2">
                  <span class="text-amber-600 mt-0.5">•</span>
                  <span>Contact operator for any changes</span>
                </li>
              </ul>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-12 flex flex-col sm:flex-row gap-4 justify-center">
        <Button
          @click="goToMyBookings"
          class="bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white px-8 py-6 text-lg"
          size="lg"
        >
          View My Bookings
          <ArrowRight class="w-5 h-5 ml-2" />
        </Button>
        <Button
          @click="goBackToHome"
          variant="outline"
          size="lg"
          class="px-8 py-6 text-lg"
        >
          Browse More Vehicles
        </Button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Ensure Roboto font is used */
* {
  font-family: 'Roboto', sans-serif;
}

/* Confetti Animation */
.confetti {
  position: absolute;
  top: -10%;
  font-size: 2rem;
  animation: confetti-fall 3s linear forwards;
}

@keyframes confetti-fall {
  0% {
    transform: translateY(0) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(100vh) rotate(720deg);
    opacity: 0;
  }
}

/* Fade in animation */
.animate-fade-in {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Slow bounce */
.animate-bounce-slow {
  animation: bounce-slow 2s infinite;
}

@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
  
  body {
    background: white !important;
  }
  
  .confetti {
    display: none !important;
  }
}
</style>