<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import vehicleRoutes from '@/routes/operator/vehicles';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Vehicle {
    id: number;
    license_plate: string;
    chasis_number: string;
    brand: string;
    model: string;
    year: number;
    is_active: boolean;
    coding_day: string | null;
    operator_location?: {
        id: number;
        name: string;
    };
    created_at: string;
}

interface PaginatedVehicles {
    data: Vehicle[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

const props = defineProps<{
    vehicles: PaginatedVehicles;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard.url(),
    },
    {
        title: 'My Vehicles',
        href: vehicleRoutes.list.url(),
    },
];

const activeVehiclesCount = computed(() => 
    props.vehicles.data.filter(v => v.is_active).length
);

const inactiveVehiclesCount = computed(() => 
    props.vehicles.data.filter(v => !v.is_active).length
);

const deleteVehicle = (id: number) => {
    if (confirm('Are you sure you want to delete this vehicle?')) {
        router.delete(vehicleRoutes.destroy.url(id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="My Vehicles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Stats Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Total Vehicles -->
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">
                                Total Vehicles
                            </p>
                            <h3 class="mt-2 text-3xl font-bold">
                                {{ vehicles.total }}
                            </h3>
                        </div>
                        <div class="text-xs text-muted-foreground">
                            All registered vehicles
                        </div>
                    </div>
                </div>

                <!-- Active Vehicles -->
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">
                                Active Vehicles
                            </p>
                            <h3 class="mt-2 text-3xl font-bold text-green-600 dark:text-green-500">
                                {{ activeVehiclesCount }}
                            </h3>
                        </div>
                        <div class="text-xs text-muted-foreground">
                            Currently operational
                        </div>
                    </div>
                </div>

                <!-- Inactive Vehicles -->
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">
                                Inactive Vehicles
                            </p>
                            <h3 class="mt-2 text-3xl font-bold text-orange-600 dark:text-orange-500">
                                {{ inactiveVehiclesCount }}
                            </h3>
                        </div>
                        <div class="text-xs text-muted-foreground">
                            Not in service
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicles Table -->
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 bg-white md:min-h-min dark:border-sidebar-border dark:bg-sidebar">
                <div class="p-6">
                    <!-- Header -->
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold">My Vehicles</h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Manage your registered vehicles
                            </p>
                        </div>
                        <Link
                            :href="vehicleRoutes.create.url()"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Add Vehicle
                        </Link>
                    </div>

                    <!-- Table -->
                    <div v-if="vehicles.data.length > 0" class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-sidebar-border/70 text-left">
                                    <th class="pb-3 text-sm font-semibold text-muted-foreground">
                                        License Plate
                                    </th>
                                    <th class="pb-3 text-sm font-semibold text-muted-foreground">
                                        Brand & Model
                                    </th>
                                    <th class="pb-3 text-sm font-semibold text-muted-foreground">
                                        Year
                                    </th>
                                    <th class="pb-3 text-sm font-semibold text-muted-foreground">
                                        Coding Day
                                    </th>
                                    <th class="pb-3 text-sm font-semibold text-muted-foreground">
                                        Status
                                    </th>
                                    <th class="pb-3 text-sm font-semibold text-muted-foreground">
                                        Location
                                    </th>
                                    <th class="pb-3 text-right text-sm font-semibold text-muted-foreground">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="vehicle in vehicles.data"
                                    :key="vehicle.id"
                                    class="border-b border-sidebar-border/50 transition-colors hover:bg-sidebar-accent/50"
                                >
                                    <td class="py-4">
                                        <div class="font-mono text-sm font-semibold">
                                            {{ vehicle.license_plate }}
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ vehicle.chasis_number }}
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <div class="text-sm font-medium">
                                            {{ vehicle.brand }}
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ vehicle.model }}
                                        </div>
                                    </td>
                                    <td class="py-4 text-sm">
                                        {{ vehicle.year }}
                                    </td>
                                    <td class="py-4">
                                        <span
                                            v-if="vehicle.coding_day"
                                            class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                                        >
                                            {{ vehicle.coding_day }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-xs text-muted-foreground"
                                        >
                                            N/A
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <span
                                            v-if="vehicle.is_active"
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400"
                                        >
                                            Active
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-800 dark:bg-orange-900/30 dark:text-orange-400"
                                        >
                                            Inactive
                                        </span>
                                    </td>
                                    <td class="py-4 text-sm">
                                        <span v-if="vehicle.operator_location">
                                            {{ vehicle.operator_location.name }}
                                        </span>
                                        <span v-else class="text-muted-foreground">
                                            Not assigned
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="vehicleRoutes.show.url(vehicle.id)"
                                                class="inline-flex items-center rounded-md p-2 text-sm transition-colors hover:bg-sidebar-accent"
                                                title="View"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            </Link>
                                            <Link
                                                :href="vehicleRoutes.edit.url(vehicle.id)"
                                                class="inline-flex items-center rounded-md p-2 text-sm transition-colors hover:bg-sidebar-accent"
                                                title="Edit"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                </svg>
                                            </Link>
                                            <button
                                                @click="deleteVehicle(vehicle.id)"
                                                class="inline-flex items-center rounded-md p-2 text-sm text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                                title="Delete"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div
                            v-if="vehicles.last_page > 1"
                            class="mt-6 flex items-center justify-between"
                        >
                            <div class="text-sm text-muted-foreground">
                                Showing {{ vehicles.data.length }} of {{ vehicles.total }} vehicles
                            </div>
                            <div class="flex items-center gap-2">
                                <Link
                                    v-if="vehicles.current_page > 1"
                                    :href="vehicleRoutes.list.url({ query: { page: vehicles.current_page - 1 } })"
                                    class="rounded-md border border-sidebar-border px-3 py-1.5 text-sm transition-colors hover:bg-sidebar-accent"
                                >
                                    Previous
                                </Link>
                                <span class="text-sm text-muted-foreground">
                                    Page {{ vehicles.current_page }} of {{ vehicles.last_page }}
                                </span>
                                <Link
                                    v-if="vehicles.current_page < vehicles.last_page"
                                    :href="vehicleRoutes.list.url({ query: { page: vehicles.current_page + 1 } })"
                                    class="rounded-md border border-sidebar-border px-3 py-1.5 text-sm transition-colors hover:bg-sidebar-accent"
                                >
                                    Next
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center py-12 text-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mb-4 h-16 w-16 text-muted-foreground/40"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2" />
                            <circle cx="7" cy="17" r="2" />
                            <path d="M9 17h6" />
                            <circle cx="17" cy="17" r="2" />
                        </svg>
                        <h3 class="mb-2 text-lg font-semibold">No vehicles yet</h3>
                        <p class="mb-4 text-sm text-muted-foreground">
                            Get started by adding your first vehicle
                        </p>
                        <Link
                            :href="vehicleRoutes.create.url()"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Add Your First Vehicle
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>