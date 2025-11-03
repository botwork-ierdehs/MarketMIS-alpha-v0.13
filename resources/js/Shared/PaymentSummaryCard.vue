<!-- components/PaymentSummaryCard.vue -->
<template>
  <div v-if="details" class="max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow p-6 space-y-4">
      <!-- Header -->
      <h2 class="text-lg font-semibold text-gray-800 flex items-center">
        <slot name="icon">
          <svg xmlns="http://www.w3.org/2000/svg" 
              class="w-5 h-5 mr-2 text-primary" 
              fill="none" viewBox="0 0 24 24" 
              stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8c-1.1 0-2 .9-2 2v2h4v-2c0-1.1-.9-2-2-2zm-6 8V10a6 6 0 1112 0v6h2v2H4v-2h2z" />
          </svg>
        </slot>
        <slot name="title">Payment Summary</slot>
      </h2>

      <!-- Dynamic Key Values (days, weeks, etc.) -->
     <div
        v-for="(value, key) in filteredDetails"
        :key="key"
        class="flex justify-between text-sm text-gray-600"
        >
        <span class="capitalize">{{ key }}</span>
        <span class="font-medium text-gray-800">{{ value }}</span>
    </div>

      <!-- Breakdown -->
      <div v-if="details.breakdown" class="border-t pt-3">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Breakdown</h3>
        <ul class="space-y-1">
          <li v-for="(item, index) in details.breakdown" :key="index"
              class="flex justify-between text-sm">
            <span>{{ item.type || `Item ${index + 1}` }}</span>
            <span class="font-medium">{{ formatAmount(item.amount) }}</span>
          </li>
        </ul>
      </div>

      <!-- Total -->
      <div v-if="details.total !== undefined" class="border-t pt-3 flex justify-between items-center">
        <span class="text-base font-semibold text-gray-700">Total</span>
        <span class="text-xl font-bold text-primary">{{ formatAmount(details.total)  }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps } from "vue"
import { formatAmount } from '@/data/helper';

const props = defineProps({
  details: {
    type: Object,
    required: true
  }
})
const filteredDetails = computed(() => {
  // Remove breakdown + total from loop
  const { breakdown, total, ...rest } = props.details
  return rest
})
</script>
