<script setup>
import { ref } from "vue";

// Props
const props = defineProps({
  datas: {
    type: Array,
    required: true
  },
  disabled: {
    type: Array,
    required: true
  }
});

// Emit selection
const emit = defineEmits(["select"]);

const selectedId = ref(null);

const getStatusColor = (status) => {
  switch (status) {
    case 1: return "bg-green-400";   // Occupied
    case 4: return "bg-red-400";     // Under Maintenance
    case 3: return "bg-yellow-400";  // Reserved
    default: return "bg-blue-300";   // Vacant
  }
};

const handleSelect = (stall) => {
    console.log(props)
  if (props.disabled) return; 
  selectedId.value = stall.id;
  emit("select", stall);
};
</script>

<template>
    <div class="p-6">
      <!-- Legend -->
      <div class="flex flex-wrap gap-4 mb-6 text-xs sm:text-sm">
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 rounded bg-blue-300"></span>
          Vacant
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 rounded bg-green-400"></span>
          Occupied
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 rounded bg-yellow-400"></span>
          Reserved
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 rounded bg-red-400"></span>
          Under Maintenance
        </div>
      </div>
      <div
        class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-6 gap-5 p-6 place-items-center"
        :class="props.disabled ? 'opacity-50 pointer-events-none cursor-not-allowed' : ''"
      >
        <div
          v-for="data in datas"
          :key="data.id"
          @click="handleSelect(data)"
          class="relative group flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-lg cursor-pointer shadow transition text-center"
          :class="[
            getStatusColor(data.status),
            selectedId === data.id ? 'ring-2 ring-offset-2 ring-blue-500' : ''
          ]"
        >
          <!-- Stall name centered -->
          <span class="text-[10px] sm:text-xs font-bold text-white leading-tight text-center break-words">
            {{ data.name }}
          </span>

          <!-- Hover tooltip -->
          <div
            class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block z-10 w-44 p-2 text-xs bg-gray-900 text-white rounded-lg shadow-lg"
          >
            <p class="font-semibold">{{ data.name }}</p>
            <p>{{ data.categories?.name }}</p>
            <p>Status: {{ data.status }}</p>
            <p v-if="data.categories?.total_payment">₱{{ data.categories?.total_payment }}</p>
          </div>
        </div>
      </div>
  </div>
</template>
