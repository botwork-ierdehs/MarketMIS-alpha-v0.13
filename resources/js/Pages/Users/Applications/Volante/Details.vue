<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stall Types">
        <template #header>
            <h2 class="font-semibold text-xl text-secondary leading-tight">
               Stall Types
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold">
          <Link class="text-primary hover:text-secondary" :href="`/my-rentals/market-volante`">Market Volante</Link>
          <span class="text-secondary font-medium">/</span>
          {{volanteRental?.name}}
        </h1>
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 space-y-6 max-h-[70vh] overflow-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-secondary">
                Market Volante Rental Details
            </h1>
            <span class="text-sm text-gray-600">Rental No.: {{ volanteRental?.permits?.permit_number || '-' }}</span>
            </div>

            <!-- Vendor Info -->
            <div class="flex gap-6">
            <!-- Avatar -->
            <div class="flex flex-col items-center">
                <div
                class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg"
                >
                {{ initials }}
                </div>
                <span
                class="mt-2 text-xs px-2 py-0.5 rounded bg-green-100 text-green-600 font-medium"
                >
                {{volanteRental?.vendor?.status ? 'Active' : 'Not Active'}}
                </span>
            </div>

            <!-- Info -->
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-primary">{{ fullName(volanteRental.vendor) }}</h2>
                <p class="text-sm text-gray-600">
                Email: <a :href="'mailto:' + volanteRental?.vendor?.email" class="text-blue-600 hover:underline">{{ volanteRental?.vendor.email }}</a>
                </p>
                <p class="text-sm text-gray-600">Mobile: {{ volanteRental?.vendor.mobile }}</p>
            </div>
            </div>

            <!-- Stall Info -->
            <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
            <!-- Type & Dates -->
           
            <div>
                <div class="text-sm text-gray-700 mb-2">
                    <p class="text-gray-600"><span class="font-semibold">Type:</span> {{ volanteRental?.permits.type }}</p>
                    <p class="text-gray-600"><span class="font-semibold">Date Issued:</span> {{ volanteRental?.permits?.issued_date ? formatDateShort(volanteRental?.permits?.issued_date) : '-'}}</p>
                </div>
                <h3 class="font-semibold text-secondary">Slot Details</h3>
                <p class="text-gray-600">Business Name: {{ volanteRental?.name }}</p>
                <p class="text-gray-600">Stall Name: {{ volanteRental?.stalls.name }}</p>
                <p class="text-gray-600">Birthday: {{ formatDateShort(volanteRental?.vendor.birthday) }}</p>
                <p class="text-gray-600">Address: {{ volanteRental?.vendor.address }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-secondary">Category</h3>
                <p class="text-gray-600">{{ volanteRental?.stalls?.stallsCategories.name }}</p>
                <h3 class="font-semibold mt-2 text-secondary">Status</h3>
                <p :class="`text-${formatStallStatus(volanteRental.stalls?.status).color}-600`">{{ formatStallStatus(volanteRental?.stalls?.status).status }}</p>
            </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                <h3 class="font-semibold text-secondary">Payment Details</h3>
                 <ul class="space-y-2">
                    <li v-for="(doc, i) in volanteRental?.paymentDetails.breakdown" :key="i" class="flex justify-between items-center px-3 py-2 text-sm">
                        <div class="flex items-center space-x-2">
                        <span>
                            <strong class="text-secondary">{{doc?.size ? `• ${doc?.type}(${doc.size}): ` : `• ${doc?.type}: `}} </strong>
                            <span class="text-gray-600"> {{formatAmount(doc?.amount)}}</span>
                        </span>
                        </div>
                    </li>
                </ul>
                <p class="text-gray-600">
                    Quarterly Payment:
                    <span class="font-bold">{{ formatAmount(volanteRental.quarterly_payment) }}</span>
                </p>
                <p class="text-gray-600">
                    Payment Due:
                    <span class="text-red-500 font-semibold">{{ volanteRental.next_payment_due ? volanteRental.next_payment_due : 'N/A' }}</span>
                </p>
                <p class="text-gray-600">
                    Current Payment Status:
                    <span :class="volanteRental?.current_payment === 'Paid' ? 'text-green-600' : 'text-red-600'">{{ volanteRental.current_payment }}</span>
                </p>
                <p class="font-bold text-secondary">
                    Total: <span class="text-green-600 font-semibold">{{ formatAmount(volanteRental.quarterly_payment) }}</span>
                </p>
            </div>


            <!-- Requirements -->
            <div>
            <h3 class="font-semibold text-secondary mb-2 text-sm">Requirements</h3>
            <ul class="space-y-2 text-sm">
                <li
                v-for="req in volanteRental.requirements"
                :key="req.name"
                class="flex items-center justify-between bg-gray-50 p-2 rounded"
                >
                <span class="text-gray-600">{{ req.name }}</span>
                <div class="flex gap-2">
                <!-- View Attachment Button -->
                <button
                    class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-600 hover:bg-blue-200"
                >
                    <a
                    v-if="req.url"
                    :href="`${req.url}/${req.attachment}`"
                    target="_blank"
                    >
                    View Attachment
                    </a>
                </button>

                <!-- Print Button -->
                <button
                    v-if="req.url"
                    @click="printAttachment(`${req.url}/${req.attachment}`)"
                    class="flex items-center gap-1 px-2 py-1 text-xs rounded bg-green-100 text-green-600 hover:bg-green-200"
                >
                    <!-- Print SVG Icon -->
                    <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 9V2h12v7M6 18h12v4H6v-4zM6 14h12a2 2 0 002-2V9a2 2 0 00-2-2H6a2 2 0 00-2 2v3a2 2 0 002 2z"
                    />
                    </svg>
                    Print
                </button>
                </div>

                </li>
            </ul>
            </div>


            <!-- Approvals -->
            <div  class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm" v-if="volanteRental?.approvals?.length > 0">
                <h3 class="text-md font-semibold mb-3 text-primary">Approvals</h3>
                    <ul class="space-y-2 text-sm">
                    <li class="flex items-center space-x-2">
                        <ul class="space-y-2">
                            <li v-for="(approval, i) in volanteRental?.approvals" :key="i" class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg shadow-sm">
                            <div class="flex items-center space-x-2">
                                <span>
                                    <strong class="text-secondary">{{`${approval?.department}: `}} </strong>
                                    <span class="text-gray-600"> {{`${approval?.approver?.name}(${approval?.approver?.position}) - ${status(approval.status).status}`}}</span>
                                </span>
                            </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>  
            <!-- Actions -->
            <div class="flex justify-end gap-2">
                <button @click="goToEdit(volanteRental.id)" :class="volanteRental?.permits?.status === 0 || volanteRental?.expiringSoon ? 'p-2 rounded-lg h bg-blue-600 text-white over:bg-blue-700' : 'p-2 rounded-lg h bg-blue-100 text-white over:bg-blue-100'"  
                :disabled="volanteRental?.permits?.status !== 0 || volanteRental?.expiringSoon">
                    Edit
                </button>
                <button @click="goToReupload(volanteRental.id)" class="p-2 rounded-lg hover:bg-red-200" 
                :class="volanteRental?.permits?.status === 2 || volanteRental?.expiringSoon ? 'bg-red-600 text-white' : 'bg-red-200 text-white'" :disabled="volanteRental?.permits?.status !== 2 || volanteRental?.expiringSoon">
                    Reupload Requirements
                </button>
                <button @click="goToEdit(volanteRental.id)" :class="volanteRental?.expiringSoon ? 'p-2 rounded-lg h bg-green-600 text-white over:bg-green-700' : 'p-2 rounded-lg h bg-green-100 text-white over:bg-green-100'" 
                :disabled="!volanteRental?.expiringSoon">
                    Renew
                </button>
            </div>
        </div>

        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 space-y-6 max-h-[70vh] overflow-auto mt-5">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-secondary">
                    Payment History
                </h1>
            </div>
            <BaseTable :headers="headers" :data="volanteRental?.payments_history" :class="'center text-center sticky right-0 bg-white z-10'">
                <template #row="{ data }">
                    <tr v-for="(bsn, i) in data" :key="volanteRental.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 capitalize">{{bsn?.date}}</td>
                        <td class="px-4 py-3 capitalize">{{ formatAmount(bsn?.amount) }}</td>
                        <td class="px-4 py-3 capitalize">{{ bsn?.reference_number }}</td>
                        <td class="px-4 py-3">{{ bsn?.receipt }}</td>
                    </tr>
                    <tr v-if="volanteRental?.payments_history?.length === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
    </AppLayout>
</template>


<script setup>
import { defineProps, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { formatAmount, formatDateShort, formatStallStatus, fullName } from '@/data/helper';

import AppLayout from '@/Layouts/AppLayout.vue'
import BaseTable from '@/Shared/BaseTable.vue';


const props = defineProps({
    volanteRental: Object,
    auth: Object,
});


const initials = computed(() => {
  const names = props.volanteRental?.vendor?.first_name.split(" ");
  return names.map((n) => n[0]).join("");
});

const headers = [
    'Date',
    'Amount',
    'Reference Number',
    'Receipt'
];

const goToEdit = (id) => {
  router.get(`/my-rentals/market-volante/${id}/edit`)
};

const goToReupload = (id) => {
  router.get(`/my-rentals/market-volante/${id}/reupload`)
};

 const printAttachment = (printUrl) =>{
    const newWindow = window.open(printUrl, "_blank");
    newWindow.focus();
    newWindow.print();
};

//0-pending,1- approved, 2-rejected, 3- expired
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

</script>

