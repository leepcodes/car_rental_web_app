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

interface VehiclePhoto {
    id: number;
    file: File | null;
    preview: string;
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
    price: null as number | null,
    description: '',
    body_type: '',
    fuel_type: '',
    transmission: '',
    color: '',
    seating_capacity: null as number | null,
    is_active: true,
    is_featured: false,
    operator_locations: null as number | null,
    coding_day: '',
    features: [] as string[],
    rating: 5.0,
    reviews: 0,
    attachments: [] as File[],
    attachment_types: [] as string[],
});

// Feature input
const newFeature = ref('');
const availableFeatures = [
    'Air Conditioning',
    'Power Steering',
    'ABS Brakes',
    'Airbags',
    'Central Locking',
    'USB Port',
    'Bluetooth Audio',
    'Backup Camera',
    'GPS Navigation',
    'Leather Seats',
    'Sunroof',
    'Cruise Control',
    'Parking Sensors',
    'Keyless Entry',
    'Push Start',
    'Apple CarPlay',
    'Android Auto',
    'Heated Seats',
    'Premium Sound System',
    '4x4 Drive',
];

// Separate refs for document types
const orFile = ref<File | null>(null);
const orPreview = ref<string>('');
const crFile = ref<File | null>(null);
const crPreview = ref<string>('');
const insuranceFile = ref<File | null>(null);
const insurancePreview = ref<string>('');

// Vehicle photos array
const vehiclePhotos = ref<VehiclePhoto[]>([
    { id: 1, file: null, preview: '' }
]);
let photoIdCounter = 2;

const codingDays = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
    'Sunday',
];

const bodyTypes = ['Sedan', 'Hatchback', 'MPV', 'SUV', 'Van', 'Pickup'];
const fuelTypes = ['Gasoline', 'Diesel', 'Hybrid', 'Electric'];
const transmissions = ['Manual', 'Automatic', 'CVT'];

const addFeature = () => {
    const feature = newFeature.value.trim();
    if (feature && !form.features.includes(feature)) {
        form.features.push(feature);
        newFeature.value = '';
    }
};

const addPredefinedFeature = (feature: string) => {
    if (!form.features.includes(feature)) {
        form.features.push(feature);
    }
};

const removeFeature = (index: number) => {
    form.features.splice(index, 1);
};

const handleFileChange = (event: Event, type: 'or' | 'cr' | 'insurance') => {
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
            }
        };
        
        reader.readAsDataURL(file);
    }
};

const handleVehiclePhotoChange = (event: Event, photoId: number) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const preview = e.target?.result as string;
            const photo = vehiclePhotos.value.find(p => p.id === photoId);
            if (photo) {
                photo.file = file;
                photo.preview = preview;
            }
        };
        
        reader.readAsDataURL(file);
    }
};

const removeFile = (type: 'or' | 'cr' | 'insurance') => {
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
    }
};

const removeVehiclePhoto = (photoId: number) => {
    const photo = vehiclePhotos.value.find(p => p.id === photoId);
    if (photo) {
        photo.file = null;
        photo.preview = '';
    }
};

const addVehiclePhotoSlot = () => {
    vehiclePhotos.value.push({
        id: photoIdCounter++,
        file: null,
        preview: ''
    });
};

