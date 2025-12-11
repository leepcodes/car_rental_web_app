<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const isProcessing = ref(false);
</script>

<template>
    <Head title="Register" />

    <section class="relative min-h-screen flex items-center justify-center px-6 lg:px-8 overflow-hidden bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 py-12">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:60px_60px]"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-3xl"></div>

        <!-- Animated Car -->
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
            <!-- Register Card -->
            <div class="relative bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 md:p-10">
                <!-- Decorative Glow -->
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-500/30 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-cyan-500/30 rounded-full blur-2xl"></div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 style="font-family: 'Roboto', sans-serif" class="text-3xl md:text-4xl font-bold text-white mb-2">
                        Create an Account
                    </h1>
                    <p style="font-family: 'Roboto', sans-serif" class="text-neutral-300 text-sm md:text-base">
                        Enter your details below to create your account
                    </p>
                </div>

                <!-- Register Form -->
                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password', 'password_confirmation']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6"
                    @processing="isProcessing = isProcessing"
                >
                    <div class="grid gap-6">
                        <!-- User Type Select -->
                        <div class="grid gap-2">
                            <Label for="user_type" class="text-white text-sm font-medium" style="font-family: 'Roboto', sans-serif">
                                User Type
                            </Label>
                            <Select name="user_type" default-value="client">
                                <SelectTrigger 
                                    id="user_type" 
                                    :tabindex="1" 
                                    class="bg-white/10 border-white/20 text-white focus:border-blue-400 focus:ring-blue-400/20 backdrop-blur-sm h-11"
                                    style="font-family: 'Roboto', sans-serif"
                                >
                                    <SelectValue placeholder="Select user type" />
                                </SelectTrigger>
                                <SelectContent 
                                    class="bg-neutral-800 border-white/20 backdrop-blur-xl"
                                    style="font-family: 'Roboto', sans-serif"
                                >
                                    <SelectItem value="client" class="text-white hover:bg-white/10 focus:bg-white/10">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Client
                                        </div>
                                    </SelectItem>
                                    <SelectItem value="operator" class="text-white hover:bg-white/10 focus:bg-white/10">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            Operator
                                        </div>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="errors.user_type" class="text-red-400" />
                        </div>

                        <!-- Name Field -->
                        <div class="grid gap-2">
                            <Label for="name" class="text-white text-sm font-medium" style="font-family: 'Roboto', sans-serif">
                                Full Name
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                name="name"
                                required
                                autofocus
                                :tabindex="2"
                                autocomplete="name"
                                placeholder="John Doe"
                                class="bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-blue-400 focus:ring-blue-400/20 backdrop-blur-sm h-11"
                                style="font-family: 'Roboto', sans-serif"
                            />
                            <InputError :message="errors.name" class="text-red-400" />
                        </div>

                        <!-- Email Field -->
                        <div class="grid gap-2">
                            <Label for="email" class="text-white text-sm font-medium" style="font-family: 'Roboto', sans-serif">
                                Email Address
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                :tabindex="3"
                                autocomplete="email"
                                placeholder="email@example.com"
                                class="bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-blue-400 focus:ring-blue-400/20 backdrop-blur-sm h-11"
                                style="font-family: 'Roboto', sans-serif"
                            />
                            <InputError :message="errors.email" class="text-red-400" />
                        </div>

                        <!-- Password Field -->
                        <div class="grid gap-2">
                            <Label for="password" class="text-white text-sm font-medium" style="font-family: 'Roboto', sans-serif">
                                Password
                            </Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                :tabindex="4"
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-blue-400 focus:ring-blue-400/20 backdrop-blur-sm h-11"
                                style="font-family: 'Roboto', sans-serif"
                            />
                            <InputError :message="errors.password" class="text-red-400" />
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="grid gap-2">
                            <Label for="password_confirmation" class="text-white text-sm font-medium" style="font-family: 'Roboto', sans-serif">
                                Confirm Password
                            </Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                :tabindex="5"
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-blue-400 focus:ring-blue-400/20 backdrop-blur-sm h-11"
                                style="font-family: 'Roboto', sans-serif"
                            />
                            <InputError :message="errors.password_confirmation" class="text-red-400" />
                        </div>

                        <!-- Submit Button - Custom HTML Button -->
                        <button
                            type="submit"
                            :tabindex="6"
                            :disabled="processing"
                            data-test="register-user-button"
                            class="mt-4 w-full h-12 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            style="font-family: 'Roboto', sans-serif"
                        >
                            <svg v-if="processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ processing ? 'Creating Account...' : 'Create Account' }}
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div
                        class="text-center text-sm text-neutral-300"
                        style="font-family: 'Roboto', sans-serif"
                    >
                        Already have an account?
                        <TextLink 
                            :href="login()" 
                            :tabindex="7"
                            class="text-blue-400 hover:text-blue-300 font-semibold transition-colors ml-1"
                            style="font-family: 'Roboto', sans-serif"
                        >
                            Log in
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
                    <span>Free Registration</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Secure</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Instant Access</span>
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

/* Car Animation */
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
