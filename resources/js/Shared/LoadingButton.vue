<template>
  <button :disabled="loading" class="flex items-center bg-btn-gradient text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary-700 transition-colors">
    <div v-if="loading" class="btn-spinner mr-2" />
    <slot />
  </button>
</template>

<script>
export default {
  props: {
    loading: Boolean,
  },
}
</script>