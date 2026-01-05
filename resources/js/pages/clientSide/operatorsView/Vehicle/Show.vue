<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import vehicleRoutes from '@/routes/operator/vehicles';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

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
    operator?: {
        id: number;
        name: string;
        email: string;
    };
    created_at: string;
    updated_at: string;
}

interface Attachment {
    id: number;
    vehicle_id: number;
    attachment_type: string;
    attachment_url: string;
    created_at: string;
}

const props = defineProps<{
    vehicles: Vehicle;
    attachments: Attachment[];
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
    {
        title: props.vehicles.license_plate,
        href: vehicleRoutes.show.url(props.vehicles.id),
    },
];

const getAttachmentTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        or: 'Official Receipt (OR)',
        cr: 'Certificate of Registration (CR)',
        receipt: 'Receipt',
        inspection: 'Inspection Report',
        insurance: 'Insurance',
        other: 'Other',
    };
    return labels[type] || type;
};

const deleteVehicle = () => {
    if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
        router.delete(vehicleRoutes.destroy.url(props.vehicles.id));
    }
};
</script>

<template>
    <Head :title="`Vehicle: ${vehicles.license_plate}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">{{ vehicles.license_plate }}</h1>
                        <p class="mt-2 text-lg text-muted-foreground">
                            {{ vehicles.brand }} {{ vehicles.model }} ({{ vehicles.year }})
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link
                            :href="vehicleRoutes.edit.url(vehicles.id)"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </Link>
                        <button
                            @click="deleteVehicle"
                            class="inline-flex items-center gap-2 rounded-lg border border-red-600 px-4 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Vehicle Details -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Basic Information -->
                <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                    <h2 class="mb-4 text-lg font-semibold">Basic Information</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">License Plate</dt>
                            <dd class="mt-1 text-sm font-mono font-semibold">{{ vehicles.license_plate }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Chasis Number</dt>
                            <dd class="mt-1 text-sm font-mono">{{ vehicles.chasis_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Brand</dt>
                            <dd class="mt-1 text-sm">{{ vehicles.brand }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Model</dt>
                            <dd class="mt-1 text-sm">{{ vehicles.model }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Year</dt>
                            <dd class="mt-1 text-sm">{{ vehicles.year }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Operational Details -->
                <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                    <h2 class="mb-4 text-lg font-semibold">Operational Details</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd class="mt-1">
                                <span
                                    v-if="vehicles.is_active"
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
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Coding Day</dt>
                            <dd class="mt-1">
                                <span
                                    v-if="vehicles.coding_day"
                                    class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                                >
                                    {{ vehicles.coding_day }}
                                </span>
                                <span v-else class="text-sm text-muted-foreground">Not set</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Location</dt>
                            <dd class="mt-1 text-sm">
                                {{ vehicles.operator_location?.name || 'Not assigned' }}
                            </dd>
                        </div>
                        <div v-if="vehicles.operator">
                            <dt class="text-sm font-medium text-muted-foreground">Operator</dt>
                            <dd class="mt-1 text-sm">
                                <div>{{ vehicles.operator.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ vehicles.operator.email }}</div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Documents -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                <h2 class="mb-4 text-lg font-semibold">Documents</h2>
                
                <div v-if="attachments.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <a
                        v-for="attachment in attachments"
                        :key="attachment.id"
                        :href="`/storage/${attachment.attachment_url}`"
                        target="_blank"
                        class="group relative overflow-hidden rounded-lg border border-sidebar-border p-4 transition-all hover:border-primary hover:shadow-md"
                    >
                        <!-- Preview -->
                        <div class="mb-3 flex items-center justify-center h-32 bg-gray-100 rounded dark:bg-gray-800">
                            <img
                                v-if="attachment.attachment_url.match(/\.(jpg|jpeg|png)$/i)"
                                :src="`/storage/${attachment.attachment_url}`"
                                :alt="getAttachmentTypeLabel(attachment.attachment_type)"
                                class="h-full w-full object-cover rounded"
                            />
                            <svg
                                v-else
                                class="h-12 w-12 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <!-- Info -->
                        <div>
                            <p class="text-sm font-medium">
                                {{ getAttachmentTypeLabel(attachment.attachment_type) }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ new Date(attachment.created_at).toLocaleDateString() }}
                            </p>
                        </div>

                        <!-- Hover overlay -->
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </div>
                    </a>
                </div>

                <div v-else class="flex flex-col items-center justify-center py-8 text-center">
                    <svg class="mb-3 h-12 w-12 text-muted-foreground/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm text-muted-foreground">No documents uploaded</p>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                <h2 class="mb-4 text-lg font-semibold">History</h2>
                <dl class="grid gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">Created</dt>
                        <dd class="mt-1 text-sm">
                            {{ new Date(vehicles.created_at).toLocaleString() }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">Last Updated</dt>
                        <dd class="mt-1 text-sm">
                            {{ new Date(vehicles.updated_at).toLocaleString() }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </AppLayout>
</template>