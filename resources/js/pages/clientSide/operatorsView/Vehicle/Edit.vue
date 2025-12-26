<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import vehicleRoutes from '@/routes/operator/vehicles';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Vehicle {
    id: number;
    license_plate: string;
    chasis_number: string;
    brand: string;
    model: string;
    year: number;
    is_active: boolean;
    coding_day: string | null;
    operator_locations: number | null;
}

interface Attachment {
    id: number;
    vehicle_id: number;
    attachment_type: string;
    attachment_url: string;
    created_at: string;
}

interface OperatorLocation {
    id: number;
    name: string;
}

const props = defineProps<{
    vehicle: Vehicle;
    attachments: Attachment[];
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
        title: props.vehicle.license_plate,
        href: vehicleRoutes.show.url(props.vehicle.id),
    },
    {
        title: 'Edit',
        href: vehicleRoutes.edit.url(props.vehicle.id),
    },
];

const form = useForm({
    license_plate: props.vehicle.license_plate,
    chasis_number: props.vehicle.chasis_number,
    brand: props.vehicle.brand,
    model: props.vehicle.model,
    year: props.vehicle.year,
    is_active: props.vehicle.is_active,
    operator_locations: props.vehicle.operator_locations,
    coding_day: props.vehicle.coding_day || '',
    new_attachments: [] as File[],
    new_attachment_types: [] as string[],
    delete_attachments: [] as number[],
});

const newAttachmentPreviews = ref<string[]>([]);

const codingDays = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
    'Sunday',
];

const attachmentTypes = [
    { value: 'or', label: 'Official Receipt (OR)' },
    { value: 'cr', label: 'Certificate of Registration (CR)' },
    { value: 'receipt', label: 'Receipt' },
    { value: 'inspection', label: 'Inspection Report' },
    { value: 'insurance', label: 'Insurance' },
    { value: 'other', label: 'Other' },
];

const getAttachmentTypeLabel = (type: string) => {
    return attachmentTypes.find(t => t.value === type)?.label || type;
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        form.new_attachments.push(...files);
        form.new_attachment_types.push(...files.map(() => 'other'));

        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                newAttachmentPreviews.value.push(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        });
    }
};

const removeNewAttachment = (index: number) => {
    form.new_attachments.splice(index, 1);
    form.new_attachment_types.splice(index, 1);
    newAttachmentPreviews.value.splice(index, 1);
};

const toggleDeleteAttachment = (attachmentId: number) => {
    const index = form.delete_attachments.indexOf(attachmentId);
    if (index > -1) {
        form.delete_attachments.splice(index, 1);
    } else {
        form.delete_attachments.push(attachmentId);
    }
};

const submit = () => {
    form.post(vehicleRoutes.update.url(props.vehicle.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            // Clear new attachments after successful update
            form.new_attachments = [];
            form.new_attachment_types = [];
            newAttachmentPreviews.value = [];
        },
    });
};
</script>

<template>
    <Head :title="`Edit Vehicle: ${vehicle.license_plate}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">Edit Vehicle</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Update vehicle information and documents
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

                    <!-- Existing Documents -->
                    <div v-if="attachments.length > 0" class="space-y-4">
                        <h3 class="text-lg font-semibold">Existing Documents</h3>
                        
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="attachment in attachments"
                                :key="attachment.id"
                                :class="[
                                    'relative rounded-lg border p-4 transition-all',
                                    form.delete_attachments.includes(attachment.id)
                                        ? 'border-red-500 bg-red-50 dark:bg-red-900/20 opacity-50'
                                        : 'border-sidebar-border'
                                ]"
                            >
                                <button
                                    type="button"
                                    @click="toggleDeleteAttachment(attachment.id)"
                                    :class="[
                                        'absolute top-2 right-2 rounded-full p-1 text-white',
                                        form.delete_attachments.includes(attachment.id)
                                            ? 'bg-green-500 hover:bg-green-600'
                                            : 'bg-red-500 hover:bg-red-600'
                                    ]"
                                >
                                    <svg v-if="form.delete_attachments.includes(attachment.id)" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <!-- Preview -->
                                <a :href="`/storage/${attachment.attachment_url}`" target="_blank" class="block mb-2">
                                    <img
                                        v-if="attachment.attachment_url.match(/\.(jpg|jpeg|png)$/i)"
                                        :src="`/storage/${attachment.attachment_url}`"
                                        class="h-32 w-full object-cover rounded"
                                    />
                                    <div v-else class="flex items-center justify-center h-32 bg-gray-100 rounded dark:bg-gray-800">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </a>

                                <p class="text-xs font-medium">
                                    {{ getAttachmentTypeLabel(attachment.attachment_type) }}
                                </p>
                                <p class="text-xs text-muted-foreground mt-1">
                                    {{ new Date(attachment.created_at).toLocaleDateString() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- New Documents -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Add New Documents</h3>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Upload Documents
                            </label>
                            <input
                                type="file"
                                @change="handleFileChange"
                                multiple
                                accept="image/jpeg,image/jpg,image/png,application/pdf"
                                class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                Accepted: JPEG, JPG, PNG, PDF (Max 5MB per file)
                            </p>
                        </div>

                        <!-- New Attachment Previews -->
                        <div v-if="form.new_attachments.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="(file, index) in form.new_attachments"
                                :key="index"
                                class="relative rounded-lg border border-sidebar-border p-4"
                            >
                                <button
                                    type="button"
                                    @click="removeNewAttachment(index)"
                                    class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div v-if="file.type.startsWith('image/')" class="mb-2">
                                    <img :src="newAttachmentPreviews[index]" class="h-32 w-full object-cover rounded" />
                                </div>
                                <div v-else class="mb-2 flex items-center justify-center h-32 bg-gray-100 rounded">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <p class="text-xs font-medium truncate mb-2">{{ file.name }}</p>
                                
                                <select
                                    v-model="form.new_attachment_types[index]"
                                    class="w-full rounded-md border border-sidebar-border px-2 py-1 text-xs"
                                >
                                    <option v-for="type in attachmentTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-4 pt-4 border-t">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update Vehicle</span>
                        </button>
                        
                        <button
                            type="button"
                            @click="router.visit(vehicleRoutes.show.url(vehicle.id))"
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