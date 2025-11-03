<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import adminItems from '@/data/sidebar/admin.json';

import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Icons from '@/Shared/Icons.vue';

const emit = defineEmits(['toggleSidebar'])
const dropdownOpen = ref(false)
const logout = () => {
    router.post(route('logout'));
};

function getHeaderName() {
  const page = usePage();
  const url = page.url;
  console.log('page', url);

  if (url.includes('/admin/departments')) return 'Departments';
  if (url.includes('/admin/users') && url.includes('role=user')) return 'All Users';
  if (url.includes('/admin/users') && url.includes('role=inspector')) return 'Inspectors';
  if (url.includes('/admin/users') && url.includes('role=sub-admin')) return 'Department Admins';
  if (url.includes('/admin/users/sub-admin-types')) return 'Admin Types';
  if (url.includes('/admin/users/user-types')) return 'User Types';
  if (url.includes('/admin/users/roles')) return 'Roles';

  return 'Dashboard';
}

</script>

<template>
  <header class="flex items-center justify-between flex-wrap bg-white h-16 px-4 sm:px-6 lg:px-8 shadow-md w-full relative overflow-visible">
    <!-- Sidebar Toggle (Mobile) -->
    <button @click="$emit('toggleSidebar')" class="text-gray-600 md:hidden">
      <Icons name="hamburger" class="w-6 h-6 text-primary" />
    </button>

    <!-- Page Title -->
    <h1 class="text-base sm:text-lg font-bold text-primary uppercase truncate max-w-xs sm:max-w-sm lg:max-w-md">
      <!-- {{ getHeaderName() }} -->
    </h1>

    <!-- Profile Dropdown -->
    <div class="relative ml-auto">
      <button @click="dropdownOpen = !dropdownOpen" class="focus:outline-none">
        <Icons name="profile" class="w-6 h-6 text-primary" />
      </button>

      <div
        v-if="dropdownOpen"
        @click.away="dropdownOpen = false"
        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50"
      >
        <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
          Profile
        </ResponsiveNavLink>

        <ResponsiveNavLink @click="logout">
          Log Out
        </ResponsiveNavLink>
      </div>
    </div>
  </header>
</template>


