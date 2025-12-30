<script setup lang="ts">
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import Button from '@/components/ui/button/Button.vue'
import OperatorView from '@/pages/superadmin/operator/OperatorModal/operatorview.vue'
import { Trash, X } from 'lucide-vue-next'

import {
  Table,
  TableHeader,
  TableBody,
  TableRow,
  TableCell,
  TableHead,
} from '@/components/ui/table';

interface User {
  id: number;
  name: string;
  email: string;
  is_verified: boolean;
}

const props = defineProps<{ users: User[] }>();

const isModalOpen = ref(false)
const selectedUserId = ref<number | null>(null)

// var sa delete
const localUsers = ref<User[]>([...props.users])
const deletedUser = ref<{ user: User; index: number } | null>(null)
const showUndo = ref(false)
const timerProgress = ref(100)
let deleteTimer: ReturnType<typeof setTimeout> | null = null
let progressInterval: ReturnType<typeof setInterval> | null = null

const openModal = (userId: number): void => {
  selectedUserId.value = userId
  isModalOpen.value = true
}

const closeModal = (): void => {
  isModalOpen.value = false
  selectedUserId.value = null
}
//Delete sa UI lang 
const handleDelete = (userId: number): void => {
  const userIndex = localUsers.value.findIndex(u => u.id === userId)
  if (userIndex !== -1) {
    const user = localUsers.value[userIndex]
    deletedUser.value = { user, index: userIndex }
    localUsers.value.splice(userIndex, 1)
    showUndo.value = true
    timerProgress.value = 100

   
    const duration = 5000 // 5s
    const interval = 50 
    const decrement = (interval / duration) * 100

    progressInterval = setInterval(() => {
      timerProgress.value -= decrement
      if (timerProgress.value <= 0) {
        timerProgress.value = 0
        if (progressInterval) clearInterval(progressInterval)
      }
    }, interval)

    //Delete na sa DB
    deleteTimer = setTimeout(() => {
      deleteFromDatabase(userId)
      showUndo.value = false
      deletedUser.value = null
      if (progressInterval) clearInterval(progressInterval)
    }, duration)
  }
}

const handleUndo = (): void => {
  if (deleteTimer) {
    clearTimeout(deleteTimer)
    deleteTimer = null
  }

  if (progressInterval) {
    clearInterval(progressInterval)
    progressInterval = null
  }

  //Cancel
  if (deletedUser.value) {
    localUsers.value.splice(deletedUser.value.index, 0, deletedUser.value.user)
    deletedUser.value = null
  }

  showUndo.value = false
  timerProgress.value = 100
}

const deleteFromDatabase = (userId: number): void => {
  router.delete(`/superadmin/operators/${userId}`, {
    preserveScroll: false,  
    onSuccess: () => {
      console.log('Operator deleted successfully')
    },
    onError: (errors) => {
      console.error('Failed to delete operator:', errors)
      handleUndo()
    }
  })
}

// Reset timer
watch(() => showUndo.value, (newVal) => {
  if (!newVal) {
    if (deleteTimer) clearTimeout(deleteTimer)
    if (progressInterval) clearInterval(progressInterval)
  }
})
</script>

<template>
  <AppSidebarLayout>
    <template #default>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Name</TableHead>
            <TableHead>Email</TableHead>
            <TableHead>Verified</TableHead>
            <TableHead>Action</TableHead>
          </TableRow>
        </TableHeader>

        <TableBody>
          <TableRow v-for="user in localUsers" :key="user.id">
            <TableCell>{{ user.name }}</TableCell>
            <TableCell>{{ user.email }}</TableCell>
            <TableCell>{{ user.is_verified ? 'Verified' : 'Not Verified' }}</TableCell>
            <TableCell class="flex items-center gap-2">
              <Button @click="openModal(user.id)">VIEW</Button>
              <Button @click="handleDelete(user.id)" variant="destructive">
                <Trash class="w-4 h-4" />
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <OperatorView :isOpen="isModalOpen" :userId="selectedUserId" @close="closeModal" />

   
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-full opacity-0"
      >
        <div
          v-if="showUndo" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-2xl flex items-center gap-4 z-50 min-w-[320px]">
        
          <div class="relative w-10 h-10 flex-shrink-0">
            <svg class="w-10 h-10 -rotate-90" viewBox="0 0 36 36">
              <!-- bilog -->
              <circle cx="18" cy="18" r="16" fill="none" class="stroke-gray-700" stroke-width="3"/>
              <!-- timer na bilog -->
              <circle cx="18" cy="18" r="16" fill="none" class="stroke-blue-500 transition-all duration-50" stroke-width="3" :stroke-dasharray="`${timerProgress} 100`" stroke-linecap="round"/>
            </svg>
        
            <div class="absolute inset-0 flex items-center justify-center text-xs font-bold">
              {{ Math.ceil(timerProgress / 20) }}
            </div>
          </div>

          <span class="flex-1">Operator deleted</span>
          
          <Button @click="handleUndo" variant="ghost" size="sm" class="text-white hover:text-gray-200 hover:bg-gray-800 font-semibold">
            UNDO
          </Button>
          
          <button @click="handleUndo" class="text-gray-400 hover:text-white transition-colors">
            <X class="w-4 h-4" />
          </button>
        </div>
      </Transition>
    </template>
  </AppSidebarLayout>
</template>