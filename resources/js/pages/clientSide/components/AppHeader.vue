<!-- AppHeader.vue Component -->
<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';

defineProps<{
  canRegister?: boolean;
}>();

const mobileMenuOpen = ref(false);
const page = usePage();

const navLinks = [
  { href: '/booking', label: 'Book' },
  { href: '/about', label: 'About' },
  { href: '/contact', label: 'Contact' },
];
</script>

<template>
  <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-neutral-200/50">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="flex h-20 items-center justify-between">
        <!-- Logo -->
        <Link href="/" class="flex items-center gap-2" aria-label="Home">
          <div class="w-8 h-8 bg-neutral-800 rounded-lg"></div>
          <span class="text-lg font-semibold tracking-tight text-neutral-800">CRR Rentals</span>
        </Link>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="px-4 py-2 text-sm text-neutral-600 hover:text-neutral-900 transition-colors"
          >
            {{ link.label }}
          </Link>
        </nav>

        <!-- Auth Links -->
        <div class="hidden md:flex items-center gap-3">
          <template v-if="page.props.auth.user">
            <Link
              :href="dashboard()"
              class="px-5 py-2.5 text-sm font-medium text-neutral-900 hover:bg-neutral-100 rounded-full transition-colors"
            >
              Dashboard
            </Link>
          </template>
          <template v-else>
            <Link
              :href="login()"
              class="px-5 py-2.5 text-sm font-medium text-neutral-900 hover:bg-neutral-100 rounded-full transition-colors"
            >
              Sign In
            </Link>
            <Link
              v-if="canRegister"
              :href="register()"
              class="px-5 py-2.5 text-sm font-medium bg-neutral-800 text-white hover:bg-neutral-700 rounded-full transition-colors"
            >
              Get Started
            </Link>
          </template>
        </div>

        <!-- Mobile Menu Button -->
        <button
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden p-2 hover:bg-neutral-100 rounded-lg transition-colors"
          aria-label="Toggle menu"
        >
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Mobile Navigation -->
      <div v-if="mobileMenuOpen" class="md:hidden border-t border-neutral-200 py-4">
        <nav class="flex flex-col gap-1">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="px-4 py-2.5 text-sm text-neutral-600 hover:bg-neutral-100 rounded-lg transition-colors"
          >
            {{ link.label }}
          </Link>
          <div class="flex flex-col gap-2 pt-3 border-t border-neutral-200 mt-2">
            <template v-if="page.props.auth.user">
              <Link :href="dashboard()" class="px-4 py-2.5 text-sm text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors">
                Dashboard
              </Link>
            </template>
            <template v-else>
              <Link :href="login()" class="px-4 py-2.5 text-sm text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors">
                Sign In
              </Link>
              <Link v-if="canRegister" :href="register()" class="px-4 py-2.5 text-sm bg-neutral-800 text-white hover:bg-neutral-700 rounded-lg transition-colors text-center">
                Get Started
              </Link>
            </template>
          </div>
        </nav>
      </div>
    </div>
  </header>
</template>