const removeVehiclePhotoSlot = (photoId: number) => {
    if (vehiclePhotos.value.length > 1) {
        vehiclePhotos.value = vehiclePhotos.value.filter(p => p.id !== photoId);
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
    
    // Add all vehicle photos
    vehiclePhotos.value.forEach((photo) => {
        if (photo.file) {
            form.attachments.push(photo.file);
            form.attachment_types.push('vehicle_photo');
        }
    });
    
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
                    <!-- Display general errors -->
                    <div v-if="form.errors && Object.keys(form.errors).length > 0" class="rounded-lg bg-red-50 p-4 border border-red-200">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h4>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            <li v-for="(error, field) in form.errors" :key="field">
                                <strong>{{ field }}:</strong> {{ error }}
                            </li>
                        </ul>
                    </div>

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

                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Price per Day (₱) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model.number="form.price"
                                    type="number"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    placeholder="1500"
                                    min="0"
                                    step="0.01"
                                    required
                                />
                                <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.price }}
                                </p>
                            </div>

                            <!-- Body Type -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Body Type
                                </label>
                                <select
                                    v-model="form.body_type"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                >
                                    <option value="">Select body type</option>
                                    <option v-for="type in bodyTypes" :key="type" :value="type">
                                        {{ type }}
                                    </option>
                                </select>
                            </div>

                            <!-- Fuel Type -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Fuel Type
                                </label>
                                <select
                                    v-model="form.fuel_type"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                >
                                    <option value="">Select fuel type</option>
                                    <option v-for="fuel in fuelTypes" :key="fuel" :value="fuel">
                                        {{ fuel }}
                                    </option>
                                </select>
                            </div>

                            <!-- Transmission -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Transmission
                                </label>
                                <select
                                    v-model="form.transmission"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                >
                                    <option value="">Select transmission</option>
                                    <option v-for="trans in transmissions" :key="trans" :value="trans">
                                        {{ trans }}
                                    </option>
                                </select>
                            </div>

                            <!-- Color -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Color
                                </label>
                                <input
                                    v-model="form.color"
                                    type="text"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    placeholder="White"
                                />
                            </div>

                            <!-- Seating Capacity -->
                            <div>
                                <label class="block text-sm font-medium mb-2">
                                    Seating Capacity
                                </label>
                                <input
                                    v-model.number="form.seating_capacity"
                                    type="number"
                                    class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    placeholder="5"
                                    min="1"
                                    max="50"
                                />
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

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="w-full rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                placeholder="Describe your vehicle..."
                            ></textarea>
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

                        <!-- Status Toggles -->
                        <div class="flex items-center gap-6">
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
                            <div class="flex items-center gap-2">
                                <input
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    id="is_featured"
                                    class="rounded border-sidebar-border"
                                />
                                <label for="is_featured" class="text-sm font-medium">
                                    Featured vehicle
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Features & Amenities</h3>
                        <p class="text-sm text-muted-foreground">
                            Add features that your vehicle offers
                        </p>

                        <!-- Quick Add Predefined Features -->
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Quick Add Features
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="feature in availableFeatures"
                                    :key="feature"
                                    type="button"
                                    @click="addPredefinedFeature(feature)"
                                    :disabled="form.features.includes(feature)"
                                    :class="[
                                        'px-3 py-1.5 text-xs rounded-lg border transition-colors',
                                        form.features.includes(feature)
                                            ? 'bg-primary/10 border-primary text-primary cursor-default'
                                            : 'bg-white border-sidebar-border hover:border-primary hover:text-primary'
                                    ]"
                                >
                                    {{ feature }}
                                    <span v-if="form.features.includes(feature)" class="ml-1">✓</span>
                                </button>
                            </div>
                        </div>

                        <!-- Custom Feature Input -->
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Add Custom Feature
                            </label>
                            <div class="flex gap-2">
                                <input
                                    v-model="newFeature"
                                    type="text"
                                    @keyup.enter="addFeature"
                                    class="flex-1 rounded-md border border-sidebar-border px-3 py-2 text-sm"
                                    placeholder="Type a custom feature and press Enter"
                                />
                                <button
                                    type="button"
                                    @click="addFeature"
                                    class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90"
                                >
                                    Add
                                </button>
                            </div>
                        </div>

                        <!-- Selected Features List -->
                        <div v-if="form.features.length > 0">
                            <label class="block text-sm font-medium mb-2">
                                Selected Features ({{ form.features.length }})
                            </label>
                            <div class="rounded-lg border border-sidebar-border p-4">
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        v-for="(feature, index) in form.features"
                                        :key="index"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-sm"
                                    >
                                        <span>{{ feature }}</span>
                                        <button
                                            type="button"
                                            @click="removeFeature(index)"
                                            class="hover:text-red-600 transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">Vehicle Documents</h3>
                        <p class="text-sm text-muted-foreground">
                            Upload required documents for vehicle registration
                        </p>
                        
                        <div class="grid gap-4 md:grid-cols-3">
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
                        </div>
                    </div>

                    <!-- Vehicle Photos Section -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">Vehicle Photos</h3>
                                <p class="text-sm text-muted-foreground">
                                    Upload photos of your vehicle (multiple photos allowed)
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="addVehiclePhotoSlot"
                                class="inline-flex items-center gap-2 rounded-lg border border-primary bg-primary/10 px-4 py-2 text-sm font-medium text-primary hover:bg-primary/20 transition-colors"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Photo
                            </button>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="photo in vehiclePhotos"
                                :key="photo.id"
                                class="rounded-lg border border-sidebar-border p-4 relative"
                            >
                                <button
                                    v-if="vehiclePhotos.length > 1"
                                    type="button"
                                    @click="removeVehiclePhotoSlot(photo.id)"
                                    class="absolute top-2 right-2 rounded-full bg-gray-500 p-1 text-white hover:bg-gray-600 z-20"
                                    title="Remove this photo slot"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <label class="block text-sm font-medium mb-2">
                                    Vehicle Photo {{ vehiclePhotos.indexOf(photo) + 1 }}
                                </label>
                                <input
                                    type="file"
                                    @change="handleVehiclePhotoChange($event, photo.id)"
                                    accept="image/jpeg,image/jpg,image/png"
                                    class="w-full text-sm"
                                />
                                <div v-if="photo.preview" class="mt-3 relative">
                                    <button
                                        type="button"
                                        @click="removeVehiclePhoto(photo.id)"
                                        class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 z-10"
                                        title="Clear this photo"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <img :src="photo.preview" class="h-40 w-full object-cover rounded" />
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-muted-foreground">
                            Accepted formats: JPEG, JPG, PNG (Max 5MB per file)
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