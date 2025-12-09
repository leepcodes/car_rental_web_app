<!-- VehiclesSection.vue Component -->
<script setup lang="ts">
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/components/ui/carousel";
import ProcessStep from '@/pages/clientSide/components/ProcessStep.vue';

const slides = [
  { alt: 'Sedan', src: '/images/SEDAN.png' },
  { alt: 'SUV', src: '/images/SUV.png' },
  { alt: 'Hatchback', src: '/images/HATCHBACK.png' },
  { alt: 'MPV', src: '/images/MPV.png' },
  { alt: 'Pickup', src: '/images/L300.webp' },
  { alt: 'Van', src: '/images/VAN.png' },
];

const steps = [
  { number: 1, title: 'Search', description: 'Find your perfect vehicle' },
  { number: 2, title: 'Book', description: 'Reserve online in seconds' },
  { number: 3, title: 'Drive', description: 'Pick up and hit the road' },
];
</script>

<template>
  <section class="py-8 md:py-12 lg:py-16 px-4 md:px-6 lg:px-8 bg-neutral-50 min-h-screen flex items-center">
    <div class="mx-auto max-w-7xl w-full">
      <!-- Header -->
      <div class="text-center max-w-2xl mx-auto mb-6 md:mb-8">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-neutral-800 mb-2 leading-tight">
          Our Fleet
        </h2>
        <p class="text-sm md:text-base text-neutral-600">
          Choose from a diverse range of well-maintained vehicles
        </p>
      </div>

      <!-- Vehicle Carousel -->
      <div class="relative mb-6 md:mb-8">
        <Carousel class="w-full" :opts="{ align: 'center', loop: true}">
          <CarouselContent>
            <CarouselItem v-for="(slide, i) in slides" :key="i" class="pl-3 basis-[85%] sm:basis-1/2 lg:basis-1/3">
              <div class="aspect-[4/3] rounded-lg overflow-hidden bg-neutral-100 shadow-md">
                <img
                  :src="slide.src"
                  :alt="slide.alt"
                  class="w-full h-full object-contain p-3"
                />
              </div>
            </CarouselItem>
          </CarouselContent>
          <CarouselPrevious class="left-2 md:left-4 bg-white hover:bg-neutral-50 border-0 shadow-lg w-9 h-9 md:w-10 md:h-10" />
          <CarouselNext class="right-2 md:right-4 bg-white hover:bg-neutral-50 border-0 shadow-lg w-9 h-9 md:w-10 md:h-10" />
        </Carousel>
      </div>

      <!-- How It Works -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mb-10 md:mb-12">
        <div class="border border-neutral-200 rounded-xl p-6 bg-white shadow-sm">
          <ProcessStep
            :number="steps[0].number"
            :title="steps[0].title"
            :description="steps[0].description"
          />
        </div>
        <div class="border border-neutral-200 rounded-xl p-6 bg-white shadow-sm">
          <ProcessStep
            :number="steps[1].number"
            :title="steps[1].title"
            :description="steps[1].description"
          />
        </div>
        <div class="border border-neutral-200 rounded-xl p-6 bg-white shadow-sm">
          <ProcessStep
            :number="steps[2].number"
            :title="steps[2].title"
            :description="steps[2].description"
          />
        </div>
      </div>

      <!-- CTA -->
      <div class="text-center">
        <button class="px-7 py-3 bg-neutral-800 text-white rounded-full hover:bg-neutral-700 active:scale-[0.98] transition-all font-semibold text-sm md:text-base shadow-md">
          Start Booking
        </button>
      </div>
    </div>
  </section>
</template> 