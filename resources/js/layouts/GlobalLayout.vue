<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <div class="app-wrapper">
    <slot />
  </div>
</template>

<style>
/* Global Roboto Font Application */
.app-wrapper {
  font-family: 'Roboto', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.app-wrapper *:not(code):not(pre) {
  font-family: inherit;
}

/* Keep monospace fonts for code elements */
code,
pre,
kbd,
samp {
  font-family: 'Courier New', Consolas, Monaco, monospace !important;
}
</style>