<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import { Card, CardContent } from '@/components/ui/card';
import {Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/components/ui/carousel"; // added

withDefaults(
  defineProps<{
    canRegister?: boolean;
  }>(),
  { canRegister: true }
);

const mobileMenuOpen = ref(false);
const currentSlide = ref(0);

function toggleMobileMenu() {
  mobileMenuOpen.value = !mobileMenuOpen.value;
}

// Sample slide data
const slides = [
  { src: '/images/vehicle-1.jpg', alt: 'Vehicle 1' },
  { src: '/images/vehicle-2.jpg', alt: 'Vehicle 2' },
  { src: '/images/vehicle-3.jpg', alt: 'Vehicle 3' },
];

// Add vehicle types data
const vehicleTypes = [
  { name: 'Sedan', image: '/images/SEDAN.png' },
  { name: 'SUV', image: '/images/SUV.png' },
  { name: 'Hatchback', image: '/images/HATCHBACK.png' },
  { name: 'MPV', image: '/images/MPV.png' },
  { name: 'Pickup', image: '/images/L300.webp' },
  { name: 'Van', image: '/images/VAN.png' },
];
</script>

<template>
  <Head title="Home" />

  <div class="min-h-screen bg-white text-foreground">
    <!-- HEADER -->
    <header class="sticky top-0 z-50 border-b border-border bg-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
          <!-- Logo -->
          <Link href="/" class="shrink-0" aria-label="Home">
            <span class="text-xl font-bold text-primary">CRR Rentals</span>
          </Link>

          <!-- Desktop Navigation -->
          <nav class="hidden flex-1 items-center justify-center gap-8 md:flex" aria-label="Main navigation">
            <Link href="/book" class="text-sm font-medium text-black hover:text-primary transition-colors">
              Book Now
            </Link>
            <Link href="/about" class="text-sm font-medium text-black hover:text-primary transition-colors">
              About Us
            </Link>
            <Link href="/contact" class="text-sm font-medium text-black hover:text-primary transition-colors">
              Contact Us
            </Link>
          </nav>

          <!-- Auth Links -->
          <div class="hidden items-center gap-3 md:flex">
            <template v-if="$page.props.auth.user">
              <Link
                :href="dashboard()"
                class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted transition-colors"
              >
                Dashboard
              </Link>
            </template>
            <template v-else>
              <Link
                :href="login()"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-black hover:bg-muted transition-colors"
              >
                Sign In
              </Link>
              <Link
                v-if="canRegister"
                :href="register()"
                class="inline-flex items-center gap-2 rounded-lg bg-destructive px-4 py-2 text-sm font-medium text-white hover:bg-destructive/90 transition-colors"
              >
                Register
              </Link>
            </template>
          </div>

          <!-- Mobile Menu Button -->
          <button
            @click="toggleMobileMenu"
            :aria-expanded="mobileMenuOpen"
            class="inline-flex md:hidden items-center justify-center rounded-lg p-2 hover:bg-muted transition-colors"
            aria-label="Toggle menu"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
        </div>

        <!-- Mobile Navigation -->
        <div
          v-if="mobileMenuOpen"
          class="border-t border-border bg-white py-4 md:hidden"
        >
          <nav class="flex flex-col gap-2">
            <Link href="/book" class="block rounded px-3 py-2 text-sm font-medium text-black hover:bg-muted transition-colors">
              Book Now
            </Link>
            <Link href="/about" class="block rounded px-3 py-2 text-sm font-medium text-black hover:bg-muted transition-colors">
              About Us
            </Link>
            <Link href="/contact" class="block rounded px-3 py-2 text-sm font-medium text-black hover:bg-muted transition-colors">
              Contact Us
            </Link>
            <div class="flex flex-col gap-2 border-t border-border pt-2">
              <template v-if="$page.props.auth.user">
                <Link
                  :href="dashboard()"
                  class="block rounded px-3 py-2 text-sm font-medium hover:bg-muted transition-colors"
                >
                  Dashboard
                </Link>
              </template>
              <template v-else>
                <Link
                  :href="login()"
                  class="block rounded px-3 py-2 text-sm font-medium text-black hover:bg-muted transition-colors"
                >
                  Sign In
                </Link>
                <Link
                  v-if="canRegister"
                  :href="register()"
                  class="block rounded bg-destructive px-3 py-2 text-sm font-medium text-white hover:bg-destructive/90 transition-colors text-center"
                >
                  Register
                </Link>
              </template>
            </div>
          </nav>
        </div>
      </div>
    </header>

    <!-- HERO SECTION -->
    <section
      class="relative min-h-screen bg-cover bg-center bg-no-repeat"
      style="background-image: linear-gradient(90deg, rgba(0,0,0,0.45) 0%, hsla(237,57%,35%,0.65) 70%), url('/images/hero-bg.jpg')"
    >
      <div class="mx-auto max-w-[92%] 2xl:max-w-[1600px] px-6 sm:px-8 lg:px-10 py-12 flex flex-col justify-center min-h-screen">
        <!-- Hero Content & Carousel -->
        <div class="w-full flex flex-col lg:flex-row items-center lg:items-start gap-6 xl:gap-12 mb-12">
          <!-- Left: Hero Content -->
          <div class="flex-1 w-full">
            <h1 class="text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-white leading-tight mb-6">
              Cruise under the city lights with
              <span class="block bg-destructive inline-block px-4 py-2 rounded-lg mt-3">CRR Rentals</span>
            </h1>
            <p class="text-lg md:text-xl lg:text-2xl text-white/90 mb-8 lg:mb-10">
              Book now and drive it yourself — clean, simple, reliable.
            </p>

            <!-- Search Card -->
            <form @submit.prevent class="bg-white rounded-2xl shadow-2xl p-6 lg:p-8 w-full max-w-4xl">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 items-end">
                <!-- Vehicle Select -->
                <div class="flex flex-col">
                  <label for="vehicle" class="text-sm font-semibold text-muted-foreground mb-2">
                    Vehicle Type
                  </label>
                  <select
                    id="vehicle"
                    class="rounded-lg border border-input bg-card px-4 py-3 text-base text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                  >
                    <option selected>All Vehicles</option>
                    <option>Sedan</option>
                    <option>SUV</option>
                    <option>Truck</option>
                    <option>Van</option>
                  </select>
                </div>

                <!-- Pickup Date -->
                <div class="flex flex-col">
                  <label for="pickup" class="text-sm font-semibold text-muted-foreground mb-2">
                    Pickup Date
                  </label>
                  <input
                    id="pickup"
                    type="datetime-local"
                    class="rounded-lg border border-input bg-card px-4 py-3 text-base text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                  />
                </div>

                <!-- Return Date -->
                <div class="flex flex-col">
                  <label for="return" class="text-sm font-semibold text-muted-foreground mb-2">
                    Return Date
                  </label>
                  <input
                    id="return"
                    type="datetime-local"
                    class="rounded-lg border border-input bg-card px-4 py-3 text-base text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                  />
                </div>

                <!-- Search Button -->
                <button
                  type="submit"
                  class="w-full rounded-xl bg-destructive px-8 py-3 font-semibold text-white hover:bg-destructive/90 transition-colors flex items-center justify-center gap-2 text-base"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                  Search
                </button>
              </div>
            </form>
          </div>

          <!-- Right: Carousel -->
          <aside class="w-full lg:w-[42%] xl:w-[44%] flex-shrink-0">
            <Carousel
              class="relative w-full"
              :opts="{
                align: 'start',
              }"
            >
              <CarouselContent class="-ml-1">
                <CarouselItem v-for="(slide, i) in slides" :key="i" :index="i" class="pl-1">
                  <div class="p-1">
                    <div class="h-64 sm:h-80 md:h-96 lg:h-[420px] xl:h-[480px] rounded-2xl overflow-hidden shadow-2xl bg-white/10">
                      <img :src="slide.src" :alt="slide.alt" class="w-full h-full object-cover" />
                    </div>
                  </div>
                </CarouselItem>
              </CarouselContent>

              <!-- Buttons -->
              <CarouselPrevious class="absolute left-3 top-1/2 -translate-y-1/2 z-20 bg-white/30 hover:bg-white/50 text-white p-2.5 rounded-full transition-all" />
              <CarouselNext class="absolute right-3 top-1/2 -translate-y-1/2 z-20 bg-white/30 hover:bg-white/50 text-white p-2.5 rounded-full transition-all" />
            </Carousel>

            <!-- Progress Indicators -->
            <div class="mt-4 flex gap-2 h-1 bg-white/20 rounded-full overflow-hidden">
              <div 
                v-for="i in slides.length" 
                :key="i"
                class="flex-1 bg-white/40 transition-all duration-300"
                :class="{ 'bg-destructive scale-y-150': i - 1 === currentSlide }"
              />
            </div>
          </aside>
        </div>

        <!-- Payment Methods -->
        <div class="flex flex-wrap items-center gap-6">
          <span class="text-base text-white/70">Accepted payments:</span>
          <div class="flex gap-4">
            <div class="h-10 w-16 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold text-sm">GCash</div>
            <div class="h-10 w-16 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold text-sm">VISA</div>
            <div class="h-10 w-16 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold text-sm">MC</div>
          </div>
        </div>
      </div>
    </section>

    <!-- VEHICLES SECTION -->
    <section
      class="relative py-20 overflow-hidden"
      style="background: linear-gradient(90deg, hsla(237,57%,35%,0.4) 0%, hsla(237,57%,35%,0.2) 50%, hsla(237,57%,35%,0.08) 100%)"
    >
      <div class="mx-auto max-w-[90%] 2xl:max-w-[1500px] px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold mb-2 text-gray-900">
            <span class="text-destructive">Wide choices</span> of vehicles!
          </h2>
          <p class="text-lg text-gray-700 font-medium">
            Top quality and with the <span class="text-destructive font-semibold">lowest price</span> in the market!
          </p>
        </div>

        <!-- Vehicle Types Grid -->
        <div class="flex flex-col gap-12 mb-20">
          <!-- First Row: 3 vehicles -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="vehicle in vehicleTypes.slice(0, 3)" :key="vehicle.name" class="text-center">
              <div class="mb-4 flex justify-center items-center h-40">
                <img 
                  :src="vehicle.image" 
                  :alt="vehicle.name" 
                  :class="[

                    'object-contain',
                    vehicle.name === 'SUV' ? 'h-60 w-auto' : 'h-40 w-auto'
                  ]"
                />
              </div>
              <p class="font-semibold text-gray-900 text-lg">{{ vehicle.name }}</p>
            </div>
          </div>

          <!-- Second Row: 3 vehicles -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="vehicle in vehicleTypes.slice(3, 6)" :key="vehicle.name" class="text-center">
              <div class="mb-4 flex justify-center items-center h-40">
                <img 
                  :src="vehicle.image" 
                  :alt="vehicle.name" 
                  :class="[

                    'object-contain',
                    vehicle.name === 'Pickup' ? 'h-52 w-auto' : 
                    vehicle.name === 'Van' ? 'h-57 w-auto' : 
                    'h-40 w-auto'
                  ]"
                />
              </div>
              <p class="font-semibold text-gray-900 text-lg">{{ vehicle.name }}</p>
            </div>
          </div>
        </div>

        <!-- Why Choose Section -->
        <div class="text-center mb-16">
          <h3 class="text-2xl md:text-3xl font-bold mb-4 text-gray-900">
            Why choose <span class="text-destructive font-bold underline">CRR Rentals</span> ?
          </h3>
          <p class="text-base text-gray-700 font-medium max-w-3xl mx-auto">
            CRR Rentals is the Philippines' first and leading car rental platform, dedicated to providing competitive
            rates and top-quality vehicles for every journey.
          </p>
        </div>

        <!-- Three Steps -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
          <!-- Find -->
          <div class="text-center">
            <div class="flex justify-center mb-4">
              <div class="w-16 h-16 rounded-full bg-destructive/10 flex items-center justify-center">
                <svg class="w-8 h-8 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
            <h4 class="text-xl font-semibold mb-2 text-gray-900">Find</h4>
            <p class="text-gray-700 font-medium">Set pick up location with your rental date</p>
          </div>

          <!-- Select -->
          <div class="text-center">
            <div class="flex justify-center mb-4">
              <div class="w-16 h-16 rounded-full bg-destructive/10 flex items-center justify-center">
                <svg class="w-8 h-8 text-destructive" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                </svg>
              </div>
            </div>
            <h4 class="text-xl font-semibold mb-2 text-gray-900">Select</h4>
            <p class="text-gray-700 font-medium">Select a vehicle available on your pick up location</p>
          </div>

          <!-- Reserve -->
          <div class="text-center">
            <div class="flex justify-center mb-4">
              <div class="w-16 h-16 rounded-full bg-destructive/10 flex items-center justify-center">
                <svg class="w-8 h-8 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
              </div>
            </div>
            <h4 class="text-xl font-semibold mb-2 text-gray-900">Reserve</h4>
            <p class="text-gray-700 font-medium">Reserve the vehicle and pick it up! Easy!</p>
          </div>
        </div>

        <!-- Book Now Button -->
        <div class="text-center mb-16">
          <button class="px-12 py-3 bg-destructive text-white font-bold rounded-full hover:bg-destructive/90 transition-colors text-lg">
            BOOK NOW
          </button>
        </div>

        <!-- Insurance Partner Section -->
        <div
          class="rounded-lg p-8 md:p-12 overflow-hidden"
          style="background: linear-gradient(90deg, hsla(237,57%,35%,0.9) 0%, hsla(237,57%,35%,0.5) 100%)"
        >
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <!-- Logo -->
            <div class="flex justify-center">
              <div class="text-3xl font-bold text-white">ASSURANCE</div>
            </div>

            <!-- Content -->
            <div>
              <h4 class="text-2xl font-bold mb-3 text-white">Our <span class="text-white underline">trusted insurance partner</span></h4>
              <p class="text-white/90">
                In partnership with Assurance Philippines, CRR Rentals ensures that every vehicle on our platform is properly insured, guaranteeing safety, reliability, and peace of mind in every rental.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
