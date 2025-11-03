<template>
    <label v-for="(option, i) in options" :key="i" class="inline-flex items-center space-x-1 mr-4 cursor-pointer">
      <input type="radio" :name="name" :value="option.value" :checked="modelValue === option.value" @change="$emit('update:modelValue', option.value)" class="form-radio text-blue-600" />
      <span>{{ option.label }}</span>
    </label>
</template>
<script setup>
  defineProps({
     modelValue: [String, Number, Boolean], 
     options: { type: Array, required: true }, 
     name: { type: String, required: true } 
  }); 
  defineEmits(['update:modelValue']);
</script>
