<!-- AppFooter.vue Component -->
<script setup lang="ts">
const paymentMethods = ['GCash', 'Visa', 'Mastercard'];
</script>

<template>
  <footer class="py-16 px-6 lg:px-8 bg-neutral-50 border-t border-neutral-200">
    <div class="mx-auto max-w-7xl">
      <div class="flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-neutral-800 rounded-lg"></div>
          <span class="text-base font-bold text-neutral-800">CRR Rentals</span>
        </div>
        <p class="text-sm text-neutral-500">© 2025 CRR Rentals. All rights reserved.</p>
        <div class="flex gap-8 text-sm font-medium text-neutral-600">
          <span v-for="method in paymentMethods" :key="method">{{ method }}</span>
        </div>
      </div>
    </div>
  </footer>
</template>