<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import { SidebarProvider, SidebarInset, SidebarTrigger } from '@/components/ui/sidebar';
</script>

<template>
  <SidebarProvider>
    <AppSidebar />
    <SidebarInset>
      <header class="flex h-16 shrink-0 items-center gap-2 border-b px-4">
        <SidebarTrigger class="-ml-1" />
        <div class="flex items-center gap-2">
          <h1 class="text-xl font-semibold">SuperAdmin Module</h1>
        </div>
      </header>
      
      <main class="flex-1 p-6">
        <p class="text-gray-600"> content goes here...</p>
      </main>
    </SidebarInset>
  </SidebarProvider>
</template>