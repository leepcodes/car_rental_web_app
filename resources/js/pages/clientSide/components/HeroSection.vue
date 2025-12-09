<!-- HeroSection.vue Component -->
<script setup lang="ts">
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/components/ui/carousel";
import SearchForm from '@/pages/clientSide/components/SearchForm.vue'; 
</script>

<template>
  <section class="pt-40 pb-32 px-6 lg:px-8 bg-gradient-to-b from-neutral-50 to-white">
    <div class="mx-auto max-w-7xl">
      <!-- Hero Content -->
      <div class="flex flex-col lg:flex-row items-center mb-20 gap-12">
        <!-- Text -->
        <div class="max-w-3xl lg:max-w-xl">
          <h1 class="text-6xl md:text-7xl lg:text-7xl font-bold tracking-tight text-neutral-800 mb-8 leading-[1.05]">
            Rent Premium Vehicles
          </h1>
          <p class="text-xl md:text-2xl text-neutral-500 mb-8 leading-relaxed">
            Self-drive rentals made simple. Book online, pick up instantly.
          </p>
          <SearchForm />
        </div>

        <!-- Hero Image -->
        <div class="flex-1">
          <img src="/images/familyPicture.png" alt="Family Picture" class="w-full h-auto object-contain" />
        </div>
      </div>

    </div>
  </section>
</template>
