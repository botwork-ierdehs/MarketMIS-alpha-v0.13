<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stalls">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 class="mb-8 text-3xl font-bold text-primary">{{`Welcome, ${user?.name} to ${user?.subAdminType?.departments?.name} department`}}</h1>

         <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-4 py-5">
            <!-- Volante -->
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <Link class="flex items-center pr-2 " :href="route(redirectPage('Market Volante'), { status: 0 })" tabindex="-1">
                    <div class="bg-green-100 text-green-600 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-bold">Pending Market Volante Applicants – Today</p>
                        <p class="text-2xl font-bold text-green-600">{{counts?.totalVolante}}</p>
                    </div>
                </Link>
            </div>

            <!-- Table Spaces -->
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <Link class="flex items-center pr-2 " :href="route(redirectPage('Table Spaces'), { status: 0 })" tabindex="-1">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <title>Table - Grid</title>
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="3" y1="15" x2="21" y2="15"/>
                        <line x1="9" y1="3" x2="9" y2="21"/>
                        <line x1="15" y1="3" x2="15" y2="21"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-bold">Pending Table Space Applicants – Today</p>
                        <p class="text-2xl font-bold text-purple-600">{{counts?.totalTable}}</p>
                    </div>
                </Link>
            </div>

            <!-- Stall Rentals -->
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <Link class="flex items-center pr-2 " :href="route(redirectPage('Stall Leasing'), { status: 0 })" tabindex="-1">
                   <div class="bg-orange-100 text-orange-600 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M4 6h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-bold">Pending Stall Leasing Applicants – Today</p>
                    <p class="text-2xl font-bold text-orange-600">{{counts?.totalStall}}</p>
                </div>
                </Link>
            </div>
        </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-2">
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <div class="bg-green-100 text-green-600 p-3 rounded-full">
                    <svg width="24" height="24" viewBox="0 0 24 24" aria-label="Approve" role="img"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M8 12l2.5 2.5L16 9.5"></path>
                    </svg>

                </div>
                <div>
                    <p class="text-gray-600 text-sm font-bold">Total Approved This Month</p>
                        <p class="text-2xl font-bold text-indigo-600">{{counts?.total?.approved ?? 0}}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
                <div class="bg-red-100 text-red-600 p-3 rounded-full">
                    <svg width="24" height="24" viewBox="0 0 24 24" aria-label="Reject" role="img"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M9 9l6 6M15 9l-6 6"></path>
                    </svg>

                </div>
                <div>
                    <p class="text-gray-600 text-sm font-bold">Total Rejected This Month</p>
                    <p class="text-2xl font-bold text-indigo-600">{{counts?.total?.rejected ?? 0}}</p>
                </div>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-1 py-5">
            <h2 class="mb-8 text-3xl font-bold text-primary">Recent Applications</h2>
            <BaseTable :headers="headers" :data="myTopApply">
                <!-- Optional: Customize rows -->
                <template #row = "{ data }">
                    <tr v-for="(stall, i) in data" :key="stall?.type" class="hover:bg-blue-100 center">
                        <td class="px-4 py-3 text-gray-500 font-semibold">
                            <Link class="flex items-center pr-2 " :href="route(redirectPage(stall))" tabindex="-1">
                                {{ stall?.type }}
                            </Link>
                        </td>
                         <td class="px-4 py-3">
                            <Link class="flex items-center pr-2 " :href="route(redirectPage(stall))" tabindex="-1">
                                <div>
                                    <div class="font-semibold capitalize text-secondary">{{ fullName(stall?.vendor) }}</div>
                                    <div class="text-xs text-gray-500">{{ `${stall.vendor.email} | ${stall?.vendor.mobile}` }}</div>
                                </div>
                            </Link>
                        </td>
                        <td class="px-4 py-3 capitalize">
                            <Link class="flex items-center pr-2 " :href="route(redirectPage(stall))" tabindex="-1">
                                <div>
                                    <div class="font-semibold capitalize text-secondary">{{ stall?.data?.business_name }}</div>
                                    <div class="text-xs text-gray-500">{{ stall?.stall }}</div>
                                </div>
                            </Link>
                        </td>
                        <td class="px-4 py-3 capitalize">
                             <Link class="flex items-center pr-2 " :href="route(redirectPage(stall))" tabindex="-1">
                                <Badge :color="status(stall?.status).color">
                                    {{ status(stall?.status).status }}
                                </Badge>
                            </Link>
                        </td>
                        
                    </tr>
                    <tr v-if="data?.length === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
          </div>
    </div>
    </AppLayout>              
</template>


<script setup>
import { defineProps } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue'
import BaseTable from '@/Shared/BaseTable.vue';
import Badge from '@/Shared/Badge.vue';
import { Link } from '@inertiajs/vue3';
import { fullName } from '@/data/helper';

const headers = [
    'Type',
    'Vendor Details',
    'Business Details',
    'Status'
];

const props = defineProps({
    counts: Object,
    myTopApply: Object,
    stalls: Object,
    user: Object,
});

const status = (stat) => {
    let statuss = {
        color: 'blue',
        status: 'Vacant'
    };
    switch (stat) {
        case 4:
            statuss = {
                color: 'red',
                status: 'Terminated'
            };
            break;
        case 3:
            statuss = {
                color: 'orange',
                status: 'Expired'
            };
            break;
        case 2:
            statuss = {
                color: 'rose',
                status: 'Rejected'
            };
            break;
        case 1:
            statuss = {
                color: 'green',
                status: 'Approved'
            };
            break;
        default:
            statuss = {
                color: 'blue',
                status: 'Pending'
            };
    }
   
    return statuss;
};

function redirectPage(stallData) {
    if (!stallData) return '#';
    let urlPath = '';
    switch (stallData?.type ?? stallData) {
        case 'Stall Leasing':
            urlPath = 'department.applications.stalls';
            break;
        case 'Market Volante':
            urlPath = 'department.applications.volantes';
            break;
        default:
            urlPath = 'department.applications.table';
            break;
    }
    console.log(urlPath);
    return urlPath;
}
</script>
