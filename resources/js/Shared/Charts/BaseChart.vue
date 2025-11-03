<script setup>
defineProps({
  chartType: Object,
  data: Object,
  options: Object,
})
</script>

<template>
  <div class="w-full h-[300px] md:h-[400px] lg:h-[500px]">
    <component :is="chartType" :data="data" :options="options" />
  </div>
</template>
