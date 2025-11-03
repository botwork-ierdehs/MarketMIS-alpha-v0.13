<script setup>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)

defineProps({
  labels: Array,
  values: Array,
})

const data = {
  labels: labels,
  datasets: [
    {
      label: 'Line Dataset',
      data: values,
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59,130,246,0.2)',
      tension: 0.4,
    },
  ],
}

const options = {
  responsive: true,
  maintainAspectRatio: false,
}
</script>

<template>
  <div class="w-full h-[300px]">
    <Line :data="data" :options="options" />
  </div>
</template>
