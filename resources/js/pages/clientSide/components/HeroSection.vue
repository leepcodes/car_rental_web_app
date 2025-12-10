<!-- HeroSection.vue Component -->
<script setup lang="ts">
import SearchForm from '@/pages/clientSide/components/SearchForm.vue'; 
</script>

<template>
  <section class="relative pt-32 pb-48 px-6 lg:px-8 overflow-hidden bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:60px_60px]"></div>
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl">
      <!-- Hero Content -->
      <div class="flex flex-col lg:flex-row items-center gap-16">
        <!-- Text Content -->
        <div class="flex-1 space-y-8">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            <span class="text-sm font-medium text-white">Available 24/7</span>
          </div>

          <!-- Main Heading -->
          <h1 style="font-family: 'Roboto', sans-serif" class="text-5xl md:text-6xl lg:text-7xl font-bold tracking-tight text-white leading-[1.1]">
            Rent Premium
            <span class="block mt-2 bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-400 bg-clip-text text-transparent">
              Vehicles
            </span>
          </h1>

          <!-- Description -->
          <p style="font-family: 'Roboto', sans-serif" class="text-xl md:text-2xl text-neutral-300 leading-relaxed max-w-xl">
            Self-drive rentals made simple. Book online, pick up instantly, and hit the road in minutes.
          </p>

          <!-- Features List -->
          <div class="flex flex-wrap gap-6 pt-4">
            <div class="flex items-center gap-2 text-white">
              <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <span style="font-family: 'Roboto', sans-serif" class="text-sm font-medium">Instant Booking</span>
            </div>
            <div class="flex items-center gap-2 text-white">
              <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <span style="font-family: 'Roboto', sans-serif" class="text-sm font-medium">No Hidden Fees</span>
            </div>
            <div class="flex items-center gap-2 text-white">
              <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <span style="font-family: 'Roboto', sans-serif" class="text-sm font-medium">Free Cancellation</span>
            </div>
          </div>

          <!-- Search Form -->
          <div class="pt-4">
            <SearchForm />
          </div>

          <!-- Stats -->
          <div class="flex flex-wrap gap-8 pt-8 border-t border-white/10">
            <div>
              <div class="text-3xl font-bold text-white mb-1">500+</div>
              <div class="text-sm text-neutral-400">Premium Vehicles</div>
            </div>
            <div>
              <div class="text-3xl font-bold text-white mb-1">10K+</div>
              <div class="text-sm text-neutral-400">Happy Customers</div>
            </div>
            <div>
              <div class="text-3xl font-bold text-white mb-1">4.9</div>
              <div class="text-sm text-neutral-400">Average Rating</div>
            </div>
          </div>
        </div>

        <!-- Animated Car Hero -->
        <div class="flex-1 relative h-[400px] lg:h-[500px] flex items-center justify-center overflow-hidden">
          <!-- Animated Car with Pan Effect -->
          <div class="car-pan-animation">
            <img 
              src="/images/Innova.png" 
              alt="Premium Car" 
              class="w-full max-w-xl h-auto object-contain drop-shadow-2xl"
            />
          </div>

          <!-- Floating Card - Price Badge -->
          <div class="absolute top-8 right-8 bg-white rounded-2xl shadow-xl p-4 backdrop-blur-sm z-10 animate-float">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <div class="text-xs text-neutral-500 font-medium">Starting from</div>
                <div class="text-xl font-bold text-neutral-900">₱1,500<span class="text-sm font-normal text-neutral-500">/day</span></div>
              </div>
            </div>
          </div>

          <!-- Floating Card - Rating Badge -->
          <div class="absolute bottom-8 left-8 bg-white rounded-2xl shadow-xl p-4 backdrop-blur-sm z-10 animate-float-delayed">
            <div class="flex items-center gap-3">
              <div class="flex gap-0.5">
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                  <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                  <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                  <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                  <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                  <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                </svg>
              </div>
              <div>
                <div class="text-lg font-bold text-neutral-900">4.9</div>
                <div class="text-xs text-neutral-500">2,450 reviews</div>
              </div>
            </div>
          </div>

          <!-- Decorative Elements -->
          <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl"></div>
          <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-blue-500/20 rounded-full blur-2xl"></div>
          
          <!-- Circular Background Glow -->
          <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-full blur-3xl opacity-50"></div>
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

<style scoped>
.bg-grid-white\/\[0\.02\] {
  background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
}

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

/* Car Pan Animation - Stay, then pan left */
@keyframes pan-left {
  0% {
    transform: translateX(0) translateY(0);
  }
  /* Stay in center for 3 seconds (30% of 10s) */
  30% {
    transform: translateX(0) translateY(0);
  }
  /* Pan to the left */
  60% {
    transform: translateX(-100%) translateY(0);
  }
  /* Reset instantly */
  60.01% {
    transform: translateX(100%) translateY(0);
  }
  /* Come back to center */
  100% {
    transform: translateX(0) translateY(0);
  }
}

.car-pan-animation {
  animation: pan-left 10s ease-in-out infinite;
  will-change: transform;
}

/* Floating Badges */
@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes float-delayed {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-float-delayed {
  animation: float-delayed 3s ease-in-out infinite;
  animation-delay: 1.5s;
}
</style>  