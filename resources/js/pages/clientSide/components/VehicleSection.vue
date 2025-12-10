<!-- VehiclesSection.vue Component -->
<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/components/ui/carousel";
import { Button } from '@/components/ui/button';
import type { CarouselApi } from "@/components/ui/carousel";
import VehicleCardLand from '@/pages/clientSide/components/VehicleCardLand.vue';

interface Vehicle {
  model: string;
  type: string;
  price: string;
  image: string;
}

const slides = [
  { 
    alt: 'Ford', 
    src: '/images/Ford.png',
    vehicles: [
      { model: 'Mustang GT', type: 'Sports', price: '$89/day', image: '🏎️' },
      { model: 'F-150 Raptor', type: 'Truck', price: '$129/day', image: '🚙' },
      { model: 'Explorer XLT', type: 'SUV', price: '$79/day', image: '🚗' },
      { model: 'Edge ST', type: 'SUV', price: '$85/day', image: '🚙' },
      { model: 'Bronco Sport', type: 'Off-road', price: '$95/day', image: '🚜' },
      { model: 'Escape Hybrid', type: 'Compact SUV', price: '$72/day', image: '🚗' },
    ]
  },
  { 
    alt: 'Toyota', 
    src: '/images/Toyota.png',
    vehicles: [
      { model: 'Camry Hybrid', type: 'Sedan', price: '$69/day', image: '🚗' },
      { model: 'RAV4 Prime', type: 'SUV', price: '$85/day', image: '🚙' },
      { model: 'Tacoma TRD', type: 'Truck', price: '$95/day', image: '🛻' },
      { model: 'Highlander', type: 'SUV', price: '$92/day', image: '🚐' },
      { model: 'Corolla Cross', type: 'Compact SUV', price: '$68/day', image: '🚗' },
      { model: '4Runner TRD Pro', type: 'Off-road', price: '$115/day', image: '🚙' },
    ]
  },
  { 
    alt: 'Honda', 
    src: '/images/Honda.png',
    vehicles: [
      { model: 'Accord Sport', type: 'Sedan', price: '$65/day', image: '🚗' },
      { model: 'CR-V Touring', type: 'SUV', price: '$75/day', image: '🚙' },
      { model: 'Pilot Elite', type: 'SUV', price: '$89/day', image: '🚐' },
      { model: 'Civic Type R', type: 'Sports', price: '$99/day', image: '🏎️' },
      { model: 'HR-V EX', type: 'Compact SUV', price: '$62/day', image: '🚗' },
      { model: 'Passport TrailSport', type: 'SUV', price: '$88/day', image: '🚙' },
    ]
  },
  { 
    alt: 'Hyundai', 
    src: '/images/Hyundai.png',
    vehicles: [
      { model: 'Sonata N Line', type: 'Sedan', price: '$62/day', image: '🚗' },
      { model: 'Tucson Hybrid', type: 'SUV', price: '$72/day', image: '🚙' },
      { model: 'Palisade Calligraphy', type: 'SUV', price: '$99/day', image: '🚐' },
      { model: 'Kona Electric', type: 'Electric SUV', price: '$78/day', image: '⚡' },
      { model: 'Santa Fe', type: 'SUV', price: '$82/day', image: '🚙' },
      { model: 'Elantra N', type:'Sports', price: '$75/day', image: '🏎️' },
    ]
  },
  { 
    alt: 'Suzuki', 
    src: '/images/Suzuki.png',
    vehicles: [
      { model: 'Swift Sport', type: 'Hatchback', price: '$55/day', image: '🚗' },
      { model: 'Vitara GLX', type: 'Compact SUV', price: '$68/day', image: '🚙' },
      { model: 'Jimny 4x4', type: 'Off-road', price: '$79/day', image: '🚙' },
      { model: 'S-Cross', type: 'Crossover', price: '$65/day', image: '🚗' },
      { model: 'Ertiga', type: 'MPV', price: '$62/day', image: '🚐' },
      { model: 'XL7', type: 'SUV', price: '$72/day', image: '🚙' },
    ]
  },
  { 
    alt: 'Mitsubishi', 
    src: '/images/Mitsubishi.svg',
    vehicles: [
      { model: 'Outlander PHEV', type: 'SUV', price: '$82/day', image: '🚙' },
      { model: 'Eclipse Cross', type: 'SUV', price: '$73/day', image: '🚗' },
      { model: 'Pajero Sport', type: 'SUV', price: '$95/day', image: '🚐' },
      { model: 'Xpander', type: 'MPV', price: '$68/day', image: '🚐' },
      { model: 'Mirage G4', type: 'Sedan', price: '$52/day', image: '🚗' },
      { model: 'L200', type: 'Pickup', price: '$89/day', image: '🛻' },
    ]
  },
];

