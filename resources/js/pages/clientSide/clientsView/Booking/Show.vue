<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import BookingCard from '@/pages/clientSide/components/BookingCard.vue';
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

interface BookedDate {
  id: number;
  start_date: string;
  end_date: string;
  status: string;
}

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

interface Document {
  id: number;
  type: string;
  url: string;
  full_url: string;
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
  bookedDates: BookedDate[];
  codingDays?: number[];
  featured: boolean;
  description: string;
  features: string[];
  specifications: Specifications;
  rules: string[];
  insurance: Insurance;
  documents?: Document[];
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
 
const selectImage = (index: number) => {
  currentImageIndex.value = index;
};

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value;
};

const goBack = () => {
  router.visit('/client/booking');
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

// Get day name from coding day number
const getCodingDayName = computed(() => {
  if (!props.vehicle.codingDays || props.vehicle.codingDays.length === 0) return null;
  const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  return props.vehicle.codingDays.map(day => dayNames[day]).join(', ');
});
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
          class="flex items-center gap-2 text-neutral-600 hover:text-[blue-600] transition-colors group"
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
          <Card class="overflow-hidden bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
            <div class="relative">
              <!-- Main Image -->
              <div class="aspect-video overflow-hidden bg-gradient-to-br from-neutral-700 via-neutral-800 to-neutral-900">
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
                <Badge class="bg-gradient-to-r from-blue-400 to-blue-500 hover:from-blue-500 hover:to-blue-600 text-white border-0">
                  {{ vehicle.type }}
                </Badge>
                <Badge v-if="vehicle.featured" class="bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-white border-0">
                  Featured
                </Badge>
                <Badge v-if="getCodingDayName" class="bg-orange-500 hover:bg-orange-600 text-white border-0">
                  Coding: {{ getCodingDayName }}
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
                      ? 'border-blue-400 scale-105' 
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
                  <span class="text-[blue-600] font-medium">{{ vehicle.specifications.plateNumber }}</span>
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
                <Badge variant="outline" class="border-blue-400 text-blue-600">
                <CheckCircle class="w-3 h-3 mr-1" />
                Verified Vehicle
              </Badge>
            </div>
          </div>

          <!-- Coding Day Notice -->
          <Card v-if="getCodingDayName" class="border-orange-200 bg-orange-50">
            <CardContent class="pt-6">
              <div class="flex items-start gap-3">
                <AlertCircle class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" />
                <div>
                  <p class="font-semibold text-orange-900">Number Coding Notice</p>
                  <p class="text-sm text-orange-700 mt-1">
                    This vehicle is subject to number coding restrictions on <strong>{{ getCodingDayName }}</strong>. 
                    These dates will be unavailable for booking.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Key Specifications -->
          <Card class="bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] text-white">Vehicle Specifications</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="flex flex-col items-center p-4 bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 rounded-lg">
                  <Users class="w-8 h-8 text-white mb-2" />
                  <span class="text-2xl font-bold text-white">{{ vehicle.passengers }}</span>
                  <span class="text-sm text-neutral-300">Passengers</span>
                </div>
                <div class="flex flex-col items-center p-4 bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 rounded-lg">
                  <Settings class="w-8 h-8 text-white mb-2" />
                  <span class="text-sm font-bold text-white text-center">{{ vehicle.transmission }}</span>
                  <span class="text-sm text-neutral-300">Transmission</span>
                </div>
                <div class="flex flex-col items-center p-4 bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 rounded-lg">
                  <Fuel class="w-8 h-8 text-white mb-2" />
                  <span class="text-sm font-bold text-white">{{ vehicle.fuel }}</span>
                  <span class="text-sm text-neutral-300">Fuel Type</span>
                </div>
                <div class="flex flex-col items-center p-4 bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 rounded-lg">
                  <Calendar class="w-8 h-8 text-white mb-2" />
                  <span class="text-2xl font-bold text-white">{{ vehicle.specifications.year }}</span>
                  <span class="text-sm text-neutral-300">Year</span>
                </div>
              </div>

              <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4 pt-6 border-t border-white/20">
                <div class="flex justify-between">
                  <span class="text-neutral-300">Make:</span>
                  <span class="font-semibold text-white">{{ vehicle.specifications.make }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-300">Model:</span>
                  <span class="font-semibold text-white">{{ vehicle.specifications.model }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-neutral-300">Color:</span>
                  <span class="font-semibold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">{{ vehicle.specifications.color }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-300">Mileage:</span>
                  <span class="font-semibold text-white">{{ vehicle.specifications.mileage }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-300">Engine:</span>
                  <span class="font-semibold text-white">{{ vehicle.specifications.engine }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-neutral-300">Doors:</span>
                  <span class="font-semibold text-white">{{ vehicle.specifications.doors }}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Description -->
          <Card class="bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] text-white">About This Vehicle</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-neutral-300 leading-relaxed">
                {{ vehicle.description }}
              </p>
            </CardContent>
          </Card>

          <!-- Features -->
          <Card class="bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] text-white">Features & Amenities</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="feature in vehicle.features"
                  :key="feature"
                  class="flex items-center gap-2 p-3 bg-white/10 rounded-lg"
                >
                  <CheckCircle class="w-5 h-5 text-green-400 flex-shrink-0" />
                  <span class="text-sm text-white">{{ feature }}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Insurance -->
          <Card class="bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
            <CardHeader>
              <div class="flex items-center gap-2">
                <Shield class="w-6 h-6 text-white" />
                <CardTitle class="text-xl font-['Roboto'] text-white">Insurance Coverage</CardTitle>
              </div>
            </CardHeader>
            <CardContent>
              <div class="space-y-2">
                <div class="flex items-center gap-2">
                  <CheckCircle class="w-5 h-5 text-green-400" />
                  <span class="font-semibold text-white">{{ vehicle.insurance.coverage }}</span>
                </div>
                <p class="text-neutral-300 ml-7">{{ vehicle.insurance.details }}</p>
              </div>
            </CardContent>
          </Card>

          <!-- Rules -->
          <Card class="bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] text-white">Rental Rules & Policies</CardTitle>
            </CardHeader>
            <CardContent>
              <ul class="space-y-3">
                <li
                  v-for="rule in vehicle.rules"
                  :key="rule"
                  class="flex items-start gap-2"
                >
                  <AlertCircle class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" />
                  <span class="text-white">{{ rule }}</span>
                </li>
              </ul>
            </CardContent>
          </Card>

          <!-- Host Information -->
          <Card class="bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
            <CardHeader>
              <CardTitle class="text-xl font-['Roboto'] text-white">Hosted By</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-cyan-400 flex items-center justify-center text-white text-2xl font-bold">
                  {{ vehicle.host.name.charAt(0) }}
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold text-white">{{ vehicle.host.name }}</h3>
                    <Badge v-if="vehicle.host.verified" class="bg-green-500 hover:bg-green-600 border-0">
                      <CheckCircle class="w-3 h-3 mr-1" />
                      Verified
                    </Badge>
                  </div>
                  <div class="flex items-center gap-1 mb-3">
                    <Star class="w-4 h-4 fill-amber-400 text-amber-400" />
                    <span class="font-semibold text-white">{{ vehicle.host.rating }}</span>
                    <span class="text-neutral-300 text-sm">Host Rating</span>
                  </div>
                  <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                      <div class="font-bold text-white">{{ vehicle.host.totalVehicles }}</div>
                      <div class="text-neutral-300">Vehicles</div>
                    </div>
                    <div>
                      <div class="font-bold text-white">{{ vehicle.host.responseTime }}</div>
                      <div class="text-neutral-300">Response Time</div>
                    </div>
                    <div>
                      <div class="font-bold text-white">{{ vehicle.host.responseRate }}%</div>
                      <div class="text-neutral-300">Response Rate</div>
                    </div>
                  </div>
                  <button
                    @click="contactHost"
                    class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-2 bg-white/10 border-2 border-white/30 text-white rounded-lg hover:bg-white hover:text-neutral-900 transition-colors font-medium"
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
          <BookingCard
            :vehicle-id="vehicle.id"
            :price="vehicle.price"
            :booked-dates="vehicle.bookedDates"
            :coding-days="vehicle.codingDays"
          />
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