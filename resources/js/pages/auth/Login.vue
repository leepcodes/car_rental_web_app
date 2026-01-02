<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const showPassword = ref(false);
</script>

<template>
    <Head title="Log in" />

    <section class="relative min-h-screen flex items-center justify-center px-6 lg:px-8 overflow-hidden bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:60px_60px]"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-3xl"></div>

        <!-- Animated Car (Flipped to go left) -->
        <div class="absolute bottom-20 left-0 w-full pointer-events-none overflow-hidden">
            <div class="car-animation">
                <img 
                    src="/images/Innova.png" 
                    alt="Animated Car" 
                    class="w-40 md:w-48 h-auto object-contain opacity-30 drop-shadow-2xl"
                />
            </div>
        </div>

        <div class="relative w-full max-w-md z-10">
            <!-- Login Card -->
            <div class="relative bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 md:p-10">
                <!-- Decorative Glow -->
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-500/30 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-cyan-500/30 rounded-full blur-2xl"></div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 style="font-family: 'Roboto', sans-serif" class="text-3xl md:text-4xl font-bold text-white mb-2">
                        Welcome Back
                    </h1>
                    <p style="font-family: 'Roboto', sans-serif" class="text-neutral-300 text-sm md:text-base">
                        Enter your credentials to access your account
                    </p>
                </div>

                <!-- Status Message -->
                <div
                    v-if="status"
                    class="mb-6 text-center text-sm font-medium text-green-400 bg-green-500/10 border border-green-500/20 rounded-lg p-3"
                    style="font-family: 'Roboto', sans-serif"
                >
                    {{ status }}
                </div>

                <!-- Login Form -->
                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-6">
                        <!-- General Error Alert -->
                        <div
                            v-if="errors.email || errors.password"
                            class="flex items-center gap-3 text-sm font-medium text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg p-3"
                            style="font-family: 'Roboto', sans-serif"
                        >
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Your credentials for login are incorrect. Please try again.</span>
                        </div>

                        <!-- Email Field -->
                        <div class="grid gap-2">
                            <Label for="email" class="text-white text-sm font-medium" style="font-family: 'Roboto', sans-serif">
                                Email address
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="email@example.com"
                                class="bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-blue-400 focus:ring-blue-400/20 backdrop-blur-sm h-11"
                                style="font-family: 'Roboto', sans-serif"
                            />
                        </div>

                        <!-- Password Field -->
                        <div class="grid gap-2">
                            <div class="flex items-center justify-between">
                                <Label for="password" class="text-white text-sm font-medium" style="font-family: 'Roboto', sans-serif">
                                    Password
                                </Label>
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="request()"
                                    class="text-sm text-blue-400 hover:text-blue-300 transition-colors"
                                    :tabindex="5"
                                    style="font-family: 'Roboto', sans-serif"
                                >
                                    Forgot password?
                                </TextLink>
                            </div>
                            <div class="relative">
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-blue-400 focus:ring-blue-400/20 backdrop-blur-sm h-11 pr-10"
                                    style="font-family: 'Roboto', sans-serif"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-300 hover:text-white transition-colors"
                                    :tabindex="-1"
                                >
                                    <Eye v-if="!showPassword" :size="20" />
                                    <EyeOff v-else :size="20" />
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button - Custom HTML Button -->
                        <button
                            type="submit"
                            :tabindex="4"
                            :disabled="processing"
                            data-test="login-button"
                            class="mt-4 w-full h-12 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            style="font-family: 'Roboto', sans-serif"
                        >
                            <svg v-if="processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ processing ? 'Logging in...' : 'Log in' }}
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div
                        class="text-center text-sm text-neutral-300"
                        v-if="canRegister"
                        style="font-family: 'Roboto', sans-serif"
                    >
                        Don't have an account?
                        <TextLink 
                            :href="register()" 
                            :tabindex="5"
                            class="text-blue-400 hover:text-blue-300 font-semibold transition-colors ml-1"
                            style="font-family: 'Roboto', sans-serif"
                        >
                            Sign up
                        </TextLink>
                    </div>
                </Form>
            </div>

            <!-- Bottom Features -->
            <div class="mt-8 flex justify-center gap-8 text-white/80 text-xs" style="font-family: 'Roboto', sans-serif">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Secure Login</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Encrypted</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Protected</span>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.bg-grid-white\/\[0\.02\] {
  background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
}

/* Hide browser's default password reveal icon */
input[type="password"]::-ms-reveal,
input[type="password"]::-ms-clear,
input[type="text"]::-ms-reveal,
input[type="text"]::-ms-clear {
  display: none;
}

input[type="password"]::-webkit-credentials-auto-fill-button,
input[type="password"]::-webkit-contacts-auto-fill-button {
  visibility: hidden;
  pointer-events: none;
  position: absolute;
  right: 0;
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

/* Car Animation - Flipped to go LEFT */
@keyframes drive-left {
  0% {
    transform: translateX(100vw) scaleX(-1);
    opacity: 0;
  }
  5% {
    opacity: 0.3;
  }
  95% {
    opacity: 0.3;
  }
  100% {
    transform: translateX(-200px) scaleX(-1);
    opacity: 0;
  }
}

.car-animation {
  animation: drive-left 20s linear infinite;
  will-change: transform;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
