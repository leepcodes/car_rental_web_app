<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import { 
  Heart, 
  Star, 
  MapPin, 
  Users, 
  Settings, 
  Fuel, 
  Shield, 
  Calendar,
  Clock,
  CheckCircle,
  AlertCircle,
  ArrowLeft,
  Share2,
  MessageCircle
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button/index';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';

interface Host {
  name: string;
  verified: boolean;
  rating: number;
  totalVehicles: number;
  responseTime: string;
  responseRate: number;
}

interface Specifications {
  year: number;
  make: string;
  model: string;
  color: string;
  mileage: string;
  engine: string;
  seats: number;
  doors: number;
  plateNumber: string;
}

interface Insurance {
  included: boolean;
  coverage: string;
  details: string;
}

interface Vehicle {
  id: number;
  name: string;
  type: string;
  images: string[];
  price: number;
  location: string;
  passengers: number;
  transmission: string;
  fuel: string;
  rating: number;
  reviews: number;
  host: Host;
  available: boolean;
  featured: boolean;
  description: string;
  features: string[];
  specifications: Specifications;
  rules: string[];
  insurance: Insurance;
}

interface Props {
  canRegister?: boolean;
  vehicle: Vehicle;
}

const props = withDefaults(defineProps<Props>(), { 
  canRegister: true
});

const currentImageIndex = ref(0);
const isFavorite = ref(false);
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
  return props.vehicle.price * totalDays.value;
});

const selectImage = (index: number) => {
  currentImageIndex.value = index;
};

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value;
};

const goBack = () => {
  router.visit('/client/booking');
};

const handleBooking = () => {
  if (!selectedPickupDate.value || !selectedReturnDate.value) {
    alert('Please select pickup and return dates');
    return;
  }
  
  // Navigate with query params using the new route structure
  router.visit(`/client/booking/${props.vehicle.id}/form`, {
    data: {
      pickupDate: selectedPickupDate.value,
      returnDate: selectedReturnDate.value,
      pickupTime: selectedPickupTime.value,
      returnTime: selectedReturnTime.value,
      totalDays: totalDays.value,
      subtotal: totalPrice.value,
      serviceFee: (totalPrice.value * 0.05),
      totalPrice: (totalPrice.value * 1.05)
    }
  });
};


const contactHost = () => {
  console.log('Contacting host:', props.vehicle.host.name);
  // Open messaging modal or navigate to messaging page
};

const shareVehicle = () => {
  if (navigator.share) {
    navigator.share({
      title: props.vehicle.name,
      text: `Check out this ${props.vehicle.name} available for rent`,
      url: window.location.href
    });
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard!');
  }
};
</script>

