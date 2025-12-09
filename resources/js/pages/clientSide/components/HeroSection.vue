<!-- HeroSection.vue Component -->
<script setup lang="ts">
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/components/ui/carousel";
import SearchForm from '@/pages/clientSide/components/SearchForm.vue';

const slides = [
  { src: '/images/vehicle-1.jpg', alt: 'Vehicle 1' },
  { src: '/images/vehicle-2.jpg', alt: 'Vehicle 2' },
  { src: '/images/vehicle-3.jpg', alt: 'Vehicle 3' },
];
</script>

<template>
  <section class="pt-40 pb-32 px-6 lg:px-8 bg-gradient-to-b from-neutral-50 to-white">
    <div class="mx-auto max-w-7xl">
      <!-- Hero Content -->
      <div class="max-w-3xl mb-20">
        <h1 class="text-6xl md:text-7xl lg:text-8xl font-bold tracking-tight text-neutral-800 mb-8 leading-[1.05]">
          Rent premium vehicles
        </h1>
        <p class="text-xl md:text-2xl text-neutral-500 mb-12 leading-relaxed">
          Self-drive rentals made simple. Book online, pick up instantly.
        </p>

        <SearchForm />
      </div>

      <!-- Carousel -->
      <div class="relative -mx-6 lg:mx-0">
        <Carousel class="w-full" :opts="{ align: 'start', loop: true }">
          <CarouselContent class="ml-6 lg:ml-0">
            <CarouselItem v-for="(slide, i) in slides" :key="i" class="pl-4 md:basis-1/2 lg:basis-1/3">
              <div class="aspect-[4/3] rounded-3xl overflow-hidden bg-neutral-100 shadow-2xl shadow-neutral-900/10">
                <img :src="slide.src" :alt="slide.alt" class="w-full h-full object-cover" />
              </div>
            </CarouselItem>
          </CarouselContent>
          <CarouselPrevious class="left-8 bg-white hover:bg-neutral-50 border-0 shadow-xl shadow-neutral-900/20 w-12 h-12" />
          <CarouselNext class="right-8 bg-white hover:bg-neutral-50 border-0 shadow-xl shadow-neutral-900/20 w-12 h-12" />
        </Carousel>
      </div>
    </div>
  </section>
</template>