<template>
    <input :id="id" ref="date" v-bind="{ ...$attrs, class: null }" class="form-input w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" :class="{ error: error }" :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" />
  </template>
  
  <script>
  import { v4 as uuid } from 'uuid'
  
  export default {
    inheritAttrs: false,
    props: {
      id: {
        type: String,
        default() {
          return `date-pick-${uuid()}`
        },
      },
      type: {
        type: String,
        default: 'date',
      },
      error: String,
      label: String,
      modelValue: String,
    },
    emits: ['update:modelValue'],
    methods: {
      focus() {
        this.$refs.input.focus()
      },
      select() {
        this.$refs.input.select()
      },
      setSelectionRange(start, end) {
        this.$refs.input.setSelectionRange(start, end)
      },
    },
  }
  </script>
  