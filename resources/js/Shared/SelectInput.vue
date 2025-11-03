<template>
  <div :class="$attrs.class">
    <select :id="id" ref="input" v-model="selected" v-bind="{ ...$attrs, class: null }" class="form-select w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" :class="{ error: error }">
      <slot />
    </select>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `select-input-${uuid()}`
      },
    },
    error: String,
    label: String,
    modelValue: [String, Number, Boolean],
  },
  emits: ['update:modelValue'],
  data() {
    return {
      selected: this.modelValue,
    }
  },
  watch: {
    selected(selected) {
      this.$emit('update:modelValue', selected)
    },
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
  },
}
</script>