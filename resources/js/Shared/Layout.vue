<template>
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div
      :class="[
        'fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-md transform transition-transform duration-300 ease-in-out',
        { '-translate-x-full': !isOpen, 'translate-x-0': isOpen },
        'md:translate-x-0 md:static md:shadow-none'
      ]"
    >
      <div class="p-4 font-bold text-xl border-b">My App</div>
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
        <span class="ml-4 font-semibold text-lg">My App</span>
      </header>

      <!-- Page content -->
      <main class="p-6">
      	<slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const isOpen = ref(false)
</script>
