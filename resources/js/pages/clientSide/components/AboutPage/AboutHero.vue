<!-- components/AboutHero.vue -->
<script setup lang="ts">
</script>

<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-neutral-600 via-neutral-700 to-black font-roboto">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>

    <!-- Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32 lg:py-40">
      <div class="max-w-4xl mx-auto text-center">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 mb-8">
          <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
          <span class="text-sm font-medium text-white">Learn More About Us</span>
        </div>

        <!-- Main Heading -->
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
          Building the
          <span class="bg-gradient-to-r from-blue-700 via-blue-500 to-sky-400 bg-clip-text text-transparent">
            Future
          </span>
          <br />
          Together
        </h1>

        <!-- Description -->
        <p class="text-lg md:text-xl text-neutral-300 max-w-2xl mx-auto mb-12 leading-relaxed">
          We're passionate about creating innovative solutions that transform businesses 
          and empower people to achieve their full potential.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <a 
            href="#story"
            class="group inline-flex items-center gap-2 px-8 py-4 bg-white text-neutral-900 font-semibold rounded-xl hover:bg-neutral-100 transition-all duration-200 shadow-lg hover:shadow-xl"
          >
            Our Story
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
          </a>
          <a 
            href="#team"
            class="inline-flex items-center gap-2 px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-200"
          >
            Meet the Team
          </a>
        </div>
      </div>

      <!-- Stats Preview -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-20 max-w-4xl mx-auto">
        <div class="text-center">
          <div class="text-3xl md:text-4xl font-bold text-white mb-2">10+</div>
          <div class="text-sm text-neutral-400">Years Experience</div>
        </div>
        <div class="text-center">
          <div class="text-3xl md:text-4xl font-bold text-white mb-2">500+</div>
          <div class="text-sm text-neutral-400">Happy Clients</div>
        </div>
        <div class="text-center">
          <div class="text-3xl md:text-4xl font-bold text-white mb-2">1K+</div>
          <div class="text-sm text-neutral-400">Projects Done</div>
        </div>
        <div class="text-center">
          <div class="text-3xl md:text-4xl font-bold text-white mb-2">50+</div>
          <div class="text-sm text-neutral-400">Team Members</div>
        </div>
      </div>
    </div>

    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 right-0">
      <svg class="w-full h-16 md:h-24" viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#fafaf9"/>
      </svg>
    </div>
  </section>
</template>
