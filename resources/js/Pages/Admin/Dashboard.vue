<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stalls">
         <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-4">
            <!-- Volante -->
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-bold">Pending Market Volante Applicants – Today</p>
                    <p class="text-2xl font-bold text-green-600">{{todayVolanteCount}}</p>
                </div>
            </div>

            <!-- Table Spaces -->
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M4 6h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-bold">Pending Table Space Applicants – Today</p>
                    <p class="text-2xl font-bold text-purple-600">{{todayTableCount}}</p>
                </div>
            </div>

            <!-- Stall Rentals -->
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <div class="bg-orange-100 text-orange-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M4 6h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-bold">Pending Stall Leasing Applicants – Today</p>
                    <p class="text-2xl font-bold text-orange-600">{{todayStallCount}}</p>
                </div>
            </div>
        </div>
         <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-5">
            <div class="bg-white p-4 rounded-2xl shadow">
                <BarChart :chartData="barData" :options = "{
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        title: {
                        display: true,
                        text: 'Monthly Lease Applications',
                        },
                    }
                }"/>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow">
                <PieChart :chartData="pieData" :options = "{
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    title: {
                    display: true,
                    text: 'Rental Application Status Overview',
                    },
                },
                }"/>
            </div>
        </div>
       
    </AppLayout>
</template>


<script setup>
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue'
import BarChart from '@/Shared/Charts/Bar.vue'
import PieChart from '@/Shared/Charts/Pie.vue'

const props = defineProps({
    occupiedStallRentalPerCategory: Object,
    businessPermitMonthlyCounts: Array,
    volantePermitMonthlyCounts: Array,
    stallPermitMonthlyCounts: Array,
    tablePermitMonthlyCounts: Array,
    permitStatusCounts: Array,
    todayStallCount: Number,
    todayVolanteCount: Number,
    todayTableCount: Number,
});

const barData = {
  labels: props?.businessPermitMonthlyCounts.map((item) => item.month),
  datasets: [
    {
        label: 'Stall Leasing',
        backgroundColor: '#F59E0B',
        data: props?.stallPermitMonthlyCounts.map((item) => item.total)
    },
    {
        label: 'Table Spaces',
        backgroundColor: '#6366F1',
        data: props?.tablePermitMonthlyCounts.map((item) => item.total)
    },
    {
        label: 'Market Volante',
        backgroundColor: '#10B981',
        data: props?.volantePermitMonthlyCounts.map((item) => item.total)
    },
  ]
}

const pieData = {
  labels: props.permitStatusCounts.map((item) => item.label),
  datasets: [{
    label: 'Applications',
    data: props.permitStatusCounts.map((item) => item.total),
    backgroundColor: ['blue', '#10B981', 'rose', 'orange', 'red'],
  }]
}
</script>
