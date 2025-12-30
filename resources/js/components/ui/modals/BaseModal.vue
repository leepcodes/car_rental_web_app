<!-- BaseModal.vue -->
<script setup lang="ts">
interface Props {
  isOpen: boolean
  userId?: string | number | null
}

withDefaults(defineProps<Props>(), {
  userId: null
})

const emit = defineEmits<{
  close: []
}>()

const closeModal = (): void => {
  emit('close')
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0   flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.5);" @click="closeModal">
    
    <div class="bg-white rounded-lg w-11/12 max-w-2xl max-h-[90vh] overflow-y-auto shadow-xl">
      <div class="flex items-center justify-between p-5 border-b border-gray-200">
        <slot name="header">
          <h2 class="text-2xl font-semibold">Modal</h2>
        </slot>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-3xl leading-none w-8 h-8">&times;</button>
      </div>
      
      <div class="p-5">
        <slot :userId="userId"></slot>
      </div>
     
    </div>
  </div>
</template>