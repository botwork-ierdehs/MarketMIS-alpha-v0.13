<script setup>
import { ref } from 'vue'
import Sidebar from './Sidebar.vue'
import Header from './Header.vue'
import Footer from '@/Components/Footer.vue';

const sidebarOpen = ref(false);
</script>

<template>
  <div class="flex min-h-screen bg-gray-100 overflow-hidden">
    <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Mobile backdrop -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 z-30 bg-black/40 md:hidden"
    ></div>

      <div class="flex flex-col flex-1 min-w-0 transition-all duration-200">
        <Header @toggleSidebar="sidebarOpen = !sidebarOpen" />

        <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8 w-full">
          <slot />
        </main>
      </div>

  </div>
  <Footer />
</template>
