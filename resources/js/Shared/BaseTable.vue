<template>
  <div class="bg-white rounded-md shadow overflow-x-auto w-full">
    <table class="w-full whitespace-nowrap min-w-full divide-y divide-gray-200 text-sm text-left text-gray-700">
      <thead class="bg-gray text-primary uppercase text-xs center">
          <tr class="text-left font-bold">
          <slot name="header">
            <th v-for="(header, index) in headers" :key="index" class="px-4 py-3 font-bold whitespace-nowrap bg-white">
              {{ header }}
            </th>
          </slot>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <slot name="row" :data="data">
          <tr v-for="(item, rowIndex) in data" :key="rowIndex" class="hover:bg-gray-50 bg-white">
          </tr>
        </slot>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  headers: {
    type: Array,
    required: true
  },
  data: {
    type: Array,
    required: true
  }
});
</script>
