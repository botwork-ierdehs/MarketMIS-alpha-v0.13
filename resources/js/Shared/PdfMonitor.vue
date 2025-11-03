<template>
  <div class="pdf-viewer w-full h-full border rounded shadow overflow-auto">
    <iframe
      v-if="pdfUrl"
      :src="pdfUrl"
      frameborder="0"
      width="100%"
      height="100%"
    ></iframe>
    <p v-else class="text-gray-500 p-4 text-center">
      No PDF to display.
    </p>
  </div>
</template>

<script setup>
const props = defineProps({
  pdfUrl: { type: String, required: true },
});
</script>

<style scoped>
.pdf-viewer {
  height: 600px; /* Adjust height as needed */
  max-height: 80vh;
}
.pdf-viewer iframe {
  border-radius: 8px;
}
</style>
