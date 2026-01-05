<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Calendar, CheckCircle, AlertCircle, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardFooter, CardHeader } from '@/components/ui/card';

interface BookedDate {
  id: number;
  start_date: string;
  end_date: string;
  status: string;
}

interface Props {
  vehicleId: number;
  price: number;
  bookedDates: BookedDate[];
  codingDays?: number[];
}

const props = withDefaults(defineProps<Props>(), {
  codingDays: () => []
});

const emit = defineEmits(['booking-attempt']);

const page = usePage();
const user = computed(() => page.props.auth?.user);

const selectedPickupDate = ref('');
const selectedReturnDate = ref('');
const currentMonth = ref(new Date());

const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// Get active bookings
const activeBookings = computed(() => 
  props.bookedDates.filter(b => ['confirmed', 'ongoing'].includes(b.status))
);

// Check if a specific date is booked
const isDateBooked = (dateStr: string): boolean => {
  const checkDate = new Date(dateStr);
  return activeBookings.value.some(booking => {
    const startDate = new Date(booking.start_date);
    const endDate = new Date(booking.end_date);
    return checkDate >= startDate && checkDate <= endDate;
  });
};

// Check if date is in the past
const isPastDate = (dateStr: string): boolean => {
  const date = new Date(dateStr);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  return date < today;
};

// Check if date is a coding day (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
const isCodingDay = (dateStr: string): boolean => {
  if (!props.codingDays || props.codingDays.length === 0) return false;
  const date = new Date(dateStr);
  const dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, etc.
  
  // Check if this day of week is in the coding days array
  return props.codingDays.includes(dayOfWeek);
};

// Generate calendar days
const calendarDays = computed(() => {
  const year = currentMonth.value.getFullYear();
  const month = currentMonth.value.getMonth();
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const daysInMonth = lastDay.getDate();
  const startingDayOfWeek = firstDay.getDay();
  
  const days = [];
  
  // Previous month's days (empty cells)
  for (let i = 0; i < startingDayOfWeek; i++) {
    days.push({ day: null, date: null, disabled: true, booked: false, isPast: false, isCoding: false });
  }
  
  // Current month's days
  for (let day = 1; day <= daysInMonth; day++) {
    const date = new Date(year, month, day);
    const dateStr = date.toISOString().split('T')[0];
    const isPast = isPastDate(dateStr);
    const isBooked = isDateBooked(dateStr);
    const isCoding = isCodingDay(dateStr);
    
    days.push({
      day,
      date: dateStr,
      disabled: isPast || isBooked,
      booked: isBooked,
      isPast,
      isCoding
    });
  }
  
  return days;
});

const handleDateClick = (dateStr: string, disabled: boolean) => {
  if (disabled || !dateStr) return;
  
  if (!selectedPickupDate.value || (selectedPickupDate.value && selectedReturnDate.value)) {
    // Start new selection
    selectedPickupDate.value = dateStr;
    selectedReturnDate.value = '';
  } else {
    const pickupDate = new Date(selectedPickupDate.value);
    const clickedDate = new Date(dateStr);
    
    if (clickedDate < pickupDate) {
      // If clicked date is before pickup, reset and start new selection
      selectedPickupDate.value = dateStr;
      selectedReturnDate.value = '';
    } else {
      // Check if any date in range is booked
      let hasBlockedInRange = false;
      for (let d = new Date(pickupDate); d <= clickedDate; d.setDate(d.getDate() + 1)) {
        const checkDateStr = d.toISOString().split('T')[0];
        if (isDateBooked(checkDateStr)) {
          hasBlockedInRange = true;
          break;
        }
      }
      
      if (hasBlockedInRange) {
        alert('Cannot select date range that includes booked dates');
        return;
      }
      
      selectedReturnDate.value = dateStr;
    }
  }
};

const isDateInRange = (dateStr: string): boolean => {
  if (!dateStr || !selectedPickupDate.value || !selectedReturnDate.value) return false;
  const date = new Date(dateStr);
  const pickup = new Date(selectedPickupDate.value);
  const returnD = new Date(selectedReturnDate.value);
  return date >= pickup && date <= returnD;
};

const previousMonth = () => {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1);
};

const nextMonth = () => {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1);
};

