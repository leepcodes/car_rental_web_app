<script setup>

import { Link } from "@inertiajs/vue3";
import Button from "@/layouts/clientSide/Button.vue";

</script>

<template>
    <section class="bg-gray-50">
        <div class="max-w-screen-xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-10 items-center">

            <!-- Left Content -->
            <div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                    Rent Your Dream Car <br />
                    With <span class="text-blue-600">Ease & Confidence</span>
                </h1>

                <p class="mt-4 text-gray-600 text-lg">
                    Quality vehicles, affordable prices, and a seamless booking experience.
                    Start your journey today.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <Button operator>Book Now</Button>

                    <Button>
                        Explore Cars
                    </Button>
                </div>
            </div>

            <!-- Right Image -->
            <div class="flex justify-center">
                <img
                    src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=900"
                    alt="Car Rental"
                    class="rounded-xl shadow-lg w-full md:w-[85%] object-cover"
                />
            </div>

        </div>
    </section>
</template>
