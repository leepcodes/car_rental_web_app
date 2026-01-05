<!-- AppHeader.vue Component - Improved UI -->
<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { login, register } from '@/routes';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { User, Calendar, LogOut, ChevronDown, Briefcase, Menu, X } from 'lucide-vue-next';

defineProps<{
  canRegister?: boolean;
  canOperator?: boolean;
}>();

const mobileMenuOpen = ref(false);
const page = usePage();

const user = computed(() => page.props.auth.user as any);

const userInitials = computed(() => {
  if (!user.value?.name) return 'U';
  return user.value.name
    .split(' ')
    .map((n: string) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
});

const handleLogout = () => {
  router.post('/logout');
};

const isClient = computed(() => {
  return user.value?.user_type === 'client';
});

const isOperator = computed(() => {
  return user.value?.user_type === 'operator';
});

// Booking link based on user type
const bookingLink = computed(() => {
  if (isClient.value) {
    return '/client/booking';
  }
  return '/booking';
});

// Navigation links - filtered based on user type
const navLinks = computed(() => {
  const baseLinks = [
    { href: bookingLink.value, label: 'Book' },
    { href: '/about', label: 'About' },
    { href: '/contact', label: 'Contact' },
  ];

  // If user is an operator, remove the "Book" link
  if (isOperator.value) {
    return baseLinks.filter(link => link.label !== 'Book');
  }

  return baseLinks;
});

// Show "Apply as Operator" button only for guests (not logged in)
const showApplyAsOperator = computed(() => {
  return !user.value; // Only show if user is not logged in
});

// Dashboard link based on user type
const dashboardLink = computed(() => {
  const userType = user.value?.user_type;
  
  if (userType === 'client') {
    return '/client/booking';
  } else if (userType === 'operator') {
    return '/operator/dashboard';
  }
  
  return '/';
});

// Dashboard label based on user type
const dashboardLabel = computed(() => {
  if (isClient.value) {
    return 'My Bookings';
  } else if (isOperator.value) {
    return 'Dashboard';
  }
  return 'Dashboard';
});
</script>


<template>
  <header class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <!-- Logo -->
        <Link href="/" class="flex items-center gap-3 group" aria-label="Home">
          <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <span class="text-xl font-bold text-gray-900">Uniride</span>
        </Link>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
          >
            {{ link.label }}
          </Link>
        </nav>

        <!-- Auth Links -->
        <div class="hidden md:flex items-center gap-3">
          <!-- Apply as Operator button (visible only for guests) -->
          <template v-if="showApplyAsOperator">
            <Link 
              href="/register?type=operator" 
              class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
            >
              <Briefcase class="h-4 w-4" />
              <span>Apply as Operator</span>
            </Link>
          </template>

          <template v-if="user">
            <!-- User Dropdown -->
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <button class="inline-flex items-center gap-2 px-3 py-2 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200">
                  <Avatar class="h-8 w-8">
                    <AvatarImage 
                      v-if="user?.avatar"
                      :src="user.avatar" 
                      :alt="user.name" 
                    />
                    <AvatarFallback class="bg-blue-600 text-white font-semibold">
                      {{ userInitials }}
                    </AvatarFallback>
                  </Avatar>
                  <span class="text-sm font-medium text-gray-900">{{ user.name }}</span>
                  <ChevronDown class="h-4 w-4 text-gray-500" />
                </button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" class="w-56">
                <DropdownMenuLabel>
                  <div class="flex flex-col space-y-1">
                    <p class="text-sm font-medium leading-none text-gray-900">{{ user.name }}</p>
                    <p class="text-xs leading-none text-gray-500">
                      {{ user.email }}
                    </p>
                  </div>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                
                <!-- Profile Link -->
                <DropdownMenuItem as-child>
                  <Link :href="isClient ? '/client/profile' : '/operator/profile'" class="cursor-pointer">
                    <User class="mr-2 h-4 w-4" />
                    <span>My Profile</span>
                  </Link>
                </DropdownMenuItem>

                <!-- Dashboard/Bookings Link -->
                <DropdownMenuItem as-child>
                  <Link :href="dashboardLink" class="cursor-pointer">
                    <Calendar class="mr-2 h-4 w-4" />
                    <span>{{ dashboardLabel }}</span>
                  </Link>
                </DropdownMenuItem>

                <DropdownMenuSeparator />
                <DropdownMenuItem @click="handleLogout" class="cursor-pointer text-red-600 focus:text-red-600 focus:bg-red-50">
                  <LogOut class="mr-2 h-4 w-4" />
                  <span>Log Out</span>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </template>
          
          <template v-else>
            <!-- Guest users - Sign In and Get Started buttons -->
            <Link
              :href="login()" 
              class="px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
            >
              Sign In
            </Link>
            <Link
              v-if="canRegister"
              :href="register()"
              class="px-5 py-2 text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 rounded-lg transition-colors shadow-sm hover:shadow-md"
            >
              Get Started
            </Link>
          </template>
        </div>

        <!-- Mobile Menu Button -->
        <button
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors"
          aria-label="Toggle menu"
        >
          <Menu v-if="!mobileMenuOpen" class="h-6 w-6 text-gray-700" />
          <X v-else class="h-6 w-6 text-gray-700" />
        </button>
      </div>

      <!-- Mobile Navigation -->
      <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200 py-4 bg-white">
        <nav class="flex flex-col gap-1">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-blue-600 rounded-lg transition-colors"
            @click="mobileMenuOpen = false"
          >
            {{ link.label }}
          </Link>
          
          <div class="flex flex-col gap-2 pt-3 border-t border-gray-200 mt-2">
            <!-- Apply as Operator (visible only for guests in mobile) -->
            <Link 
              v-if="showApplyAsOperator"
              href="/register?type=operator" 
              class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-2"
              @click="mobileMenuOpen = false"
            >
              <Briefcase class="h-4 w-4" />
              <span>Apply as Operator</span>
            </Link>

            <template v-if="user">
              <!-- User Info -->
              <div class="px-4 py-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                  <Avatar class="h-10 w-10">
                    <AvatarImage 
                      v-if="user?.avatar"
                      :src="user.avatar" 
                      :alt="user.name" 
                    />
                    <AvatarFallback class="bg-blue-600 text-white font-semibold">
                      {{ userInitials }}
                    </AvatarFallback>
                  </Avatar>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ user.name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                  </div>
                </div>
              </div>

              <!-- Profile Link -->
              <Link 
                :href="isClient ? '/client/profile' : '/operator/profile'" 
                class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-blue-600 rounded-lg transition-colors flex items-center gap-2"
                @click="mobileMenuOpen = false"
              >
                <User class="h-4 w-4" />
                <span>My Profile</span>
              </Link>

              <!-- Dashboard/Bookings Link -->
              <Link 
                :href="dashboardLink" 
                class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-blue-600 rounded-lg transition-colors flex items-center gap-2"
                @click="mobileMenuOpen = false"
              >
                <Calendar class="h-4 w-4" />
                <span>{{ dashboardLabel }}</span>
              </Link>

              <button
                @click="handleLogout"
                class="px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2 text-left w-full"
              >
                <LogOut class="h-4 w-4" />
                <span>Log Out</span>
              </button>
            </template>
            
            <template v-else>
              <!-- Guest users mobile menu -->
              <Link 
                :href="login()" 
                class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                @click="mobileMenuOpen = false"
              >
                Sign In
              </Link>
              <Link 
                v-if="canRegister" 
                :href="register()" 
                class="px-4 py-2.5 text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 rounded-lg transition-colors text-center shadow-sm"
                @click="mobileMenuOpen = false"
              >
                Get Started
              </Link>
            </template>
          </div>
        </nav>
      </div>
    </div>
  </header>
</template>