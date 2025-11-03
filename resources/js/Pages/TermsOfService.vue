<script setup>
import Logo from '@/Shared/Logo.vue'

const props = defineProps({
  onAccept: Function,   // called when Accept is clicked
  onDecline: Function,  // called when Decline is clicked
})
</script>

<template>
  <div class="flex flex-col items-center justify-center p-6 text-gray-700 overflow-y-auto">
    <!-- Logo -->
    <div class="flex justify-center mb-6">
      <Logo class="w-24 h-24 object-contain" />
    </div>

    <!-- Title -->
    <h1 class="text-2xl font-bold text-primary mb-4">Terms of Service</h1>

    <!-- Content -->
    <p class="text-gray-700 mb-4 text-sm">
      By using this application, you agree to the following terms and conditions:
    </p>

    <ul class="list-disc list-inside text-gray-600 mb-4 text-sm space-y-1">
      <li>You must be at least 18 years old to use this application.</li>
      <li>You agree to provide accurate and complete information during registration.</li>
      <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
      <li>You agree not to use the application for any illegal or unauthorized purposes.</li>
      <li>We reserve the right to modify or terminate the application at any time without notice.</li>
      <li>We are not liable for any damages or losses resulting from your use of the application.</li>
    </ul>

    <p class="text-gray-600 mb-4 text-sm">
      If you do not agree to these terms, please do not use the application.
    </p>

    <p class="text-gray-600 mb-4 text-sm">
      For more information, please contact us at
      <a href="mailto:marketchristian7@gmail.com" class="text-blue-500 underline">
        marketchristian7@gmail.com
      </a>.
    </p>

    <p class="text-gray-600 mb-4 text-sm">
      By clicking “Accept”, you agree to the terms and conditions outlined above.
    </p>

    <!-- Buttons -->
    <div class="mt-4 flex flex-col md:flex-row gap-4">
      <button
        @click="onAccept && onAccept()"
        class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-primary-700 transition-colors"
      >
        Accept
      </button>
      <button
        @click="onDecline && onDecline()"
        class="bg-secondary text-white font-bold py-2 px-4 rounded hover:bg-secondary-700 transition-colors"
      >
        Decline
      </button>
    </div>
  </div>
</template>
