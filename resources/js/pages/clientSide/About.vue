<!-- About Page - Main Component -->
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';    
import AppFooter from '@/pages/clientSide/components/AppFooter.vue';
import AboutHero from './components/AboutHero.vue';
import MissionVision from './components/MissionVision.vue';
import StatsSection from './components/StatsSection.vue';
import OurStory from './components/OurStory.vue';
import ValuesSection from '@/pages/clientSide/components/ValuesSection.vue';
import TeamSection from './components/TeamSection.vue';
import CTASection from './components/CTASection.vue';

withDefaults(
  defineProps<{
    canRegister?: boolean;
  }>(),
  { canRegister: true }
);
</script>

<template>
  <Head title="About Us" />

  <div class="min-h-screen bg-neutral-50">
    <AppHeader :can-register="canRegister" />
    
    <AboutHero />
    <MissionVision />

    <OurStory />
    <ValuesSection />
  
    
    <AppFooter />
  </div>
</template>