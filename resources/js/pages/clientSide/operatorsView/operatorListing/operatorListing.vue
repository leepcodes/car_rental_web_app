<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/operator/dashboard',
    },
    {
        title: 'Listings',
        href: '/operator/listings',
    },
];

const handleAddListing = () => {
    // This will be implemented later to open a form or navigate to add listing page
    console.log('Add new listing');
};
</script>

<template>
    <Head title="My Listings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header with Add Listing Button -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">My Listings</h1>
                    <p class="text-muted-foreground">
                        Manage your vehicle listings
                    </p>
                </div>
                <Button @click="handleAddListing" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Add Listing
                </Button>
            </div>

            <!-- Listings Content Area -->
            <div
                class="relative min-h-[60vh] flex-1 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border"
            >
                <div class="flex h-full items-center justify-center">
                    <div class="text-center">
                        <div class="mb-4 flex justify-center">
                            <div
                                class="rounded-full bg-muted p-4"
                            >
                                <Plus class="h-8 w-8 text-muted-foreground" />
                            </div>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold">No listings yet</h3>
                        <p class="mb-4 text-sm text-muted-foreground">
                            Get started by creating your first vehicle listing
                        </p>
                        <Button @click="handleAddListing" variant="outline" class="gap-2">
                            <Plus class="h-4 w-4" />
                            Create Your First Listing
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