const emblaApi = ref<CarouselApi>();
const selectedIndex = ref(0);
const visibleVehicles = ref(4);

const currentBrand = computed(() => slides[selectedIndex.value]);
const displayedVehicles = computed(() => currentBrand.value.vehicles.slice(0, visibleVehicles.value));
const hasMoreVehicles = computed(() => visibleVehicles.value < currentBrand.value.vehicles.length);

// Function to determine if slide is adjacent to selected
function isAdjacentToSelected(index: number): boolean {
  const totalSlides = slides.length;
  const prevIndex = (selectedIndex.value - 1 + totalSlides) % totalSlides;
  const nextIndex = (selectedIndex.value + 1) % totalSlides;
  return index === prevIndex || index === nextIndex;
}

function onInit(api: CarouselApi) {
  emblaApi.value = api;
  if (api) {
    selectedIndex.value = api.selectedScrollSnap();
    api.on('select', () => {
      selectedIndex.value = api.selectedScrollSnap();
      visibleVehicles.value = 4;
    });
  }
}

function loadMoreVehicles() {
  visibleVehicles.value = Math.min(visibleVehicles.value + 2, currentBrand.value.vehicles.length);
}

function scrollPrev() {
  emblaApi.value?.scrollPrev();
}

function scrollNext() {
  emblaApi.value?.scrollNext();
}

function handleKeyPress(e: KeyboardEvent) {
  if (e.key === 'ArrowLeft') {
    scrollPrev();
  } else if (e.key === 'ArrowRight') {
    scrollNext();
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyPress);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyPress);
});
</script>

