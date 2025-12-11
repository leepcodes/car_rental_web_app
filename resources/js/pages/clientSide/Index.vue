<!-- Main Page Component -->
<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import HeroSection from '@/pages/clientSide/components/HeroSection.vue';
import VehiclesSection from '@/pages/clientSide/components/VehicleSection.vue';
import InsuranceSection from '@/pages/clientSide/components/InsuranceSection.vue';
import TestimonialSection from '@/pages/clientSide/components/TestimonialSection.vue';
import AppFooter from '@/pages/clientSide/components/AppFooter.vue';

withDefaults(
  defineProps<{
    canRegister?: boolean;
  }>(),
  { canRegister: true }
);
</script>

<template>
  <Head title="Home" />

  <div class="min-h-screen bg-neutral-50">
    <AppHeader :can-register="canRegister" :can-operator="canRegister"/>
    <HeroSection />
    <VehiclesSection />
    <InsuranceSection />
    <TestimonialSection/>
    <AppFooter />
  </div>
</template>