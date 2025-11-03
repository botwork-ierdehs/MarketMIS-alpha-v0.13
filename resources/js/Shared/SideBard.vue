<template>
  <div class="flex min-h-screen bg-gray-100 ">
    <!-- Sidebar -->
    <div
      :class="[
        'fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-md transform transition-transform duration-300 ease-in-out',
        { '-translate-x-full': !isOpen, 'translate-x-0': isOpen },
        'md:translate-x-0 md:static md:shadow-none'
      ]"
    >
      <nav class="p-4 space-y-2">
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200">Dashboard</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200">Profile</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200">Settings</a>
        <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200">Logout</a>
      </nav>
    </div>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Top bar -->
      <header class="bg-white shadow-md p-4 flex items-center md:hidden">
        <button @click="isOpen = !isOpen" class="text-gray-600 hover:text-black focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <div class="flex justify-center items-center">
            <Logo class="fill-white" style="width: 130px; height: 55px;" />
        </div>

      </header>

      <!-- Page content -->
      <main class="p-6">
        <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
        <p>This is your main content area. Resize to test responsiveness.</p>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Logo from './Logo.vue';

const isOpen = ref(false)
</script>
    