<!-- components/ChartPie.vue -->
<template>
  <Pie :data="chartData" :options="chartOptions" />
</template>

<script setup>
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
  chartData: Object,
  chartOptions: Object,
})

</script>

<style scoped>
canvas {
  height: 300px !important;
}
</style>
