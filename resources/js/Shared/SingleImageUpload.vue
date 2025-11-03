<template>
  <div class="space-y-4 w-full max-w-sm mx-auto">
    <label class="block text-gray-700 font-medium">{{ label }}</label>

    <!-- Dropzone / Click -->
    <div
      class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 hover:bg-gray-50 transition"
      @dragover.prevent
      @drop.prevent="handleDrop"
      @click="fileInput.click()"
    >
      <p v-if="!file" class="text-gray-500">
        Drag & drop an image here or click to select
      </p>
      <p v-else class="text-gray-700 font-medium truncate">{{ file.name }}</p>
    </div>

    <!-- Image Preview -->
    <div v-if="file" class="mt-2">
      <img
        :src="previewUrl"
        class="rounded shadow object-contain"
        :style="{ width: width + 'px', height: height + 'px' }"
        alt="Preview"
      />
    </div>

    <!-- Clear Button -->
    <button
      v-if="file"
      @click="clearFile"
      class="mt-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded shadow"
    >
      Remove Image
    </button>

    <!-- Hidden input -->
    <input
      type="file"
      accept="image/jpeg,image/png"
      class="hidden"
      ref="fileInput"
      @change="handleChange"
    />
  </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from "vue";

const props = defineProps({
  modelValue: File,
  label: { type: String, default: "Upload Image (JPG or PNG)" },
  width: { type: Number, default: 100 },
  height: { type: Number, default: 50 },
});

const emit = defineEmits(["update:modelValue"]);

const fileInput = ref(null);
const file = ref(props.modelValue || null);
const previewUrl = ref(null);

// Watch for changes to file and emit to parent
watch(file, (f) => {
  emit("update:modelValue", f);
  if (f) {
    const reader = new FileReader();
    reader.onload = (e) => (previewUrl.value = e.target.result);
    reader.readAsDataURL(f);
  } else {
    previewUrl.value = null;
  }
});

// Handle manual selection
const handleChange = (e) => {
  const selected = e.target.files[0];
  if (selected && validateFile(selected)) file.value = selected;
};

// Handle drag & drop
const handleDrop = (e) => {
  const dropped = e.dataTransfer.files[0];
  if (dropped && validateFile(dropped)) file.value = dropped;
};

// Validate JPG/PNG
const validateFile = (f) => {
  if (!["image/jpeg", "image/png"].includes(f.type)) {
    alert("Only JPG or PNG images are allowed.");
    return false;
  }
  return true;
};

// Clear selection
const clearFile = () => {
  file.value = null;
  if (fileInput.value) fileInput.value.value = "";
};
</script>
