<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppHeader from '../../components/AppHeader.vue';
import AppFooter from '../../components/AppFooter.vue';

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { MapPin, Calendar, User, FileText, Upload, Info, CheckCircle2, Loader2, UserCheck, X } from 'lucide-vue-next';

const formData = ref({
  address: '',
  age: '' as string | number,
  gender: '',
  license_number: '',
  license_file: null as File | null,
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);
const previewUrl = ref<string | null>(null);

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    formData.value.license_file = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
      previewUrl.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const removeFile = () => {
  previewUrl.value = null;
  formData.value.license_file = null;
  const fileInput = document.getElementById('license-upload') as HTMLInputElement;
  if (fileInput) fileInput.value = '';
};

const submit = () => {
  errors.value = {};
  
  const data = new FormData();
  data.append('address', formData.value.address);
  data.append('age', formData.value.age.toString());
  data.append('gender', formData.value.gender);
  data.append('license_number', formData.value.license_number);
  
  if (formData.value.license_file) {
    data.append('license_file', formData.value.license_file);
  }
  
  processing.value = true;
  
  router.post('/client/profile/complete', data, {
    preserveScroll: true,
    onError: (formErrors) => {
      errors.value = formErrors;
      processing.value = false;
    },
    onSuccess: () => {
      processing.value = false;
    },
    onFinish: () => {
      processing.value = false;
    }
  });
};
</script>

<template>
  <Head title="Complete Your Client Profile" />
  <AppHeader />
  
  <section class="relative min-h-screen px-6 lg:px-8 overflow-hidden bg-gradient-to-br from-neutral-500 via-neutral-800 to-neutral-900">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
    </div>

    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>
    
    <main class="relative container max-w-3xl mx-auto px-4 py-8 md:py-12 mt-20">
      <Card class="border shadow-sm bg-white relative z-10">
        <CardHeader class="space-y-1 pb-6">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-gray-100 rounded-lg">
              <UserCheck class="h-6 w-6 text-gray-700" />
            </div>
            <div class="flex-1">
              <CardTitle class="text-2xl text-gray-900">Complete Your Client Profile</CardTitle>
              <CardDescription class="text-base text-gray-600">
                Just a few more details to get you started on your journey
              </CardDescription>
            </div>
            <Badge variant="secondary" class="hidden sm:flex bg-gray-100 text-gray-700">Step 1 of 1</Badge>
          </div>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Address -->
            <div class="space-y-2">
              <Label for="address" class="flex items-center gap-2 text-gray-700">
                <MapPin class="h-4 w-4 text-blue-600" />
                Complete Address
              </Label>
              <Textarea
                id="address"
                v-model="formData.address"
                placeholder="Enter your complete address (Street, Barangay, City, Province)"
                rows="3"
                required
                :class="errors.address ? 'border-red-500' : 'border-gray-300'"
                class="resize-none bg-white text-gray-700"
              />
              <p v-if="errors.address" class="text-sm text-red-600">
                {{ errors.address }}
              </p>
            </div>

            <!-- Age -->
            <div class="space-y-2">
              <Label for="age" class="flex items-center gap-2 text-gray-700">
                <Calendar class="h-4 w-4 text-blue-600" />
                Age
              </Label>
              <Input
                id="age"
                v-model.number="formData.age"
                type="number"
                min="18"
                max="100"
                placeholder="Enter your age"
                required
                :class="errors.age ? 'border-red-500' : 'border-gray-300'"
                class="text-gray-700 bg-white"
              />
              <p v-if="errors.age" class="text-sm text-red-600">
                {{ errors.age }}
              </p>
            </div>

            <!-- Gender -->
            <div class="space-y-2">
              <Label class="flex items-center gap-2 mb-3 text-gray-700">
                <User class="h-4 w-4 text-blue-600" />
                Gender
              </Label>
              <RadioGroup v-model="formData.gender" class="grid grid-cols-3 gap-4">
                <div v-for="option in ['male', 'female', 'others']" :key="option">
                  <RadioGroupItem
                    :value="option"
                    :id="option"
                    class="peer sr-only"
                  />
                  <Label
                    :for="option"
                    class="flex items-center justify-center rounded-md border-2 border-gray-300 bg-white px-3 py-2 hover:bg-gray-50 hover:border-gray-400 peer-data-[state=checked]:border-blue-700 peer-data-[state=checked]:bg-gray-50 [&:has([data-state=checked])]:border-blue-700 capitalize cursor-pointer transition-colors text-gray-700"
                  >
                    {{ option }}
                  </Label>
                </div>
              </RadioGroup>
              <p v-if="errors.gender" class="text-sm text-red-600">
                {{ errors.gender }}
              </p>
            </div>

            <!-- License Number -->
            <div class="space-y-2">
              <Label for="license_number" class="flex items-center gap-2 text-gray-700">
                <FileText class="h-4 w-4 text-gray-500" />
                Driver's License Number
              </Label>
              <Input
                id="license_number"
                v-model="formData.license_number"
                type="text"
                placeholder="e.g., N01-12-345678"
                required
                :class="errors.license_number ? 'border-red-500' : 'border-gray-300'"
                class="text-gray-700 bg-white"
              />
              <p v-if="errors.license_number" class="text-sm text-red-600">
                {{ errors.license_number }}
              </p>
            </div>

            <!-- License Upload -->
            <div class="space-y-2">
              <Label class="flex items-center gap-2 text-gray-700 font-medium">
                <Upload class="h-4 w-4 text-blue-600" />
                Upload Driver's License
              </Label>
              
              <div class="relative">
                <input
                  type="file"
                  accept="image/*,.pdf"
                  @change="handleFileUpload"
                  class="hidden"
                  id="license-upload"
                />
                
                  <label
                  v-if="!previewUrl"
                  for="license-upload"
                  :class="[
                    'flex flex-col items-center justify-center w-full h-40 rounded-lg border-2 border-dashed cursor-pointer transition-all hover:border-blue-400 hover:bg-blue-50/50',
                    errors.license_file ? 'border-red-500 bg-red-50/50' : 'border-gray-300 bg-gray-50/50'
                  ]"
                >
                  <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                    <div class="p-3 bg-blue-100 rounded-full mb-3">
                      <Upload class="h-8 w-8 text-blue-600" />
                    </div>
                    <p class="mb-2 text-sm text-gray-600">
                      <span class="font-semibold text-blue-600">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-gray-500">JPG, PNG, or PDF (Max 2MB)</p>
                  </div>
                </label>

                <div v-else class="relative w-full h-52 rounded-lg border-2 border-blue-300 overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 shadow-sm">
                  <img 
                    v-if="formData.license_file?.type?.startsWith('image')"
                    :src="previewUrl" 
                    alt="License preview" 
                    class="w-full h-full object-contain p-2"
                  />
                  <div v-else class="flex flex-col items-center justify-center h-full p-4">
                    <div class="p-4 bg-white rounded-full mb-3 shadow-md">
                      <FileText class="h-12 w-12 text-blue-600" />
                    </div>
                    <span class="text-sm font-medium text-gray-700 text-center break-all px-4">
                      {{ formData.license_file?.name }}
                    </span>
                  </div>
                  <button
                    type="button"
                    @click="removeFile"
                    class="absolute top-3 right-3 h-9 w-9 bg-red-500 text-white hover:bg-red-600 rounded-lg flex items-center justify-center transition-colors shadow-md"
                  >
                    <X class="h-5 w-5" />
                  </button>
                </div>
              </div>
              <p v-if="errors.license_file" class="text-sm text-red-600 flex items-center gap-1">
                {{ errors.license_file }}
              </p>
            </div>

            <!-- Submit button -->
            <button
              type="submit"
              :disabled="processing"
              class="w-full h-11 bg-gray-900 text-white hover:bg-gray-800 rounded-md font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <Loader2 v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
              <CheckCircle2 v-else class="mr-2 h-4 w-4" />
              {{ processing ? 'Submitting...' : 'Complete Profile' }}
            </button>

            <!-- Info Alert -->
            <Alert variant="default" class="border-blue-200 bg-blue-50">
              <Info class="h-4 w-4 text-blue-600" />
              <AlertDescription class="text-blue-900">
                <strong class="font-semibold">Important Information</strong>
                <p class="mt-1 text-sm text-blue-800">
                  Please ensure all information is accurate. Your driver's license will be verified before you can start booking rides.
                </p>
              </AlertDescription>
            </Alert>
          </form>
        </CardContent>
      </Card>
    </main>
  </section>
  
  <AppFooter />
</template>