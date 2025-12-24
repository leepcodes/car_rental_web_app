<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import vehicleRoutes from '@/routes/operator/vehicles';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface OperatorLocation {
    id: number;
    name: string;
}

const props = defineProps<{
    operatorLocations?: OperatorLocation[];
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
        title: 'Add Vehicle',
        href: vehicleRoutes.create.url(),
    },
];

const form = useForm({
    license_plate: '',
    chasis_number: '',
    brand: '',
    model: '',
    year: new Date().getFullYear(),
    is_active: true,
    operator_locations: null as number | null,
    coding_day: '',
    attachments: [] as File[],
    attachment_types: [] as string[],
});

// Separate refs for each document type
const orFile = ref<File | null>(null);
const orPreview = ref<string>('');
const crFile = ref<File | null>(null);
const crPreview = ref<string>('');
const insuranceFile = ref<File | null>(null);
const insurancePreview = ref<string>('');
const receiptFile = ref<File | null>(null);
const receiptPreview = ref<string>('');
const inspectionFile = ref<File | null>(null);
const inspectionPreview = ref<string>('');

const codingDays = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
    'Sunday',
];

const handleFileChange = (event: Event, type: 'or' | 'cr' | 'insurance' | 'receipt' | 'inspection') => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const preview = e.target?.result as string;
            
            switch (type) {
                case 'or':
                    orFile.value = file;
                    orPreview.value = preview;
                    break;
                case 'cr':
                    crFile.value = file;
                    crPreview.value = preview;
                    break;
                case 'insurance':
                    insuranceFile.value = file;
                    insurancePreview.value = preview;
                    break;
                case 'receipt':
                    receiptFile.value = file;
                    receiptPreview.value = preview;
                    break;
                case 'inspection':
                    inspectionFile.value = file;
                    inspectionPreview.value = preview;
                    break;
            }
        };
        
        reader.readAsDataURL(file);
    }
};

const removeFile = (type: 'or' | 'cr' | 'insurance' | 'receipt' | 'inspection') => {
    switch (type) {
        case 'or':
            orFile.value = null;
            orPreview.value = '';
            break;
        case 'cr':
            crFile.value = null;
            crPreview.value = '';
            break;
        case 'insurance':
            insuranceFile.value = null;
            insurancePreview.value = '';
            break;
        case 'receipt':
            receiptFile.value = null;
            receiptPreview.value = '';
            break;
        case 'inspection':
            inspectionFile.value = null;
            inspectionPreview.value = '';
            break;
    }
};

