<script setup lang="ts">
import { ref } from 'vue';
import AppHeader from '@/pages/clientSide/components/AppHeader.vue';
import AppFooter from '@/pages/clientSide/components/AppFooter.vue';
import Map from '@/pages/clientSide/components/Map.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '../../components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';

withDefaults(
  defineProps<{
    canRegister?: boolean;
  }>(),
  { canRegister: true }
);

const formData = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
});

const isSubmitting = ref(false);
const submitSuccess = ref(false);

async function handleSubmit() {
  isSubmitting.value = true;
  
  // Simulate API call
  setTimeout(() => {
    submitSuccess.value = true;
    isSubmitting.value = false;
    
    // Reset form
    formData.value = {
      name: '',
      email: '',
      phone: '',
      subject: '',
      message: ''
    };
    
    // Hide success message after 3 seconds
    setTimeout(() => {
      submitSuccess.value = false;
    }, 3000);
  }, 1000);
}
</script>

<template>
  <div class="min-h-screen bg-neutral-50">
    <AppHeader :can-register="canRegister" />>
    <!-- Main Content -->
    <section class="py-28 md:py-16 lg:py-30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8 lg:gap-12">
          
          <!-- Contact Form - Takes 2 columns -->
          <div class="lg:col-span-2">
            <Card class="shadow-xl">
              <CardHeader>
                <CardTitle class="text-2xl md:text-3xl">Send us a Message</CardTitle>
                <CardDescription>Fill out the form below and we'll get back to you shortly</CardDescription>
              </CardHeader>
              <CardContent>
                <!-- Success Alert -->
                <Alert v-if="submitSuccess" class="mb-6 bg-green-50 border-green-500 text-green-700">
                  <AlertDescription class="flex items-center gap-3">
                    <span class="text-2xl">✓</span>
                    <span class="font-semibold">Message sent successfully! We'll get back to you soon.</span>
                  </AlertDescription>
                </Alert>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                  <!-- Name and Email Row -->
                  <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <Label for="name">Full Name *</Label>
                      <Input
                        id="name"
                        v-model="formData.name"
                        type="text"
                        required
                        placeholder="John Doe"
                      />
                    </div>
                    <div class="space-y-2">
                      <Label for="email">Email Address *</Label>
                      <Input
                        id="email"
                        v-model="formData.email"
                        type="email"
                        required
                        placeholder="john@example.com"
                      />
                    </div>
                  </div>

                  <!-- Phone and Subject Row -->
                  <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <Label for="phone">Phone Number</Label>
                      <Input
                        id="phone"
                        v-model="formData.phone"
                        type="tel"
                        placeholder="+63 912 345 6789"
                      />
                    </div>
                    <div class="space-y-2">
                      <Label for="subject">Subject *</Label>
                      <Input
                        id="subject"
                        v-model="formData.subject"
                        type="text"
                        required
                        placeholder="Rental Inquiry"
                      />
                    </div>
                  </div>

                  <!-- Message -->
                  <div class="space-y-2">
                    <Label for="message">Message *</Label>
                    <Textarea
                      id="message"
                      v-model="formData.message"
                      required
                      rows="6"
                      placeholder="Tell us how we can help you..."
                      class="resize-none"
                    />
                  </div>

                  <!-- Submit button -->
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="w-full bg-neutral-600 hover:bg-neutral-600/90"
                    size="lg"
                  >
                    <span v-if="!isSubmitting">Send Message</span>
                    <span v-else>Sending...</span>
                    <svg v-if="!isSubmitting" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                  </button>
                </form>
              </CardContent>
            </Card>
          </div>

          <!-- Contact Information - Takes 1 column -->
          <div class="space-y-6">
            
            <!-- Contact Details Card -->
            <Card class="shadow-xl">
              <CardHeader>
                <CardTitle>Contact Information</CardTitle>
              </CardHeader>
              <CardContent class="space-y-6">
                <!-- Address -->
                <div class="flex gap-4">
                  <div class="flex-shrink-0 w-12 h-12 bg-neutral-600/10 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">📍</span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-neutral-800 mb-1">Address</h4>
                    <p class="text-neutral-600 text-sm">
                      123 Main Street, Quezon City<br />
                      Metro Manila, Philippines
                    </p>
                  </div>
                </div>

                <!-- Phone -->
                <div class="flex gap-4">
                  <div class="flex-shrink-0 w-12 h-12 bg-neutral-600/10 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">📞</span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-neutral-800 mb-1">Phone</h4>
                    <p class="text-neutral-600 text-sm">
                      +63 912 345 6789<br />
                      +63 998 765 4321
                    </p>
                  </div>
                </div>

                <!-- Email -->
                <div class="flex gap-4">
                  <div class="flex-shrink-0 w-12 h-12 bg-neutral-600/10 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">📧</span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-neutral-800 mb-1">Email</h4>
                    <p class="text-neutral-600 text-sm">
                      info@carrental.com<br />
                      support@carrental.com
                    </p>
                  </div>
                </div>

                <!-- Hours -->
                <div class="flex gap-4">
                  <div class="flex-shrink-0 w-12 h-12 bg-neutral-600/10 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">🕐</span>
                  </div>
                  <div>
                    <h4 class="font-semibold text-neutral-800 mb-1">Business Hours</h4>
                    <p class="text-neutral-600 text-sm">
                      Monday - Friday: 8AM - 8PM<br />
                      Saturday - Sunday: 9AM - 6PM
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Social Media Card -->
            <Card class="shadow-xl bg-gradient-to-br from-neutral-600 to-neutral-600/80 text-white border-none">
              <CardHeader>
                <CardTitle class="text-white">Follow Us</CardTitle>
                <CardDescription class="text-white/90">
                  Stay connected on social media for updates and promotions
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div class="grid grid-cols-4 gap-3">
                  <button variant="secondary" size="icon" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm border-none h-12">
                    <span class="text-2xl">📘</span>
                  </button>
                  <button variant="secondary" size="icon" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm border-none h-12">
                    <span class="text-2xl">📷</span>
                  </button>
                  <button variant="secondary" size="icon" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm border-none h-12">
                    <span class="text-2xl">🐦</span>
                  </button>
                  <button variant="secondary" size="icon" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm border-none h-12">
                    <span class="text-2xl">💼</span>
                  </button>
                </div>
              </CardContent>
            </Card>

            <!-- Quick Response Card -->
            <Card class="bg-neutral-100 border-neutral-200">
              <CardContent class="pt-6">
                <div class="flex items-center gap-3 mb-3">
                  <span class="text-3xl">⚡</span>
                  <h4 class="font-bold text-neutral-800">Quick Response</h4>
                </div>
                <p class="text-neutral-600 text-sm">
                  We typically respond within 24 hours during business days. For urgent matters, please call us directly.
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
           <Map />
      </div>
    </section>

    <AppFooter />
  </div>
</template>