<template>
  <Head :title="vehicle.name" />
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
          <span class="font-medium">Back to Listings</span>
        </button>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Vehicle Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Image Gallery -->
          <Card class="overflow-hidden">
            <div class="relative">
              <!-- Main Image -->
              <div class="aspect-video overflow-hidden bg-neutral-200">
                <img
                  v-if="vehicle.images && vehicle.images.length > 0"
                  :src="vehicle.images[currentImageIndex]"
                  :alt="vehicle.name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-neutral-400">
                  No image available
                </div>
              </div>

              <!-- Badges Overlay -->
              <div class="absolute top-4 left-4 flex gap-2">
                <Badge class="bg-[#0081A7] hover:bg-[#0081A7]/90 text-white border-0">
                  {{ vehicle.type }}
                </Badge>
                <Badge v-if="vehicle.featured" class="bg-[#00AFB9] hover:bg-[#00AFB9]/90 text-white border-0">
                  Featured
                </Badge>
              </div>

              <!-- Action Buttons -->
              <div class="absolute top-4 right-4 flex gap-2">
                <button
                  @click="shareVehicle"
                  class="p-2 bg-white/90 hover:bg-white rounded-full shadow-lg transition-colors"
                >
                  <Share2 class="w-5 h-5 text-neutral-700" />
                </button>
                <button
                  @click="toggleFavorite"
                  class="p-2 bg-white/90 hover:bg-white rounded-full shadow-lg transition-colors"
                >
                  <Heart 
                    :class="[
                      'w-5 h-5 transition-colors',
                      isFavorite ? 'fill-red-500 text-red-500' : 'text-neutral-700'
                    ]"
                  />
                </button>
              </div>

              <!-- Thumbnail Gallery -->
              <div v-if="vehicle.images && vehicle.images.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 bg-black/40 backdrop-blur-sm rounded-lg p-2">
                <button
                  v-for="(image, index) in vehicle.images"
                  :key="index"
                  @click="selectImage(index)"
                  :class="[
                    'w-16 h-12 rounded overflow-hidden border-2 transition-all',
                    currentImageIndex === index 
                      ? 'border-[#00AFB9] scale-105' 
                      : 'border-white/50 hover:border-white opacity-70 hover:opacity-100'
                  ]"
                >
                  <img
                    :src="image"
                    :alt="`${vehicle.name} - Image ${index + 1}`"
                    class="w-full h-full object-cover"
                  />
                </button>
              </div>
            </div>
          </Card>

          <!-- Vehicle Title and Rating -->
          <div>
            <div class="flex items-start justify-between mb-2">
              <div>
                <h1 class="text-3xl md:text-4xl font-bold text-neutral-900 font-['Roboto']">
                  {{ vehicle.name }}
                </h1>
                <div class="flex items-center gap-3 mt-2 text-neutral-600">
                  <div class="flex items-center gap-1">
                    <MapPin class="w-4 h-4" />
                    <span>{{ vehicle.location }}</span>
                  </div>
                  <span class="text-neutral-300">•</span>
                  <span class="text-[#0081A7] font-medium">{{ vehicle.specifications.plateNumber }}</span>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-4 py-3 border-y">
              <div class="flex items-center gap-1">
                <Star class="w-5 h-5 fill-amber-400 text-amber-400" />
                <span class="font-bold text-lg">{{ vehicle.rating }}</span>
              </div>
              <span class="text-neutral-400">•</span>
              <span class="text-neutral-600">{{ vehicle.reviews }} reviews</span>
              <span class="text-neutral-400">•</span>
              <Badge variant="outline" class="border-[#00AFB9] text-[#00AFB9]">
                <CheckCircle class="w-3 h-3 mr-1" />
                Verified Vehicle
              </Badge>
            </div>
          </div>

          <!-- Key Specifications -->
          <Card>
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto']">Vehicle Specifications</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="flex flex-col items-center p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg">
                  <Users class="w-8 h-8 text-[#0081A7] mb-2" />
                  <span class="text-2xl font-bold text-neutral-900">{{ vehicle.passengers }}</span>
                  <span class="text-sm text-neutral-600">Passengers</span>
                </div>
                <div class="flex flex-col items-center p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg">
                  <Settings class="w-8 h-8 text-[#0081A7] mb-2" />
                  <span class="text-sm font-bold text-neutral-900 text-center">{{ vehicle.transmission }}</span>
                  <span class="text-sm text-neutral-600">Transmission</span>
                </div>
                <div class="flex flex-col items-center p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg">
                  <Fuel class="w-8 h-8 text-[#0081A7] mb-2" />
                  <span class="text-sm font-bold text-neutral-900">{{ vehicle.fuel }}</span>
                  <span class="text-sm text-neutral-600">Fuel Type</span>
                </div>
                <div class="flex flex-col items-center p-4 bg-gradient-to-br from-[#0081A7]/10 to-[#00AFB9]/10 rounded-lg">
                  <Calendar class="w-8 h-8 text-[#0081A7] mb-2" />
                  <span class="text-2xl font-bold text-neutral-900">{{ vehicle.specifications.year }}</span>
                  <span class="text-sm text-neutral-600">Year</span>
                </div>
              </div>

              <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4 pt-6 border-t">
                <div class="flex justify-between">
                  <span class="text-neutral-600">Make:</span>
                  <span class="font-semibold">{{ vehicle.specifications.make }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-600">Model:</span>
                  <span class="font-semibold">{{ vehicle.specifications.model }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-600">Color:</span>
                  <span class="font-semibold">{{ vehicle.specifications.color }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-600">Mileage:</span>
                  <span class="font-semibold">{{ vehicle.specifications.mileage }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-600">Engine:</span>
                  <span class="font-semibold">{{ vehicle.specifications.engine }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-600">Doors:</span>
                  <span class="font-semibold">{{ vehicle.specifications.doors }}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Description -->
          <Card>
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto']">About This Vehicle</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-neutral-700 leading-relaxed">
                {{ vehicle.description }}
              </p>
            </CardContent>
          </Card>

          <!-- Features -->
          <Card>
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto']">Features & Amenities</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="feature in vehicle.features"
                  :key="feature"
                  class="flex items-center gap-2 p-3 bg-neutral-50 rounded-lg"
                >
                  <CheckCircle class="w-5 h-5 text-[#00AFB9] flex-shrink-0" />
                  <span class="text-sm text-neutral-700">{{ feature }}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Insurance -->
          <Card class="border-[#00AFB9]/30 bg-gradient-to-br from-[#0081A7]/5 to-[#00AFB9]/5">
            <CardHeader>
              <div class="flex items-center gap-2">
                <Shield class="w-6 h-6 text-[#0081A7]" />
                <CardTitle class="text-xl font-['Roboto']">Insurance Coverage</CardTitle>
              </div>
            </CardHeader>
            <CardContent>
              <div class="space-y-2">
                <div class="flex items-center gap-2">
                  <CheckCircle class="w-5 h-5 text-[#00AFB9]" />
                  <span class="font-semibold text-neutral-900">{{ vehicle.insurance.coverage }}</span>
                </div>
                <p class="text-neutral-600 ml-7">{{ vehicle.insurance.details }}</p>
              </div>
            </CardContent>
          </Card>

          <!-- Rules -->
          <Card>
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto']">Rental Rules & Policies</CardTitle>
            </CardHeader>
            <CardContent>
              <ul class="space-y-3">
                <li
                  v-for="rule in vehicle.rules"
                  :key="rule"
                  class="flex items-start gap-2"
                >
                  <AlertCircle class="w-5 h-5 text-[#0081A7] flex-shrink-0 mt-0.5" />
                  <span class="text-neutral-700">{{ rule }}</span>
                </li>
              </ul>
            </CardContent>
          </Card>

          <!-- Host Information -->
          <Card class="border-[#0081A7]/30">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto']">Hosted By</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#0081A7] to-[#00AFB9] flex items-center justify-center text-white text-2xl font-bold">
                  {{ vehicle.host.name.charAt(0) }}
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold">{{ vehicle.host.name }}</h3>
                    <Badge v-if="vehicle.host.verified" class="bg-[#00AFB9] hover:bg-[#00AFB9]/90 border-0">
                      <CheckCircle class="w-3 h-3 mr-1" />
                      Verified
                    </Badge>
                  </div>
                  <div class="flex items-center gap-1 mb-3">
                    <Star class="w-4 h-4 fill-amber-400 text-amber-400" />
                    <span class="font-semibold">{{ vehicle.host.rating }}</span>
                    <span class="text-neutral-500 text-sm">Host Rating</span>
                  </div>
                  <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                      <div class="font-bold text-neutral-900">{{ vehicle.host.totalVehicles }}</div>
                      <div class="text-neutral-600">Vehicles</div>
                    </div>
                    <div>
                      <div class="font-bold text-neutral-900">{{ vehicle.host.responseTime }}</div>
                      <div class="text-neutral-600">Response Time</div>
                    </div>
                    <div>
                      <div class="font-bold text-neutral-900">{{ vehicle.host.responseRate }}%</div>
                      <div class="text-neutral-600">Response Rate</div>
                    </div>
                  </div>
                  <button
                    @click="contactHost"
                    class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-2 bg-white border-2 border-[#0081A7] text-[#0081A7] rounded-lg hover:bg-[#0081A7] hover:text-white transition-colors font-medium"
                  >
                    <MessageCircle class="w-4 h-4" />
                    Contact Host
                  </button>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Right Column: Booking Card -->
        <div class="lg:col-span-1">
          <div class="sticky top-24">
            <Card class="border-2 border-[#0081A7]/20 shadow-xl">
              <CardHeader class="bg-gradient-to-br from-[#0081A7] to-[#00AFB9] text-white">
                <div class="flex items-baseline gap-2">
                  <span class="text-4xl font-bold font-['Roboto']">₱{{ vehicle.price.toLocaleString() }}</span>
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
                    <span class="text-neutral-600">₱{{ vehicle.price.toLocaleString() }} × {{ totalDays }} day{{ totalDays > 1 ? 's' : '' }}</span>
                    <span class="font-semibold">₱{{ totalPrice.toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-neutral-600">Service fee</span>
                    <span class="font-semibold">₱{{ (totalPrice * 0.05).toLocaleString() }}</span>
                  </div>
                  <div class="flex justify-between pt-2 border-t font-bold text-lg">
                    <span>Total</span>
                    <span class="text-[#0081A7]">₱{{ (totalPrice * 1.05).toLocaleString() }}</span>
                  </div>
                </div>
              </CardContent>
              <CardFooter class="flex flex-col gap-2">
                <button
                  @click="handleBooking"
                  :disabled="!vehicle.available"
                  class="w-full py-3 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white rounded-lg font-semibold hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed font-['Roboto']"
                >
                  {{ vehicle.available ? 'Book Now' : 'Not Available' }}
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
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Ensure Roboto font is used */
* {
  font-family: 'Roboto', sans-serif;
}
</style>