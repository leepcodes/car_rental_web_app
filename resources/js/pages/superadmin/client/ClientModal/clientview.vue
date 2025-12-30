<script setup lang="ts">
import BaseModal from '@/components/ui/modals/BaseModal.vue'

interface Props {
  isOpen: boolean
  userId?: number | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
}>()

</script>

<template>
  <BaseModal :isOpen="isOpen" :userId="userId" @close="emit('close')">
    <template #header>
      <h2>Client Details</h2>
    </template>

    <div>
      <p>User ID: {{ userId }}</p>
      
    </div>
  </BaseModal>
</template>