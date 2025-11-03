<script setup>
import { ref } from 'vue'
import Logo from '@/Shared/Logo.vue'

// props: allow parent to control modal visibility and update form state
const props = defineProps({
  onAccept: Function,   // callback when accepted
  onDecline: Function,  // callback when declined
})
</script>

<template>
  <div class="flex flex-col items-center justify-center p-6 text-gray-700 overflow-y-auto">
    <!-- Logo -->
    <div class="flex justify-center mb-6">
      <Logo class="w-24 h-24 object-contain" />
    </div>

    <!-- Title -->
    <h1 class="text-2xl font-bold text-primary mb-4 text-left">Welcome! You're almost there!</h1>

    <!-- Content -->
    <p class="text-gray-700 mb-4 text-sm">
      Thank you for registering with our application. To complete your registration, please check your email for a verification link. Click on the link to verify your email address and activate your account.
    </p>

    <p class="text-gray-700 mb-4 text-sm">
      If you did not receive the email, please check your spam or junk folder. 
    </p>
   
    <!-- Actions -->
    <div class="mt-4 flex flex-col md:flex-row gap-4">
      <button
        @click="onAccept && onAccept()"
        class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-primary-700 transition-colors"
      >
        Done
      </button>
    </div>
  </div>
</template>
