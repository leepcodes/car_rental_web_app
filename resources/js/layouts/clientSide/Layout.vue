<script setup>
import Button from '@/layouts/clientSide/Button.vue';
import { Link } from "@inertiajs/vue3";
import { ref } from 'vue';

const isOpen = ref(false);

</script>

<template>
    <div class="mx-auto max-w-screen-xl">
        <header class="text-black">
            <nav class="relative flex items-center justify-between p-4">
                
                <!-- Left side - Logo -->
                <div class="flex-shrink-0">
                    <Link href="#" class="text-lg font-semibold">Car Rental</Link>
                </div>

                <!-- Hamburger / Close Button -->
                <button
                    class="md:hidden text-gray-700 focus:outline-none"
                    @click="isOpen = !isOpen"
                >
                    <transition name="icon-fade" mode="out-in">
                        <!-- Hamburger Icon -->
                        <svg
                            v-if="!isOpen"
                            key="menu"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transform transition-all duration-200"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>

                        <!-- X Icon -->
                        <svg
                            v-else
                            key="close"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 transform transition-all duration-200 rotate-90"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </transition>
                </button>

                <!--center - Menu -->
                <div class="hidden md:absolute md:inset-y-0 md:left-1/2 md:transform md:-translate-x-1/2 md:flex md:items-center space-x-6">
                    <Link href="#" class="hover:underline text-sm">Book Now</Link>
                    <Link href="#" class="hover:underline text-sm">About Us</Link>
                    <Link href="#" class="hover:underline text-sm">Contact Us</Link>
                </div>

                <div class="hidden md:flex space-x-2 text-sm">
                    <Button operator>Apply as Operator</Button>
                    <Button href="">Sign In Or Register</Button>
                </div>


            </nav>

            <!-- Mobile Dropdown Menu -->
            <div 
                v-if="isOpen" 
                class="md:hidden px-4 pb-4 space-y-3 bg-white shadow"
            >
                <Link href="#" class="block text-sm hover:underline">Book Now</Link>
                <Link href="#" class="block text-sm hover:underline">About Us</Link>
                <Link href="#" class="block text-sm hover:underline">Contact Us</Link>

                <div class="flex flex-col gap-2 pt-2">
                    <Button operator>Apply as Operator</Button>
                    <Button href="">Sign In Or Register</Button>
                </div>
            </div>
        </header>

        <main class="p-4">
            <slot />
        </main>
    </div>
</template>