const totalDays = computed(() => {
  if (!selectedPickupDate.value || !selectedReturnDate.value) return 0;
  const pickup = new Date(selectedPickupDate.value);
  const returnDate = new Date(selectedReturnDate.value);
  const diffTime = Math.abs(returnDate.getTime() - pickup.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays || 1;
});

const totalPrice = computed(() => props.price * totalDays.value);
const serviceFee = computed(() => totalPrice.value * 0.05);
const grandTotal = computed(() => totalPrice.value + serviceFee.value);
const canBook = computed(() => selectedPickupDate.value && selectedReturnDate.value);

const handleBooking = () => {
  // Check if user is logged in first
  if (!user.value) {
    emit('booking-attempt');
    return;
  }

  if (!selectedPickupDate.value || !selectedReturnDate.value) {
    alert('Please select pickup and return dates');
    return;
  }
  
  router.get(`/client/booking/${props.vehicleId}/form`, {
    pickup_date: selectedPickupDate.value,
    return_date: selectedReturnDate.value,
  });
};

const formatDate = (dateStr: string): string => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

<template>
  <div class="sticky top-24">
    <Card class="border-2 border-[#0081A7] shadow-xl bg-white overflow-hidden">
      <CardHeader class="bg-white border-b border-neutral-200">
        <div class="flex items-baseline gap-2">
          <span class="text-4xl font-bold font-['Roboto'] text-[#0081A7]">₱{{ price.toLocaleString() }}</span>
          <span class="text-lg text-neutral-600">/day</span>
        </div>
        <CardDescription class="text-neutral-600">
          Best price guarantee
        </CardDescription>
      </CardHeader>
      
      <CardContent class="pt-6 space-y-6">
        <!-- Interactive Calendar -->
        <div class="border-2 border-[#0081A7]/20 rounded-lg p-4 bg-white">
          <!-- Calendar Header -->
          <div class="flex items-center justify-between mb-4">
            <button 
              @click="previousMonth"
              class="p-2 text-[#0081A7] hover:bg-[#0081A7]/10 rounded-lg transition-colors"
            >
              <ChevronLeft class="w-5 h-5" />
            </button>
            <h3 class="font-semibold text-[#0081A7] text-lg">
              {{ monthNames[currentMonth.getMonth()] }} {{ currentMonth.getFullYear() }}
            </h3>
            <button 
              @click="nextMonth"
              class="p-2 text-[#0081A7] hover:bg-[#0081A7]/10 rounded-lg transition-colors"
            >
              <ChevronRight class="w-5 h-5" />
            </button>
          </div>

          <!-- Day Names -->
          <div class="grid grid-cols-7 gap-1 mb-2">
            <div 
              v-for="day in dayNames" 
              :key="day" 
              class="text-center text-xs font-medium text-neutral-600 py-1"
            >
              {{ day }}
            </div>
          </div>

          <!-- Calendar Days -->
          <div class="grid grid-cols-7 gap-1">
            <button
              v-for="(day, idx) in calendarDays"
              :key="idx"
              @click="day.date && handleDateClick(day.date, day.disabled)"
              :disabled="!day.day || day.disabled"
              :class="[
                'aspect-square p-1 text-neutral-900 text-sm rounded-lg transition-all font-medium relative',
                !day.day ? 'invisible' : '',
                day.booked ? 'bg-red-50 text-red-700 cursor-not-allowed border border-red-200' : '',
                day.isCoding && !day.booked ? 'bg-amber-100 text-amber-800 border-2 border-amber-400' : '',
                day.isPast && !day.booked && !day.isCoding ? 'bg-neutral-50 text-neutral-400 cursor-not-allowed' : '',
                day.date === selectedPickupDate || day.date === selectedReturnDate ? 'bg-[#0081A7] text-white font-bold ring-2 ring-[#0081A7] ring-offset-2 shadow-md' : '',
                day.date && isDateInRange(day.date) && !day.booked ? 'bg-[#00AFB9]/20 text-[#0081A7] border border-[#00AFB9]/30' : '',
                day.date && !day.disabled && day.date !== selectedPickupDate && day.date !== selectedReturnDate && !isDateInRange(day.date) ? 'hover:bg-[#0081A7]/10 hover:ring-2 hover:ring-[#0081A7]/20 cursor-pointer' : ''
              ]"
            >
              <div v-if="day.booked" class="absolute inset-0 flex items-center justify-center">
                <div class="w-0.5 h-full bg-red-500 rotate-45"></div>
              </div>
              <div v-if="day.isCoding && !day.booked" class="absolute top-1 right-1">
                <div class="w-1.5 h-1.5 bg-amber-500 rounded-full"></div>
              </div>
              <span :class="(day.booked || day.isCoding) ? 'relative z-10' : ''">{{ day.day }}</span>
            </button>
          </div>

          <!-- Legend -->
          <div class="mt-4 pt-4 border-t-2 border-[#0081A7]/20 grid grid-cols-2 gap-3 text-xs">
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-[#0081A7] rounded ring-2 ring-[#0081A7] ring-offset-1"></div>
              <span class="text-neutral-700">Selected</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-[#00AFB9]/20 rounded border border-[#00AFB9]/30"></div>
              <span class="text-neutral-700">In Range</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-red-50 border border-red-200 rounded relative">
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-0.5 h-full bg-red-500 rotate-45"></div>
                </div>
              </div>
              <span class="text-neutral-700">Booked</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-amber-100 border-2 border-amber-400 rounded relative">
                <div class="absolute top-0.5 right-0.5">
                  <div class="w-1.5 h-1.5 bg-amber-500 rounded-full"></div>
                </div>
              </div>
              <span class="text-neutral-700">Coding Day</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-neutral-50 rounded"></div>
              <span class="text-neutral-700">Past</span>
            </div>
          </div>    
        </div>

        <!-- Selected Dates Display -->
        <div 
          v-if="selectedPickupDate || selectedReturnDate" 
          class="p-4 bg-[#0081A7]/5 rounded-lg border-2 border-[#0081A7]/20"
        >
          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-neutral-600">Pickup:</span>
              <span class="font-semibold text-[#0081A7]">
                {{ selectedPickupDate ? formatDate(selectedPickupDate) : 'Not selected' }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-neutral-600">Return:</span>
              <span class="font-semibold text-[#0081A7]">
                {{ selectedReturnDate ? formatDate(selectedReturnDate) : 'Not selected' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Active Bookings -->
        <div v-if="activeBookings.length > 0" class="pt-4 border-t-2 border-[#0081A7]/20">
          <h4 class="text-sm font-semibold text-neutral-900 mb-3 flex items-center gap-2">
            <AlertCircle class="w-4 h-4 text-red-500" />
            Unavailable Dates
          </h4>
          <div class="space-y-2 max-h-32 overflow-y-auto">
            <div 
              v-for="booking in activeBookings" 
              :key="booking.id" 
              class="p-2 bg-red-50 border-2 border-red-200 rounded text-xs"
            >
              <div class="flex items-center justify-between gap-2">
                <span class="font-medium text-red-800 truncate">
                  {{ formatDate(booking.start_date) }} - {{ formatDate(booking.end_date) }}
                </span>
                <span class="text-red-600 text-[10px] font-semibold uppercase">
                  {{ booking.status }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Price Breakdown -->
        <div v-if="totalDays > 0" class="pt-4 border-t-2 border-[#0081A7]/20 space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-neutral-600">₱{{ price.toLocaleString() }} × {{ totalDays }} day{{ totalDays > 1 ? 's' : '' }}</span>
            <span class="font-semibold text-neutral-900">₱{{ totalPrice.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-neutral-600">Service fee (5%)</span>
            <span class="font-semibold text-neutral-900">₱{{ serviceFee.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between pt-2 border-t-2 border-[#0081A7]/20 font-bold text-lg">
            <span class="text-neutral-900">Total</span>
            <span class="text-[#0081A7]">₱{{ grandTotal.toLocaleString() }}</span>
          </div>
        </div>
      </CardContent>
      
      <CardFooter class="flex flex-col gap-2 bg-white border-t border-neutral-200">
        <button
          @click="handleBooking"
          :disabled="!canBook"
          class="w-full py-3 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white rounded-lg font-semibold hover:shadow-lg hover:from-[#0081A7]/90 hover:to-[#00AFB9]/90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-['Roboto']"
        >
          {{ canBook ? 'Book Now' : 'Select Dates on Calendar' }}
        </button>
        <p class="text-xs text-center text-neutral-600">
          You won't be charged yet
        </p>
      </CardFooter>
    </Card>

    <!-- Quick Info Card -->
    <Card class="mt-4 bg-blue-50 border-2 border-blue-200">
      <CardContent class="pt-6">
        <div class="space-y-3 text-sm">
          <div class="flex items-center gap-2 text-neutral-700">
            <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
            <span>Free cancellation within 24 hours</span>
          </div>
          <div class="flex items-center gap-2 text-neutral-700">
            <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
            <span>Instant booking confirmation</span>
          </div>
          <div class="flex items-center gap-2 text-neutral-700">
            <CheckCircle class="w-4 h-4 text-[#00AFB9]" />
            <span>24/7 customer support</span>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>

<style>
* {
  font-family: 'Roboto', sans-serif;
} 

/* Style date and time input calendar icons */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
  filter: invert(50%) sepia(100%) saturate(500%) hue-rotate(160deg);
  cursor: pointer;
  opacity: 1 !important;
  inline-size: 20px;
} 

input[type="date"]::-webkit-calendar-picker-indicator:hover,
input[type="time"]::-webkit-calendar-picker-indicator:hover {
  filter: invert(50%) sepia(100%) saturate(600%) hue-rotate(160deg);
}
 
input[type="date"],
input[type="time"] {
  color-scheme: light;
}
 
input[type="date"]::-webkit-inner-spin-button,
input[type="date"]::-webkit-clear-button {
  display: none;
}

input[type="time"]::-webkit-inner-spin-button,
input[type="time"]::-webkit-clear-button {
  display: none;
}
</style> 