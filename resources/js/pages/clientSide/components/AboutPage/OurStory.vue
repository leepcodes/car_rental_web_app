<!-- components/OurStory.vue -->
<script setup lang="ts">
</script>

<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 py-24 md:py-32">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute top-20 right-20 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-20 left-20 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-cyan-500/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)] bg-[size:40px_40px]"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Badge -->
      <div class="flex justify-center mb-6">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20">
          <span class="text-sm font-medium text-white">Our Journey</span>
        </div>
      </div>

      <!-- Heading -->
      <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 text-center">
        Our Story
      </h2>
      
      <!-- Subheading -->
      <p class="text-xl text-neutral-400 text-center mb-16 max-w-2xl mx-auto">
        A decade of innovation, passion, and transforming visions into reality.
      </p>

      <!-- Story Content -->
      <div class="space-y-8">
        <!-- Timeline Item 1 -->
        <div class="relative pl-8 md:pl-12 border-l-2 border-blue-500/30">
          <div class="absolute -left-2 top-0 w-4 h-4 bg-blue-500 rounded-full ring-4 ring-blue-500/20"></div>
          <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-all duration-300">
            <div class="flex items-center gap-3 mb-4">
              <span class="text-2xl font-bold text-blue-400">2014</span>
              <span class="text-sm text-neutral-500">•</span>
              <span class="text-sm font-medium text-neutral-400">The Beginning</span>
            </div>
            <p class="text-lg text-neutral-300 leading-relaxed">
              Our journey began with a simple yet powerful idea: technology should serve humanity, not the other way around. What started as a small team of passionate developers has grown into a thriving community of innovators, designers, and problem-solvers.
            </p>
          </div>
        </div>

        <!-- Timeline Item 2 -->
        <div class="relative pl-8 md:pl-12 border-l-2 border-blue-500/30">
          <div class="absolute -left-2 top-0 w-4 h-4 bg-blue-500 rounded-full ring-4 ring-blue-500/20"></div>
          <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-all duration-300">
            <div class="flex items-center gap-3 mb-4">
              <span class="text-2xl font-bold text-blue-400">Growth</span>
              <span class="text-sm text-neutral-500">•</span>
              <span class="text-sm font-medium text-neutral-400">Expanding Horizons</span>
            </div>
            <p class="text-lg text-neutral-300 leading-relaxed">
              Over the years, we've had the privilege of working with hundreds of clients across various industries, from startups finding their footing to established enterprises undergoing digital transformation. Each project has taught us valuable lessons and reinforced our commitment to excellence.
            </p>
          </div>
        </div>

        <!-- Timeline Item 3 -->
        <div class="relative pl-8 md:pl-12 border-l-2 border-cyan-500/30">
          <div class="absolute -left-2 top-0 w-4 h-4 bg-cyan-500 rounded-full ring-4 ring-cyan-500/20"></div>
          <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-all duration-300">
            <div class="flex items-center gap-3 mb-4">
              <span class="text-2xl font-bold text-cyan-400">Today</span>
              <span class="text-sm text-neutral-500">•</span>
              <span class="text-sm font-medium text-neutral-400">Looking Forward</span>
            </div>
            <p class="text-lg text-neutral-300 leading-relaxed">
              We continue to push boundaries, embrace emerging technologies, and most importantly, listen to our clients. Because at the end of the day, their success is our success. Together, we're building the future.
            </p>
          </div>
        </div>
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