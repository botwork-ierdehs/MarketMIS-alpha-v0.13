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
    <h1 class="text-2xl font-bold text-primary mb-4">Privacy Policy</h1>

    <!-- Content -->
    <p class="text-gray-700 mb-4 text-sm">
      We value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and safeguard your data.
    </p>
    <ul class="list-disc list-inside text-gray-600 mb-4 text-sm space-y-1">
      <li>We collect personal information such as your name, email address, and contact details when you register or use our services.</li>
      <li>Your data is used to provide and improve our services, communicate with you, and comply with legal obligations.</li>
      <li>We implement appropriate security measures to protect your personal information from unauthorized access, disclosure, or misuse.</li>
      <li>We do not share your personal information with third parties without your consent, except as required by law or to provide our services.</li>
      <li>You have the right to access, correct, or delete your personal information at any time.</li>
      <li>We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated effective date.</li>
    </ul>

    <p class="text-gray-600 mb-4 text-sm">
      If you have any questions or concerns about our Privacy Policy, please contact us at
      <a href="mailto:marketchristian7@gmail.com" class="text-blue-500 underline">
        marketchristian7@gmail.com
      </a>.
    </p>

    <p class="text-gray-600 mb-4 text-sm">
      By using our services, you consent to the collection and use of your personal information as described in this Privacy Policy.
    </p>

    <!-- Actions -->
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