<template>
  <section class="py-8 md:py-12 lg:py-16 px-4 md:px-6 lg:px-8 bg-neutral-50 min-h-screen flex items-center">
    <div class="mx-auto max-w-7xl w-full">
      <!-- Header -->
      <div class="text-center max-w-2xl mx-auto mb-8 md:mb-12">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-neutral-800 mb-2 leading-tight">
          Our Fleet
        </h2>
        <p class="text-sm md:text-base text-neutral-600">
          Choose from a diverse range of well-maintained vehicles
        </p>
      </div>

      <!-- Vehicle Carousel with proper spacing -->
      <div class="relative">
        <!-- Extra padding wrapper to prevent clipping -->
        <div class="py-6 md:py-6">
          <Carousel class="w-full " :opts="{ align: 'center', loop: true}" @init-api="onInit">
            <CarouselContent class="mt-20 -ml-2 md:-ml-4">
              <CarouselItem 
                v-for="(slide, i) in slides" 
                :key="i" 
                class="pl-2 md:pl-4 basis-[70%] sm:basis-[45%] lg:basis-[30%] transition-all duration-700"
              >
                <div 
                  class="rounded-lg overflow-hidden bg-white transition-all duration-700"
                  :class="[
                    i === selectedIndex 
                      ? 'aspect-[4/3] shadow-[0_10px_40px_rgba(0,0,0,0.3)] scale-110 md:scale-125 opacity-100 z-20 pointer-events-auto' 
                      : isAdjacentToSelected(i)
                      ? 'aspect-[4/3] shadow-md opacity-50 scale-95 z-10 pointer-events-none grayscale'
                      : 'aspect-[4/3] shadow-sm opacity-0 scale-90 z-0 pointer-events-none grayscale'
                  ]"
                >
                  <img
                    :src="slide.src"
                    :alt="slide.alt"
                    class="w-full h-full object-contain p-4 md:p-6 transition-all duration-700"
                    :class="i === selectedIndex ? 'scale-105 grayscale-0' : 'scale-100 grayscale'"
                  />
                </div>
                <p 
                  class="text-center mt-3 md:mt-4 font-semibold text-sm md:text-base transition-all duration-700"
                  :class="[
                    i === selectedIndex 
                      ? 'text-destructive opacity-100 scale-110' 
                      : isAdjacentToSelected(i)
                      ? 'text-neutral-600 opacity-40'
                      : 'text-neutral-600 opacity-0'
                  ]"
                >
                  {{ slide.alt }}
                </p>
              </CarouselItem>
            </CarouselContent>
            <!-- Navigation Arrows - Custom buttons -->
            <button
              @click="scrollPrev"
              class="absolute -left-4 md:-left-6 top-1/2 -translate-y-1/2 bg-white hover:bg-destructive hover:text-white border-2 border-neutral-200 hover:border-destructive shadow-lg w-10 h-10 md:w-12 md:h-12 transition-all duration-300 z-50 rounded-full flex items-center justify-center"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              @click="scrollNext"
              class="absolute -right-4 md:-right-6 top-1/2 -translate-y-1/2 bg-white hover:bg-destructive hover:text-white border-2 border-neutral-200 hover:border-destructive shadow-lg w-10 h-10 md:w-12 md:h-12 transition-all duration-300 z-50 rounded-full flex items-center justify-center"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </Carousel>
        </div>

        <!-- Additional Control Buttons Below Carousel -->
        <div class="flex justify-center gap-3 md:gap-4 mt-6 md:mt-8">
          <Button
            @click="scrollPrev"
            variant="outline"
            size="lg"
            class="px-5 md:px-7 py-2.5 md:py-3 bg-white hover:bg-destructive hover:text-white border-2 border-neutral-300 hover:border-destructive rounded-lg font-semibold transition-all duration-300 flex items-center gap-2 shadow-md"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="hidden sm:inline">Previous</span>
          </Button>
          
          <Button
            @click="scrollNext"
            variant="outline"
            size="lg"
            class="px-5 md:px-7 py-2.5 md:py-3 bg-white hover:bg-destructive hover:text-white border-2 border-neutral-300 hover:border-destructive rounded-lg font-semibold transition-all duration-300 flex items-center gap-2 shadow-md"
          >
            <span class="hidden sm:inline">Next</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </Button>
        </div>
      </div>

      <!-- Available Vehicles Section -->
      <div class="mt-8 md:mt-12">
        <div class="text-center mb-6 md:mb-8">
          <h3 class="text-2xl md:text-3xl font-bold text-neutral-800 mb-2">
            Available {{ currentBrand.alt }} Vehicles
          </h3>
          <p class="text-sm md:text-base text-neutral-600">Select your perfect ride</p>
        </div>

        <!-- Vehicle Cards Grid - 4 per row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 mb-6 md:mb-8">
          <VehicleCardLand
            v-for="(vehicle, index) in displayedVehicles"
            :key="index"
            :vehicle="vehicle"
          />
        </div>

        <!-- View More Button -->
        <div v-if="hasMoreVehicles" class="flex justify-center mt-6 md:mt-8">
          <button
            @click="loadMoreVehicles"
            variant="outline"
            size="lg"
            class="px-8 md:px-10 py-3 md:py-3.5 bg-white hover:bg-neutral-800 hover:text-white text-neutral-800 rounded-lg font-semibold transition-all duration-300 border-2 border-neutral-300 shadow-md"
          >
            View More Vehicles
          </button>
        </div>
      </div>
    </div>  
  </section>
</template>