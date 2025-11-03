<!-- components/ChartBar.vue -->
<template>
  <Bar :data="chartData" :options="chartOptions" />
</template>

<script setup>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

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
