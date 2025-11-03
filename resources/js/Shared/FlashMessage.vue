<!-- resources/js/Components/FlashMessage.vue -->
<template>
  <div v-if="visible" :class="['fixed top-5 right-5 z-50 px-4 py-2 rounded shadow-lg text-white', typeClass]" transition>
    {{ message }}
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';

const props = defineProps({
  message: String,
  type: {
    type: String,
    default: 'success' // or 'error'
  },
  duration: {
    type: Number,
    default: 3000
  }
});

const visible = ref(true);

const typeClass = computed(() => {
  return props.type === 'success' ? 'bg-green-500' : 'bg-red-500';
});

watch(() => props.message, () => {
  if (props.message) {
    visible.value = true;
    setTimeout(() => {
      visible.value = false;
    }, props.duration);
  }
});

onMounted(() => {
  setTimeout(() => {
    visible.value = false;
  }, props.duration);
});
</script>


