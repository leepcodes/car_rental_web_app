<!-- AppHeader.vue Component -->
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
import { User, Calendar, LogOut, ChevronDown, Briefcase } from 'lucide-vue-next';

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

// Navigation links - filtered based on user type
const navLinks = computed(() => {
  const baseLinks = [
    { href: '/booking', label: 'Book' },
    { href: '/about', label: 'About' },
    { href: '/contact', label: 'Contact' },
  ];

  // If user is an operator, remove the "Book" link
  if (isOperator.value) {
    return baseLinks.filter(link => link.href !== '/booking');
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
          <!-- Apply as Operator button (visible only for guests) -->
          <template v-if="showApplyAsOperator">
            <Link 
              href="/register?type=operator" 
              class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-neutral-900 border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors"
            >
              <Briefcase class="h-4 w-4" />
              <span>Apply as Operator</span>
            </Link>
          </template>

          <template v-if="user">
            <!-- User Dropdown -->
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <button class="inline-flex items-center gap-2 px-3 py-2 hover:bg-neutral-100 rounded-lg transition-colors">
                  <Avatar class="h-8 w-8">
                    <AvatarImage :src="user.avatar" :alt="user.name" />
                    <AvatarFallback>{{ userInitials }}</AvatarFallback>
                  </Avatar>
                  <span class="text-sm font-medium">{{ user.name }}</span>
                  <ChevronDown class="h-4 w-4 text-neutral-500" />
                </button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" class="w-56">
                <DropdownMenuLabel>
                  <div class="flex flex-col space-y-1">
                    <p class="text-sm font-medium leading-none">{{ user.name }}</p>
                    <p class="text-xs leading-none text-muted-foreground">
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
                <DropdownMenuItem @click="handleLogout" class="cursor-pointer text-destructive focus:text-destructive">
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
            <!-- Apply as Operator (visible only for guests in mobile) -->
            <Link 
              v-if="showApplyAsOperator"
              href="/register?type=operator" 
              class="px-4 py-2.5 text-sm text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors flex items-center gap-2"
            >
              <Briefcase class="h-4 w-4" />
              <span>Apply as Operator</span>
            </Link>

            <template v-if="user">
              <!-- User Info -->
              <div class="px-4 py-2 border-b border-neutral-200">
                <div class="flex items-center gap-3">
                  <Avatar class="h-10 w-10">
                    <AvatarImage :src="user.avatar" :alt="user.name" />
                    <AvatarFallback>{{ userInitials }}</AvatarFallback>
                  </Avatar>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ user.name }}</p>
                    <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
                  </div>
                </div>
              </div>

              <!-- Profile Link -->
              <Link 
                :href="isClient ? '/client/profile' : '/operator/profile'" 
                class="px-4 py-2.5 text-sm text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors flex items-center gap-2"
              >
                <User class="h-4 w-4" />
                <span>My Profile</span>
              </Link>

              <!-- Dashboard/Bookings Link -->
              <Link 
                :href="dashboardLink" 
                class="px-4 py-2.5 text-sm text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors flex items-center gap-2"
              >
                <Calendar class="h-4 w-4" />
                <span>{{ dashboardLabel }}</span>
              </Link>

              <button
                @click="handleLogout"
                class="px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2 text-left w-full"
              >
                <LogOut class="h-4 w-4" />
                <span>Log Out</span>
              </button>
            </template>
            
            <template v-else>
              <!-- Guest users mobile menu -->
              <Link 
                :href="login()" 
                class="px-4 py-2.5 text-sm text-neutral-900 hover:bg-neutral-100 rounded-lg transition-colors"
              >
                Sign In
              </Link>
              <Link 
                v-if="canRegister" 
                :href="register()" 
                class="px-4 py-2.5 text-sm bg-neutral-800 text-white hover:bg-neutral-700 rounded-lg transition-colors text-center"
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