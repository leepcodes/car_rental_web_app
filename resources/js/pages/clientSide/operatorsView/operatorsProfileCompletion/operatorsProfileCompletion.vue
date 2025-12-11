<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppHeader from '../../components/AppHeader.vue';
import AppFooter from '../../components/AppFooter.vue';

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { RadioGroup, RadioGroupItem } from '../../../../components/ui/radio-group';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { MapPin, Calendar, User, FileText, Upload, AlertCircle, CheckCircle2, Loader2, ShieldCheck, X } from 'lucide-vue-next';

const form = useForm({
  address: '',
  age: '' as string | number,
  gender: '',
  license_number: '',
  license_file: null as File | null,
});

const previewUrl = ref<string | null>(null);

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    form.license_file = file;
    
    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
      previewUrl.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const removeFile = () => {
  previewUrl.value = null;
  form.license_file = null;
  const fileInput = document.getElementById('license-upload') as HTMLInputElement;
  if (fileInput) fileInput.value = '';
};

const submit = () => {
  form.post(route('operator.profile.complete.store'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Complete Your Operator Profile" />

  <AppHeader />
  
  <div class="min-h-screen bg-white">
    <main class="container max-w-3xl mx-auto px-4 py-8 md:py-12 mt-20">
      <Card class="border-2">
        <CardHeader class="space-y-1 pb-6">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 rounded-lg">
              <ShieldCheck class="h-6 w-6 text-primary" />
            </div>
            <div class="flex-1">
              <CardTitle class="text-2xl">Complete Your Operator Profile</CardTitle>
              <CardDescription class="text-base">
                Become a verified driver and start earning today
              </CardDescription>
            </div>
            <Badge variant="secondary" class="hidden sm:flex">Step 1 of 1</Badge>
          </div>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Address -->
            <div class="space-y-2">
              <Label for="address" class="flex items-center gap-2">
                <MapPin class="h-4 w-4 text-muted-foreground" />
                Complete Address
              </Label>
              <Textarea
                id="address"
                v-model="form.address"
                placeholder="Enter your complete address (Street, Barangay, City, Province)"
                rows="3"
                required
                :class="form.errors.address ? 'border-destructive' : ''"
              />
              <p v-if="form.errors.address" class="text-sm text-destructive">
                {{ form.errors.address }}
              </p>
            </div>

            <!-- Age -->
            <div class="space-y-2">
              <Label for="age" class="flex items-center gap-2">
                <Calendar class="h-4 w-4 text-muted-foreground" />
                Age
              </Label>
              <Input
                id="age"
                v-model.number="form.age"
                type="number"
                min="18"
                max="100"
                placeholder="Enter your age"
                required
                :class="form.errors.age ? 'border-destructive' : ''"
              />
              <p v-if="form.errors.age" class="text-sm text-destructive">
                {{ form.errors.age }}
              </p>
            </div>

            <!-- Gender -->
            <div class="space-y-2">
              <Label class="flex items-center gap-2 mb-3">
                <User class="h-4 w-4 text-muted-foreground" />
                Gender
              </Label>
              <RadioGroup v-model="form.gender" class="grid grid-cols-3 gap-4" required>
                <div v-for="option in ['male', 'female', 'others']" :key="option">
                  <RadioGroupItem
                    :value="option"
                    :id="option"
                    class="peer sr-only"
                  />
                  <Label
                    :for="option"
                    class="flex items-center justify-center rounded-md border-2 border-muted bg-transparent px-3 py-2 hover:bg-accent hover:text-accent-foreground peer-data-[state=checked]:border-primary [&:has([data-state=checked])]:border-primary capitalize cursor-pointer transition-colors"
                  >
                    {{ option }}
                  </Label>
                </div>
              </RadioGroup>
              <p v-if="form.errors.gender" class="text-sm text-destructive">
                {{ form.errors.gender }}
              </p>
            </div>

            <!-- License Number -->
            <div class="space-y-2">
              <Label for="license_number" class="flex items-center gap-2">
                <FileText class="h-4 w-4 text-muted-foreground" />
                Professional Driver's License Number
              </Label>
              <Input
                id="license_number"
                v-model="form.license_number"
                type="text"
                placeholder="e.g., N01-12-345678"
                required
                :class="form.errors.license_number ? 'border-destructive' : ''"
              />
              <p v-if="form.errors.license_number" class="text-sm text-destructive">
                {{ form.errors.license_number }}
              </p>
            </div>

            <!-- License Upload -->
            <div class="space-y-2">
              <Label class="flex items-center gap-2">
                <Upload class="h-4 w-4 text-muted-foreground" />
                Upload Professional Driver's License
              </Label>
              
              <div class="relative">
                <input
                  type="file"
                  accept="image/*,.pdf"
                  @change="handleFileUpload"
                  class="hidden"
                  id="license-upload"
                  required
                />
                
                <label
                  v-if="!previewUrl"
                  for="license-upload"
                  :class="[
                    'flex flex-col items-center justify-center w-full h-40 rounded-lg border-2 border-dashed cursor-pointer transition-colors hover:bg-accent',
                    form.errors.license_file ? 'border-destructive' : 'border-muted-foreground/25'
                  ]"
                >
                  <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                    <Upload class="h-10 w-10 mb-3 text-muted-foreground" />
                    <p class="mb-2 text-sm text-muted-foreground">
                      <span class="font-semibold">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-muted-foreground">JPG, PNG, or PDF (Max 2MB)</p>
                  </div>
                </label>

                <div v-else class="relative w-full h-48 rounded-lg border-2 border-muted overflow-hidden bg-muted/20">
                  <img 
                    v-if="form.license_file?.type?.startsWith('image')"
                    :src="previewUrl" 
                    alt="License preview" 
                    class="w-full h-full object-contain"
                  />
                  <div v-else class="flex flex-col items-center justify-center h-full p-4">
                    <FileText class="h-16 w-16 text-muted-foreground mb-2" />
                    <span class="text-sm text-muted-foreground text-center break-all px-4">
                      {{ form.license_file?.name }}
                    </span>
                  </div>
                  <Button
                    type="button"
                    variant="destructive"
                    size="icon"
                    @click="removeFile"
                    class="absolute top-2 right-2 h-8 w-8"
                  >
                    <X class="h-4 w-4" />
                  </Button>
                </div>
              </div>
              <p v-if="form.errors.license_file" class="text-sm text-destructive">
                {{ form.errors.license_file }}
              </p>
            </div>

            <!-- Submit Button -->
            <Button
              type="submit"
              :disabled="form.processing"
              class="w-full h-11"
              size="lg"
            >
              <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
              <CheckCircle2 v-else class="mr-2 h-4 w-4" />
              {{ form.processing ? 'Submitting...' : 'Submit for Verification' }}
            </Button>

            <!-- Warning Alert -->
            <Alert variant="default" class="border-amber-200 bg-amber-50 dark:border-amber-900 dark:bg-amber-950">
              <AlertCircle class="h-4 w-4 text-amber-600 dark:text-amber-500" />
              <AlertDescription class="text-amber-900 dark:text-amber-100">
                <strong class="font-semibold">Verification Required</strong>
                <p class="mt-1 text-sm text-amber-700 dark:text-amber-200">
                  Your profile will be reviewed by our team. This process may take 24-48 hours. You'll receive an email notification once verified.
                </p>
              </AlertDescription>
            </Alert>

            <!-- Requirements Checklist -->
            <Alert variant="default" class="border-emerald-200 bg-emerald-50 dark:border-emerald-900 dark:bg-emerald-950">
              <CheckCircle2 class="h-4 w-4 text-emerald-600 dark:text-emerald-500" />
              <AlertDescription class="text-emerald-900 dark:text-emerald-100">
                <strong class="font-semibold">Requirements Checklist</strong>
                <ul class="mt-2 space-y-1 text-sm text-emerald-700 dark:text-emerald-200">
                  <li class="flex items-center gap-2">
                    <CheckCircle2 class="h-3.5 w-3.5 flex-shrink-0" />
                    Valid professional driver's license
                  </li>
                  <li class="flex items-center gap-2">
                    <CheckCircle2 class="h-3.5 w-3.5 flex-shrink-0" />
                    Minimum 18 years of age
                  </li>
                  <li class="flex items-center gap-2">
                    <CheckCircle2 class="h-3.5 w-3.5 flex-shrink-0" />
                    Clear photo of license (front side)
                  </li>
                  <li class="flex items-center gap-2">
                    <CheckCircle2 class="h-3.5 w-3.5 flex-shrink-0" />
                    Complete and accurate information
                  </li>
                </ul>
              </AlertDescription>
            </Alert>
          </form>
        </CardContent>
      </Card>
    </main>
    
    <AppFooter />
  </div>
</template>