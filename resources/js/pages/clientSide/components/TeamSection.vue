
<!-- components/TeamSection.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import TeamCard from './TeamCard.vue';
import { Badge } from '@/components/ui/badge';

const team = ref([
  {
    name: 'Sarah Johnson',
    role: 'CEO & Founder',
    image: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop',
    bio: 'Visionary leader with 15+ years in tech',
    social: {
      linkedin: '#',
      twitter: '#'
    }
  },
  {
    name: 'Michael Chen',
    role: 'CTO',
    image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop',
    bio: 'Engineering excellence and innovation',
    social: {
      linkedin: '#',
      twitter: '#'
    }
  },
  {
    name: 'Emily Rodriguez',
    role: 'Head of Design',
    image: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop',
    bio: 'Creating beautiful user experiences',
    social: {
      linkedin: '#',
      twitter: '#'
    }
  },
  {
    name: 'David Kim',
    role: 'Lead Developer',
    image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop',
    bio: 'Building scalable solutions',
    social: {
      linkedin: '#',
      twitter: '#'
    }
  }
]);
</script>

<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 py-24 md:py-32">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute top-20 right-20 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-20 left-20 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-cyan-500/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:40px_40px]"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Badge -->
      <div class="flex justify-center mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20">
          <span class="text-sm font-medium text-white">Our Team</span>
        </div>
      </div>

      <!-- Heading -->
      <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 text-center">
        Meet Our Team
      </h2>
      
      <!-- Subheading -->
      <p class="text-xl text-neutral-400 text-center mb-16 max-w-2xl mx-auto">
        The talented individuals behind our success, dedicated to delivering exceptional results.
      </p>

      <!-- Team Grid -->
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <TeamCard
          v-for="member in team"
          :key="member.name"
          :name="member.name"
          :role="member.role"
          :image="member.image"
          :bio="member.bio"
          :social="member.social"
        />
      </div>
    </div>
  </section>
</template>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 0.6;
  }
  50% {
    opacity: 0.8;
  }
}

.animate-pulse {
  animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.delay-1000 {
  animation-delay: 1s;
}
</style>