const submit = () => {
    // Build attachments array with their types
    form.attachments = [];
    form.attachment_types = [];
    
    if (orFile.value) {
        form.attachments.push(orFile.value);
        form.attachment_types.push('or');
    }
    if (crFile.value) {
        form.attachments.push(crFile.value);
        form.attachment_types.push('cr');
    }
    if (insuranceFile.value) {
        form.attachments.push(insuranceFile.value);
        form.attachment_types.push('insurance');
    }
    if (receiptFile.value) {
        form.attachments.push(receiptFile.value);
        form.attachment_types.push('receipt');
    }
    if (inspectionFile.value) {
        form.attachments.push(inspectionFile.value);
        form.attachment_types.push('inspection');
    }
    
    form.post(vehicleRoutes.store.url(), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Add Vehicle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">Add New Vehicle</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Fill in the details to register a new vehicle
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Basic Information</h3>
                        
                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- License Plate -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    License Plate <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.license_plate"
                                    type="text"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    placeholder="ABC-1234"
                                    required
                                />
                                <p v-if="form.errors.license_plate" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.license_plate }}
                                </p>
                            </div>

                            <!-- Chasis Number -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Chasis Number <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.chasis_number"
                                    type="text"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    placeholder="CHASIS123456"
                                    required
                                />
                                <p v-if="form.errors.chasis_number" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.chasis_number }}
                                </p>
                            </div>

                            <!-- Brand -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Brand <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.brand"
                                    type="text"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    placeholder="Toyota"
                                    required
                                />
                                <p v-if="form.errors.brand" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.brand }}
                                </p>
                            </div>

                            <!-- Model -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Model <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.model"
                                    type="text"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    placeholder="Vios"
                                    required
                                />
                                <p v-if="form.errors.model" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.model }}
                                </p>
                            </div>

                            <!-- Year -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Year <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model.number="form.year"
                                    type="number"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    :min="1900"
                                    :max="new Date().getFullYear() + 1"
                                    required
                                />
                                <p v-if="form.errors.year" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.year }}
                                </p>
                            </div>

                            <!-- Coding Day -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Coding Day
                                </label>
                                <select
                                    v-model="form.coding_day"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                >
                                    <option value="">Select coding day</option>
                                    <option v-for="day in codingDays" :key="day" :value="day">
                                        {{ day }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Location -->
                        <div v-if="operatorLocations && operatorLocations.length > 0">
                            <label class="block text-sm font-medium mb-2">
                                Operator Location
                            </label>
                            <select
                                v-model="form.operator_locations"
                                class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                            >
                                <option :value="null">Select location</option>
                                <option v-for="location in operatorLocations" :key="location.id" :value="location.id">
                                    {{ location.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center gap-2">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                id="is_active"
                                class="rounded border-sidebar-border"
                            />
                            <label for="is_active" class="text-sm font-medium">
                                Vehicle is active
                            </label>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Vehicle Documents</h3>
                        <p class="text-sm text-muted-foreground">
                            Upload required documents for vehicle registration
                        </p>
                        
                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Official Receipt (OR) -->
                            <div class="rounded-lg border border-sidebar-border p-4">
                                <label class="block text-sm font-medium mb-2">
                                    Official Receipt (OR)
                                </label>
                                <input
                                    type="file"
                                    @change="handleFileChange($event, 'or')"
                                    accept="image/jpeg,image/jpg,image/png,application/pdf"
                                    class="w-full text-sm"
                                />
                                <div v-if="orPreview" class="mt-3 relative">
                                    <button
                                        type="button"
                                        @click="removeFile('or')"
                                        class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 z-10"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <img v-if="orFile?.type.startsWith('image/')" :src="orPreview" class="h-32 w-full object-cover rounded" />
                                    <div v-else class="flex items-center justify-center h-32 bg-gray-100 rounded">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificate of Registration (CR) -->
                            <div class="rounded-lg border border-sidebar-border p-4">
                                <label class="block text-sm font-medium mb-2">
                                    Certificate of Registration (CR)
                                </label>
                                <input
                                    type="file"
                                    @change="handleFileChange($event, 'cr')"
                                    accept="image/jpeg,image/jpg,image/png,application/pdf"
                                    class="w-full text-sm"
                                />
                                <div v-if="crPreview" class="mt-3 relative">
                                    <button
                                        type="button"
                                        @click="removeFile('cr')"
                                        class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 z-10"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <img v-if="crFile?.type.startsWith('image/')" :src="crPreview" class="h-32 w-full object-cover rounded" />
                                    <div v-else class="flex items-center justify-center h-32 bg-gray-100 rounded">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Insurance -->
                            <div class="rounded-lg border border-sidebar-border p-4">
                                <label class="block text-sm font-medium mb-2">
                                    Insurance
                                </label>
                                <input
                                    type="file"
                                    @change="handleFileChange($event, 'insurance')"
                                    accept="image/jpeg,image/jpg,image/png,application/pdf"
                                    class="w-full text-sm"
                                />
                                <div v-if="insurancePreview" class="mt-3 relative">
                                    <button
                                        type="button"
                                        @click="removeFile('insurance')"
                                        class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 z-10"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <img v-if="insuranceFile?.type.startsWith('image/')" :src="insurancePreview" class="h-32 w-full object-cover rounded" />
                                    <div v-else class="flex items-center justify-center h-32 bg-gray-100 rounded">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Receipt -->
                            <div class="rounded-lg border border-sidebar-border p-4">
                                <label class="block text-sm font-medium mb-2">
                                    Receipt
                                </label>
                                <input
                                    type="file"
                                    @change="handleFileChange($event, 'receipt')"
                                    accept="image/jpeg,image/jpg,image/png,application/pdf"
                                    class="w-full text-sm"
                                />
                                <div v-if="receiptPreview" class="mt-3 relative">
                                    <button
                                        type="button"
                                        @click="removeFile('receipt')"
                                        class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 z-10"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <img v-if="receiptFile?.type.startsWith('image/')" :src="receiptPreview" class="h-32 w-full object-cover rounded" />
                                    <div v-else class="flex items-center justify-center h-32 bg-gray-100 rounded">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Inspection Report -->
                            <div class="rounded-lg border border-sidebar-border p-4 md:col-span-2">
                                <label class="block text-sm font-medium mb-2">
                                    Inspection Report
                                </label>
                                <input
                                    type="file"
                                    @change="handleFileChange($event, 'inspection')"
                                    accept="image/jpeg,image/jpg,image/png,application/pdf"
                                    class="w-full text-sm"
                                />
                                <div v-if="inspectionPreview" class="mt-3 relative max-w-md">
                                    <button
                                        type="button"
                                        @click="removeFile('inspection')"
                                        class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 z-10"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <img v-if="inspectionFile?.type.startsWith('image/')" :src="inspectionPreview" class="h-32 w-full object-cover rounded" />
                                    <div v-else class="flex items-center justify-center h-32 bg-gray-100 rounded">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-muted-foreground">
                            Accepted formats: JPEG, JPG, PNG, PDF (Max 5MB per file)
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-4 pt-4 border-t">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Vehicle</span>
                        </button>
                        
                        <button
                            type="button"
                            @click="router.visit(vehicleRoutes.list.url())"
                            class="inline-flex items-center gap-2 rounded-lg border border-sidebar-border px-6 py-2.5 text-sm font-medium transition-colors hover:bg-sidebar-accent"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>