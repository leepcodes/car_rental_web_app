<script setup>
import ProcessStep from '@/pages/clientSide/components/ProcessStep.vue';

const steps = [
    { number: 1, title: 'Search', description: 'Find your perfect vehicle' },
    { number: 2, title: 'Book', description: 'Reserve online in seconds' },
    { number: 3, title: 'Drive', description: 'Pick up and hit the road' },
];
</script>

<template>
  <div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-10 mb-10 md:mb-12">
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
    
    <div class="flex justify-center">
      <button class="px-7 py-3 bg-neutral-800 text-white rounded-full hover:bg-neutral-700 active:scale-[0.98] transition-all font-semibold text-sm md:text-base shadow-md">
        Start Booking
      </button>
    </div>


 </div>  

</template>