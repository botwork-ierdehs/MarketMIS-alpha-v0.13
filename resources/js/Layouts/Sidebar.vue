<script setup>
import { ref } from 'vue'
import { Link, usePage} from '@inertiajs/vue3';

import adminItems from '@/data/sidebar/admin.json';
import userItem from '@/data/sidebar/users.json';
import subItem from '@/data/sidebar/sub-admin.json';
import { ADMIN_TYPE, ROLE_ID } from '@/data/data.js';

import Logo from '@/Shared/Logo.vue';
import Icons from '@/Shared/Icons.vue';

const page = usePage();

const props = defineProps({
  open: Boolean,

})

defineEmits(['close'])

const openSections = ref({})
const toggleSection = (label) => {
  openSections.value[label] = !openSections.value[label]
}

function getItems(data) {
  const { role_id, sub_admin_type_id } = data;
  let displayItems;
  switch (role_id) {
    case ROLE_ID.SUB_ADMIN:
      displayItems = subItem;
      break;
    case ROLE_ID.USER:
      displayItems = userItem;
      break;
    default:
      displayItems = adminItems;
      break;
  }
  return displayItems;
}

function getUserDetails() {
  return page.props.auth.user;
}
</script>

<template>
  <aside
    :class="[
      'fixed md:static inset-y-0 left-0 z-40 bg-white shadow-lg transform transition-transform duration-200 ease-in-out',
      'min-h-screen w-64 sm:w-56 md:w-60',
      { '-translate-x-full': !open, 'translate-x-0': open },
      'md:translate-x-0',
    ]"
  >
    <!-- Logo -->
    <div class="p-4 flex items-center justify-center">
      <Logo class="fill-current text-gray-800" style="width: 120px; height: 50px;" />
    </div>

    <!-- Sidebar Navigation -->
    <nav class="p-4 space-y-1 overflow-y-auto max-h-[calc(100vh-100px)]">
      <template v-for="(item, i) in getItems(getUserDetails())" :key="i">
        <!-- With Children -->
        <div v-if="item.children" class="space-y-1">
          <button
            @click="toggleSection(item.label)"
            class="flex items-center justify-between w-full px-3 py-2 text-primary rounded-md hover:bg-primary/60 hover:text-white text-sm transition"
            :class="[
                'flex items-center gap-2 px-2 py-1 rounded text-primary hover:bg-primary/60 hover:text-white transition text-sm',
                { 
                  'bg-primary/60 text-white': $page.url === item.link || $page.url.startsWith(item.link + '/')
                }
                ]"
          >
            <div class="flex items-center gap-2">
              <Icons :is="item.icon" :name="item.icon" class="w-5 h-5 shrink-0" />
              <span class="truncate">{{ item.label }}</span>
            </div>
            <svg
              class="w-4 h-4 transition-transform"
              :class="{ 'rotate-90': openSections[item.label] }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <!-- Nested Links -->
          <div v-if="openSections[item.label]" class="ml-6 space-y-1">
            <Link
              v-for="(child, j) in item.children"
              :key="j"
                :href="child.link"
                :class="[
                'flex items-center gap-2 px-2 py-1 rounded text-primary hover:bg-primary/60 hover:text-white transition text-sm',
                { 
                  'bg-primary/60 text-white': $page.url === child.link || $page.url.startsWith(child.link + '/')
                }
                ]"
              class="flex items-center gap-2 px-2 py-1 rounded text-primary hover:bg-primary/60 hover:text-white transition text-sm"
            >
              <Icons :is="child.icon" :name="child.icon" class="w-4 h-4 shrink-0" />
              <span class="truncate">{{ child.label }}</span>
            </Link>
          </div>
        </div>

        <!-- Without Children -->
        <div v-else>
          <Link
            :href="item.link"
            class="flex items-center gap-3 px-3 py-2 rounded-md text-primary hover:bg-primary/60 hover:text-white transition text-sm"
            :class="[
                'flex items-center gap-2 px-2 py-1 rounded text-primary hover:bg-primary/60 hover:text-white transition text-sm',
                { 
                  'bg-primary/60 text-white': $page.url === item.link || $page.url.startsWith(item.link + '/')
                }
                ]"
          >
            <Icons :is="item.icon" :name="item.icon" class="w-5 h-5 shrink-0" />
            <span class="truncate">{{ item.label }}</span>
          </Link>
        </div>
      </template>
    </nav>
  </aside>
</template>

