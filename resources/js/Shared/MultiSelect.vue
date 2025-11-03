<template>
  <div class="w-full">
    <!-- Select Box -->
    <select
      multiple
      class="border rounded p-2 w-full"
      @change="onSelect($event)"
    >
      <option
        v-for="option in options"
        :key="option.id"
        :value="option.id"
        :selected="modelValue.includes(option.id)"
      >
        {{ option.type }}: {{ formatCurrency(option.amount) }}
      </option>
    </select>

    <!-- Selected Badges -->
    <div class="flex flex-wrap gap-2 mt-3">
      <span
        v-for="item in selectedItems"
        :key="item.id"
        class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center gap-2"
      >
        {{ item.type }}: {{ formatCurrency(item.amount) }}
        <button
          @click="removeItem(item.id)"
          class="text-red-500 hover:text-red-700"
        >
          ✕
        </button>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  name: "MultiSelect",
  props: {
    modelValue: {
      type: Array,
      default: () => [],
    },
    options: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    selectedItems() {
      return this.options.filter((opt) => this.modelValue.includes(opt.id));
    },
  },
  methods: {
    onSelect(event) {
      const selected = Array.from(event.target.selectedOptions).map((o) =>
        parseInt(o.value)
      );
      // Push new IDs into array (unique)
      const merged = Array.from(new Set([...this.modelValue, ...selected]));
      this.$emit("update:modelValue", merged);
    },
    removeItem(id) {
      const updated = this.modelValue.filter((v) => v !== id);
      this.$emit("update:modelValue", updated);
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
      }).format(amount);
    },
  },
};
</script>
