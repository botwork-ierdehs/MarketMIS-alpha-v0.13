<template>
  <div>
    <input 
      type="file" 
      :accept="accept"
      @change="onFileChange"
      class="block w-full text-sm text-primary border border-primary rounded-lg cursor-pointer bg-gray-50 focus:ring-indigo-500 rounded-md shadow-sm"
    />

    <!-- Preview name -->
    <div v-if="file" class="mt-2 text-sm text-gray-600">
      Selected: {{ file.name }}
    </div>

    <!-- Error -->
    <InputError v-if="error" class="mt-2" :message="error" />
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  modelValue: File,
  label: { type: String, default: "" },
  accept: { type: String, default: ".jpg,.jpeg,.png,.pdf" },
  error: { type: String, default: "" }
});

const emit = defineEmits(["update:modelValue"]);

const file = ref(null);

const onFileChange = (event) => {
  const selected = event.target.files[0];
  file.value = selected || null;
  emit("update:modelValue", selected || null);
};

// keep sync with parent v-model
watch(() => props.modelValue, (val) => {
  file.value = val;
});
</script>
