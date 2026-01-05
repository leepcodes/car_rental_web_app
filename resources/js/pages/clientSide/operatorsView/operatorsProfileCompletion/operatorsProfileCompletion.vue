<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppHeader from '../../components/AppHeader.vue';
import AppFooter from '../../components/AppFooter.vue';

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { MapPin, Calendar, User, FileText, Upload, AlertCircle, CheckCircle2, Loader2, ShieldCheck, X } from 'lucide-vue-next';

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
  
  router.post('/operator/profile/complete', data, {
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
  <Head title="Complete Your Operator Profile" />
  <AppHeader />
  
  <section class="relative min-h-screen px-6 lg:px-8 overflow-hidden bg-gradient-to-br from-neutral-50 to-neutral-100">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-[#0081A7]/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-[#00AFB9]/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
    </div>

    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#0081A708_1px,transparent_1px),linear-gradient(to_bottom,#0081A708_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>
    
    <main class="relative container max-w-3xl mx-auto px-4 py-8 md:py-12 mt-20">
      <Card class="border-2 border-[#0081A7] shadow-xl bg-white relative z-10">
        <CardHeader class="space-y-1 pb-6 bg-white border-b-2 border-[#0081A7]/20">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-[#0081A7]/10 rounded-lg">
              <ShieldCheck class="h-7 w-7 text-[#0081A7]" />
            </div>
            <div class="flex-1">
              <CardTitle class="text-2xl text-neutral-900 font-['Roboto']">Complete Your Operator Profile</CardTitle>
              <CardDescription class="text-neutral-600 text-base">
                Become a verified driver and start earning today
              </CardDescription>
            </div>
            <Badge variant="secondary" class="hidden sm:flex bg-[#0081A7]/10 text-[#0081A7] border border-[#0081A7]/30">
              Step 1 of 1
            </Badge>
          </div>
        </CardHeader>

        <CardContent class="pt-6">
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Address -->
            <div class="space-y-2">
              <Label for="address" class="flex items-center gap-2 text-neutral-900 font-medium">
                <MapPin class="h-4 w-4 text-[#0081A7]" />
                Complete Address
              </Label>
              <Textarea
                id="address"
                v-model="formData.address"
                placeholder="Enter your complete address (Street, Barangay, City, Province)"
                rows="3"
                required
                :class="[
                  'resize-none bg-white text-neutral-900 border-2 transition-colors',
                  errors.address 
                    ? 'border-red-500 focus:border-red-500' 
                    : 'border-[#0081A7]/30 focus:border-[#0081A7]'
                ]"
              />
              <p v-if="errors.address" class="text-sm text-red-600 flex items-center gap-1">
                <AlertCircle class="h-3.5 w-3.5" />
                {{ errors.address }}
              </p>
            </div>

            <!-- Age -->
            <div class="space-y-2">
              <Label for="age" class="flex items-center gap-2 text-neutral-900 font-medium">
                <Calendar class="h-4 w-4 text-[#0081A7]" />
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
                :class="[
                  'text-neutral-900 bg-white border-2 transition-colors',
                  errors.age 
                    ? 'border-red-500 focus:border-red-500' 
                    : 'border-[#0081A7]/30 focus:border-[#0081A7]'
                ]"
              />
              <p v-if="errors.age" class="text-sm text-red-600 flex items-center gap-1">
                <AlertCircle class="h-3.5 w-3.5" />
                {{ errors.age }}
              </p>
            </div>

            <!-- Gender -->
            <div class="space-y-2">
              <Label class="flex items-center gap-2 mb-3 text-neutral-900 font-medium">
                <User class="h-4 w-4 text-[#0081A7]" />
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
                    class="flex items-center justify-center rounded-md border-2 border-[#0081A7]/30 bg-white px-3 py-2 hover:bg-[#0081A7]/5 hover:border-[#0081A7]/50 peer-data-[state=checked]:border-[#0081A7] peer-data-[state=checked]:bg-[#0081A7]/10 [&:has([data-state=checked])]:border-[#0081A7] capitalize cursor-pointer transition-colors text-neutral-900 font-medium"
                  >
                    {{ option }}
                  </Label>
                </div>
              </RadioGroup>
              <p v-if="errors.gender" class="text-sm text-red-600 flex items-center gap-1">
                <AlertCircle class="h-3.5 w-3.5" />
                {{ errors.gender }}
              </p>
            </div>

            <!-- License Number -->
            <div class="space-y-2">
              <Label for="license_number" class="flex items-center gap-2 text-neutral-900 font-medium">
                <FileText class="h-4 w-4 text-[#0081A7]" />
                Professional Driver's License Number
              </Label>
              <Input
                id="license_number"
                v-model="formData.license_number"
                type="text"
                placeholder="e.g., N01-12-345678"
                required
                :class="[
                  'text-neutral-900 bg-white border-2 transition-colors',
                  errors.license_number 
                    ? 'border-red-500 focus:border-red-500' 
                    : 'border-[#0081A7]/30 focus:border-[#0081A7]'
                ]"
              />
              <p v-if="errors.license_number" class="text-sm text-red-600 flex items-center gap-1">
                <AlertCircle class="h-3.5 w-3.5" />
                {{ errors.license_number }}
              </p>
            </div>

            <!-- License Upload -->
            <div class="space-y-2">
              <Label class="flex items-center gap-2 text-neutral-900 font-medium">
                <Upload class="h-4 w-4 text-[#0081A7]" />
                Upload Professional Driver's License
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
                    'flex flex-col items-center justify-center w-full h-44 rounded-lg border-2 border-dashed cursor-pointer transition-all',
                    errors.license_file 
                      ? 'border-red-500 bg-red-50/50' 
                      : 'border-[#0081A7]/30 bg-[#0081A7]/5 hover:border-[#0081A7] hover:bg-[#0081A7]/10'
                  ]"
                >
                  <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                    <div class="p-3 bg-[#0081A7]/10 rounded-full mb-3">
                      <Upload class="h-8 w-8 text-[#0081A7]" />
                    </div>
                    <p class="mb-2 text-sm text-neutral-700">
                      <span class="font-semibold text-[#0081A7]">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-neutral-500">JPG, PNG, or PDF (Max 2MB)</p>
                  </div>
                </label>

                <div v-else class="relative w-full h-52 rounded-lg border-2 border-[#0081A7] overflow-hidden bg-gradient-to-br from-[#0081A7]/5 to-[#00AFB9]/5 shadow-sm">
                  <img 
                    v-if="formData.license_file?.type?.startsWith('image')"
                    :src="previewUrl" 
                    alt="License preview" 
                    class="w-full h-full object-contain p-2"
                  />
                  <div v-else class="flex flex-col items-center justify-center h-full p-4">
                    <div class="p-4 bg-white rounded-full mb-3 shadow-md border-2 border-[#0081A7]/20">
                      <FileText class="h-12 w-12 text-[#0081A7]" />
                    </div>
                    <span class="text-sm font-medium text-neutral-900 text-center break-all px-4">
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
                <AlertCircle class="h-3.5 w-3.5" />
                {{ errors.license_file }}
              </p>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="processing"
              class="w-full h-12 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white hover:from-[#0081A7]/90 hover:to-[#00AFB9]/90 rounded-lg font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-lg hover:shadow-xl font-['Roboto']"
            >
              <Loader2 v-if="processing" class="mr-2 h-5 w-5 animate-spin" />
              <CheckCircle2 v-else class="mr-2 h-5 w-5" />
              {{ processing ? 'Submitting...' : 'Submit for Verification' }}
            </button>

            <!-- Warning Alert -->
            <Alert class="border-2 border-amber-200 bg-amber-50">
              <AlertCircle class="h-4 w-4 text-amber-600" />
              <AlertDescription class="text-amber-900">
                <strong class="font-semibold">Verification Required</strong>
                <p class="mt-1 text-sm text-amber-700">
                  Your profile will be reviewed by our team. This process may take 24-48 hours. You'll receive an email notification once verified.
                </p>
              </AlertDescription>
            </Alert>

            <!-- Requirements Checklist -->
            <Alert class="border-2 border-[#00AFB9]/30 bg-[#00AFB9]/5">
              <CheckCircle2 class="h-4 w-4 text-[#00AFB9]" />
              <AlertDescription class="text-neutral-900">
                <strong class="font-semibold">Requirements Checklist</strong>
                <ul class="mt-2 space-y-1.5 text-sm text-neutral-700">
                  <li class="flex items-center gap-2">
                    <CheckCircle2 class="h-3.5 w-3.5 flex-shrink-0 text-[#00AFB9]" />
                    Valid professional driver's license
                  </li>
                  <li class="flex items-center gap-2">
                    <CheckCircle2 class="h-3.5 w-3.5 flex-shrink-0 text-[#00AFB9]" />
                    Minimum 18 years of age
                  </li>
                  <li class="flex items-center gap-2">
                    <CheckCircle2 class="h-3.5 w-3.5 flex-shrink-0 text-[#00AFB9]" />
                    Clear photo of license (front side)
                  </li>
                  <li class="flex items-center gap-2">
                    <CheckCircle2 class="h-3.5 w-3.5 flex-shrink-0 text-[#00AFB9]" />
                    Complete and accurate information
                  </li>
                </ul>
              </AlertDescription>
            </Alert>
          </form>
        </CardContent>
      </Card>
    </main>
  </section> 
  
  <AppFooter />
</template>

<style scoped>
* {
  font-family: 'Roboto', sans-serif;
}
</style>