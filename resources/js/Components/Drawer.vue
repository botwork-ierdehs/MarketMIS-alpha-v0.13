<template>
  <transition name="drawer">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-stretch"
    >
      <!-- Overlay -->
      <div
        class="absolute inset-0 bg-black/50 z-0"
        @click="$emit('update:modelValue', false)"
      ></div>

      <!-- Drawer Panel -->
      <div class="relative ml-auto w-full sm:w-3/4 md:w-1/2 h-full bg-white z-10 shadow-xl overflow-y-auto transform transition-transform duration-300 ease-in-out">

      <div class="flex items-center justify-between p-4 border-b">
          <h2 class="text-lg font-semibold text-primary">{{ title }}</h2>
          <button @click="$emit('update:modelValue', false)" class="text-primary hover:text-secondary">
            ✕
          </button>
        </div>

        <div class="p-4">
          <slot />
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: 'Drawer'
  }
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.drawer-enter-active,
.drawer-leave-active {
  transition: transform 0.3s ease;
}
.drawer-enter-from,
.drawer-leave-to {
  transform: translateX(100%);
}
</style>
