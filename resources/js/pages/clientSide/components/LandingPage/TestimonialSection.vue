<!-- TestimonialSection.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import StatCard from '@/pages/clientSide/clientsView/Booking/StatCard.vue';

interface Testimonial {
  id: number;
  name: string;
  role: string;
  comment: string;
  avatar: string;
  rating: number;
}

interface InfluencerSection {
  id: number;
  influencer: {
    name: string;
    title: string;
    image: string;
    followers: string;
  };
  testimonials: Testimonial[];
}

const sections = ref<InfluencerSection[]>([
  {
    id: 1,
    influencer: {
      name: 'Jessica Martinez',
      title: 'Travel & Lifestyle Influencer',
      image: '👩‍✈️',
      followers: '250K'
    },
    testimonials: [
      {
        id: 1,
        name: 'David Wilson',
        role: 'Photographer',
        comment: 'Used their service for my cross-country road trip. Excellent vehicle condition and amazing customer support throughout my journey!',
        avatar: '📸',
        rating: 5
      },
      {
        id: 2,
        name: 'Lisa Anderson',
        role: 'Marketing Manager',
        comment: 'Professional, reliable, and affordable. The booking process was super easy and the car exceeded my expectations. Will definitely use again!',
        avatar: '💼',
        rating: 5
      },
      {
        id: 3,
        name: 'James Lee',
        role: 'Entrepreneur',
        comment: 'Great fleet selection and transparent pricing. No hidden fees, just honest service. Highly recommend for business travelers!',
        avatar: '🚀',
        rating: 5
      }
    ]
  },
  {
    id: 2,
    influencer: {
      name: 'Marcus Thompson',
      title: 'Automotive Reviewer',
      image: '👨‍🔧',
      followers: '180K'
    },
    testimonials: [
      {
        id: 4,
        name: 'Sophie Chen',
        role: 'Event Planner',
        comment: 'Organized multiple events and always relied on their service for client transportation. Never disappointed with the quality!',
        avatar: '🎉',
        rating: 5
      },
      {
        id: 5,
        name: 'Robert Garcia',
        role: 'Sales Director',
        comment: 'The team went above and beyond to accommodate my last-minute booking. Exceptional service and well-maintained vehicles.',
        avatar: '💎',
        rating: 5
      },
      {
        id: 6,
        name: 'Amanda White',
        role: 'Fitness Coach',
        comment: 'Perfect for my outdoor adventure trips! Clean, reliable, and the staff is incredibly helpful. Best rental experience ever!',
        avatar: '⛰️',
        rating: 5
      }
    ]
  }
]);
</script>

