<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
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

const selectedPickupDate = ref('');
const selectedReturnDate = ref('');
const currentMonth = ref(new Date());

const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// Get active bookings
const activeBookings = computed(() => 
  props.bookedDates.filter(b => ['pending', 'confirmed', 'ongoing'].includes(b.status))
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
      disabled: isPast || isBooked || isCoding,
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
      // Check if any date in range is booked or coding day
      let hasBlockedInRange = false;
      for (let d = new Date(pickupDate); d <= clickedDate; d.setDate(d.getDate() + 1)) {
        const checkDateStr = d.toISOString().split('T')[0];
        if (isDateBooked(checkDateStr) || isCodingDay(checkDateStr)) {
          hasBlockedInRange = true;
          break;
        }
      }
      
      if (hasBlockedInRange) {
        alert('Cannot select date range that includes booked dates or coding days');
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
    <Card class="border-2 border-neutral-600 shadow-xl bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 overflow-hidden">
      <CardHeader class="bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900 text-white">
        <div class="flex items-baseline gap-2">
          <span class="text-4xl font-bold font-['Roboto']">₱{{ price.toLocaleString() }}</span>
          <span class="text-lg">/day</span>
        </div>
        <CardDescription class="text-white/90">
          Best price guarantee
        </CardDescription>
      </CardHeader>
      
      <CardContent class="pt-6 space-y-6">
        <!-- Interactive Calendar -->
        <div class="border rounded-lg p-4 bg-white">
          <!-- Calendar Header -->
          <div class="flex items-center justify-between mb-4">
            <button 
              @click="previousMonth"
class="p-2 text-blue-500 hover:text-blue-300 rounded-lg transition-colors"
            >
              <ChevronLeft class="w-5 h-5" />
            </button>
            <h3 class="font-semibold text-cyan-500 text-lg">
              {{ monthNames[currentMonth.getMonth()] }} {{ currentMonth.getFullYear() }}
            </h3>
            <button 
              @click="nextMonth"
              class="p-2 text-blue-500 hover:text-blue-300 rounded-lg transition-colors"
            >
              <ChevronRight class="w-5 h-5" />
            </button>
          </div>

          <!-- Day Names -->
          <div class="grid grid-cols-7 gap-1 mb-2">
            <div 
              v-for="day in dayNames" 
              :key="day" 
              class="text-center text-xs font-medium text-neutral-500 py-1"
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
                'aspect-square p-1 text-neutral-700 text-sm rounded-lg transition-all font-medium relative',
                !day.day ? 'invisible' : '',
                day.booked ? 'bg-red-100 text-red-700 cursor-not-allowed' : '',
                day.isCoding && !day.booked ? 'bg-orange-100 text-orange-700 cursor-not-allowed' : '',
                day.isPast && !day.booked && !day.isCoding ? 'bg-neutral-100 text-neutral-400 cursor-not-allowed' : '',
                day.date === selectedPickupDate || day.date === selectedReturnDate ? 'bg-[#0081A7] text-white font-bold ring-2 ring-[#0081A7] ring-offset-2' : '',
                day.date && isDateInRange(day.date) && !day.booked && !day.isCoding ? 'bg-[#00AFB9]/20 text-[#0081A7]' : '',
                day.date && !day.disabled && day.date !== selectedPickupDate && day.date !== selectedReturnDate && !isDateInRange(day.date) ? 'hover:bg-neutral-300 hover:ring-2 hover:ring-neutral-300' : ''
              ]"
            >
              <div v-if="day.booked" class="absolute inset-0 flex items-center justify-center">
                <div class="w-0.5 h-full bg-red-500 rotate-45"></div>
              </div>
              <div v-if="day.isCoding && !day.booked" class="absolute inset-0 flex items-center justify-center">
                <div class="w-0.5 h-full bg-orange-500 rotate-45"></div>
              </div>
              <span :class="(day.booked || day.isCoding) ? 'relative z-10' : ''">{{ day.day }}</span>
            </button>
          </div>

          <!-- Legend -->
          <div class="mt-4 pt-4 border-t grid grid-cols-2 gap-3 text-xs">
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-[#0081A7] rounded ring-2 ring-[#0081A7] ring-offset-1"></div>
              <span class="text-[#0081A7]">Selected</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-[#00AFB9]/20 rounded"></div>
              <span class="text-[#00AFB9]">In Range</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-red-100 rounded relative">
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-0.5 h-full bg-red-500 rotate-45"></div>
                </div>
              </div>
              <span class="text-red-500">Booked</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-orange-100 rounded relative">
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-0.5 h-full bg-orange-500 rotate-45"></div>
                </div>
              </div>
              <span class="text-orange-800">Coding Day</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 bg-neutral-300 rounded"></div>
              <span class="text-neutral-800">Past</span>
            </div>
          </div>    
        </div>

        <!-- Selected Dates Display -->
        <div 
          v-if="selectedPickupDate || selectedReturnDate" 
          class="p-4 bg-[#0081A7]/5 rounded-lg border border-[#0081A7]/20"
        >
          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-neutral-100">Pickup:</span>
              <span class="font-semibold text-[#0081A7]">
                {{ selectedPickupDate ? formatDate(selectedPickupDate) : 'Not selected' }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-neutral-100">Return:</span>
              <span class="font-semibold text-[#0081A7]">
                {{ selectedReturnDate ? formatDate(selectedReturnDate) : 'Not selected' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Active Bookings -->
        <div v-if="activeBookings.length > 0" class="pt-4 border-t">
          <h4 class="text-sm font-semibold text-neutral-700 mb-3 flex items-center gap-2">
            <AlertCircle class="w-4 h-4 text-red-500" />
            Unavailable Dates
          </h4>
          <div class="space-y-2 max-h-32 overflow-y-auto">
            <div 
              v-for="booking in activeBookings" 
              :key="booking.id" 
              class="p-2 bg-red-50 border border-red-200 rounded text-xs"
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
        <div v-if="totalDays > 0" class="pt-4 border-t border-white/20 space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-white/80">₱{{ price.toLocaleString() }} × {{ totalDays }} day{{ totalDays > 1 ? 's' : '' }}</span>
            <span class="font-semibold text-white">₱{{ totalPrice.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-white/80">Service fee</span>
            <span class="font-semibold text-white">₱{{ serviceFee.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between pt-2 border-t border-white/20 font-bold text-lg">
            <span class="text-white">Total</span>
            <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">₱{{ grandTotal.toLocaleString() }}</span>
          </div>
        </div>
      </CardContent>
      
      <CardFooter class="flex flex-col gap-2 bg-gradient-to-br from-neutral-600 via-neutral-700 to-neutral-900">
        <button
          @click="handleBooking"
          :disabled="!canBook"
          class="w-full py-3 bg-gradient-to-r from-[#0081A7] to-[#00AFB9] text-white rounded-lg font-semibold hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed font-['Roboto']"
        >
          {{ canBook ? 'Book Now' : 'Select Dates on Calendar' }}
        </button>
        <p class="text-xs text-center text-white/70">
          You won't be charged yet
        </p>
      </CardFooter>
    </Card>

    <!-- Quick Info Card -->
    <Card class="mt-4 bg-neutral-50">
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

<style> {
  font-family: 'Roboto', sans-serif;
} 
Style date and time input calendar icons to white 
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
  filter: invert(100%) brightness(100%);
  cursor: pointer;
  opacity: 1 !important;
  inline-size: 20px;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover,
input[type="time"]::-webkit-calendar-picker-indicator:hover {
  filter: invert(100%) brightness(120%);
}


input[type="date"],
input[type="time"] {
  color-scheme: dark;
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
