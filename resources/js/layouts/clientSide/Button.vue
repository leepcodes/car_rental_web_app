<script setup>

import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        default: null,
    },
    operator: {
        type: Boolean,
        default: false,
    }

});

const classes = [
    'px-4 py-2 rounded border transition',
    props.operator
        ? 'bg-red-600 text-white hover:bg-red-700'
        : 'bg-white text-black border-gray-300 hover:bg-gray-100'
];

</script>

<template>

    <!-- If href exists → render Link -->
    <Link v-if="href" :href="href" :class="classes">
        <slot />
    </Link>

    <!-- Otherwise render normal button -->
    <button v-else :class="classes">
        <slot />
    </button>

</template>