<!-- components/CTASection.vue -->
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { register } from '@/routes';
</script>

<template>
  <section class="py-16 md:py-24 bg-gradient-to-br from-neutral-600 to-neutral-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h2 class="text-3xl md:text-4xl font-bold mb-6">
        Ready to Work Together?
      </h2>
      <p class="text-xl text-neutral-100 mb-8">
        Let's create something amazing. Get in touch with us today.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <Link 
          href="register"
          class="inline-flex items-center justify-center px-8 py-4 bg-white text-neutral-600 font-semibold rounded-lg hover:bg-neutral-300 hover:text-neutral-100 transition-colors duration-200"
        >
          Get Started
        </Link>
        <a 
          href="#contact"
          class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-neutral-600 transition-colors duration-200"
        >
          Contact Us
        </a>
      </div>
    </div>
  </section>
</template>