<template>
  <section class="py-16 md:py-24 px-4 md:px-6 lg:px-8 bg-gradient-to-b from-neutral-50 via-white to-neutral-50">
    <div class="mx-auto max-w-7xl">
      <!-- Header -->
      <div class="text-center mb-16 md:mb-20">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-neutral-800 mb-4">
          Trusted by Influencers & Travelers
        </h2>
        <p class="text-base md:text-lg text-neutral-600 max-w-2xl mx-auto">
          See what industry leaders and satisfied customers say about our service
        </p>
      </div>

      <!-- Influencer Sections -->
      <div class="space-y-24 md:space-y-32">
        <!-- First Influencer - Image Right, Comments Left -->
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
          <!-- Comments Left -->
          <div class="space-y-4 md:space-y-6 order-2 lg:order-1">
            <div
              v-for="testimonial in sections[0].testimonials"
              :key="testimonial.id"
              class="group cursor-pointer transition-all duration-500 transform hover:translate-x-2"
            >
              <div class="bg-white rounded-2xl p-5 md:p-6 shadow-md hover:shadow-2xl transition-all duration-300 border-2 border-neutral-100 hover:border-destructive/50">
                <!-- Rating -->
                <div class="flex gap-1 mb-3">
                  <span v-for="star in testimonial.rating" :key="star" class="text-yellow-400 text-lg">⭐</span>
                </div>
                
                <!-- Comment -->
                <p class="text-neutral-700 text-sm md:text-base leading-relaxed mb-4">
                  "{{ testimonial.comment }}"
                </p>
                
                <!-- Author -->
                <div class="flex items-center gap-3">
                  <div class="text-3xl">{{ testimonial.avatar }}</div>
                  <div>
                    <h4 class="font-bold text-neutral-800 text-sm md:text-base">{{ testimonial.name }}</h4>
                    <p class="text-neutral-500 text-xs md:text-sm">{{ testimonial.role }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Influencer Image Right -->
          <div class="order-1 lg:order-2">
            <div class="relative">
              <!-- Main Card -->
              <div class="bg-gradient-to-br from-destructive/10 to-neutral-100 rounded-3xl p-8 md:p-10 shadow-2xl">
                <!-- Image Container -->
                <div class="aspect-[3/4] bg-gradient-to-br from-destructive/20 to-neutral-200 rounded-2xl mb-6 flex items-center justify-center overflow-hidden shadow-lg">
                  <div class="text-9xl transform group-hover:scale-110 transition-transform duration-500">
                    {{ sections[0].influencer.image }}
                  </div>
                </div>
                
                <!-- Influencer Info -->
                <div class="text-center">
                  <h3 class="text-2xl md:text-3xl font-bold text-neutral-800 mb-2">
                    {{ sections[0].influencer.name }}
                  </h3>
                  <p class="text-neutral-600 mb-4">{{ sections[0].influencer.title }}</p>
                  <div class="inline-flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-md">
                    <span class="text-xl">👥</span>
                    <span class="font-bold text-destructive">{{ sections[0].influencer.followers }}</span>
                    <span class="text-neutral-600 text-sm">Followers</span>
                  </div>
                </div>
              </div>

              <!-- Decorative Elements -->
              <div class="absolute -top-6 -right-6 w-32 h-32 bg-destructive/10 rounded-full blur-3xl"></div>
              <div class="absolute -bottom-6 -left-6 w-40 h-40 bg-neutral-300/20 rounded-full blur-3xl"></div>
            </div>
          </div>
        </div>

        <!-- Second Influencer - Image Left, Comments Right -->
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
          <!-- Influencer Image Left -->
          <div class="order-1">
            <div class="relative">
              <!-- Main Card -->
              <div class="bg-gradient-to-br from-neutral-800 to-neutral-700 rounded-3xl p-8 md:p-10 shadow-2xl">
                <!-- Image Container -->
                <div class="aspect-[3/4] bg-gradient-to-br from-neutral-600 to-neutral-500 rounded-2xl mb-6 flex items-center justify-center overflow-hidden shadow-lg">
                  <div class="text-9xl transform group-hover:scale-110 transition-transform duration-500">
                    {{ sections[1].influencer.image }}
                  </div>
                </div>
                
                <!-- Influencer Info -->
                <div class="text-center">
                  <h3 class="text-2xl md:text-3xl font-bold text-white mb-2">
                    {{ sections[1].influencer.name }}
                  </h3>
                  <p class="text-neutral-300 mb-4">{{ sections[1].influencer.title }}</p>
                  <div class="inline-flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-md">
                    <span class="text-xl">👥</span>
                    <span class="font-bold text-destructive">{{ sections[1].influencer.followers }}</span>
                    <span class="text-neutral-600 text-sm">Followers</span>
                  </div>
                </div>
              </div>

              <!-- Decorative Elements -->
              <div class="absolute -top-6 -left-6 w-32 h-32 bg-neutral-400/20 rounded-full blur-3xl"></div>
              <div class="absolute -bottom-6 -right-6 w-40 h-40 bg-destructive/10 rounded-full blur-3xl"></div>
            </div>
          </div>

          <!-- Comments Right -->
          <div class="space-y-4 md:space-y-6 order-2">
            <div
              v-for="testimonial in sections[1].testimonials"
              :key="testimonial.id"
              class="group cursor-pointer transition-all duration-500 transform hover:-translate-x-2"
            >
              <div class="bg-white rounded-2xl p-5 md:p-6 shadow-md hover:shadow-2xl transition-all duration-300 border-2 border-neutral-100 hover:border-neutral-800/50">
                <!-- Rating -->
                <div class="flex gap-1 mb-3">
                  <span v-for="star in testimonial.rating" :key="star" class="text-yellow-400 text-lg">⭐</span>
                </div>
                
                <!-- Comment -->
                <p class="text-neutral-700 text-sm md:text-base leading-relaxed mb-4">
                  "{{ testimonial.comment }}"
                </p>
                
                <!-- Author -->
                <div class="flex items-center gap-3">
                  <div class="text-3xl">{{ testimonial.avatar }}</div>
                  <div>
                    <h4 class="font-bold text-neutral-800 text-sm md:text-base">{{ testimonial.name }}</h4>
                    <p class="text-neutral-500 text-xs md:text-sm">{{ testimonial.role }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>