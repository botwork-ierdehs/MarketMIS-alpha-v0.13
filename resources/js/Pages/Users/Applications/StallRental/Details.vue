<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stall Types">
        <template #header>
            <h2 class="font-semibold text-xl text-secondary leading-tight">
               Stall Types
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold">
          <Link class="text-primary hover:text-secondary" :href="`/my-rentals/stall-leasing`">Stalls Leasing</Link>
          <span class="text-secondary font-medium">/</span>
          {{stallRental?.name}}
        </h1>
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 space-y-6 max-h-[70vh] overflow-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-secondary">
                Vendor Stall Leasing Details
            </h1>
            <span class="text-sm text-gray-600">Rental No.: {{ stallRental?.permits?.permit_number || '-' }}</span>
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
                {{stallRental?.vendor?.status ? 'Active' : 'Not Active'}}
                </span>
            </div>

            <!-- Info -->
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-primary">{{ fullName(stallRental.vendor) }}</h2>
                <p class="text-sm text-gray-600">
                Email: <a :href="'mailto:' + stallRental?.vendor?.email" class="text-blue-600 hover:underline">{{ stallRental?.vendor.email }}</a>
                </p>
                <p class="text-sm text-gray-600">Mobile: {{ stallRental?.vendor.mobile }}</p>
            </div>
            </div>

            <!-- Stall Info -->
            <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
            <!-- Type & Dates -->
           
            <div>
                <div class="text-sm text-gray-700 mb-2">
                    <p class="text-gray-600"><span class="font-semibold">Type:</span> {{ stallRental?.permits.type }}</p>
                    <p class="text-gray-600"><span class="font-semibold">Date Issued:</span> {{ stallRental?.permits?.issued_date ? formatDateShort(stallRental?.permits?.issued_date) : '-'}}</p>
                </div>
                <h3 class="font-semibold text-secondary">Stall Details</h3>
                <p class="text-gray-600">Business Name: {{ stallRental?.business_name }}</p>
                <p class="text-gray-600">Stall Name: {{ stallRental?.stalls.name }}</p>
                <p class="text-gray-600">Size: {{ `${JSON.parse(stallRental?.stalls.size).length}X${JSON.parse(stallRental?.stalls.size).width}` }}</p>
                <p class="text-gray-600">Birthday: {{ formatDateShort(stallRental?.vendor.birthday) }}</p>
                <p class="text-gray-600">Address: {{ stallRental?.vendor.address }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-secondary">Category</h3>
                <p class="text-gray-600">{{ stallRental?.stalls?.stallsCategories.name }}</p>
                <h3 class="font-semibold mt-2 text-secondary">Status</h3>
                <p :class="`text-${formatStallStatus(stallRental.stalls?.status).color}-600`">{{ formatStallStatus(stallRental?.stalls?.status).status }}</p>
            </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                <h3 class="font-semibold text-secondary">Payment Details</h3>
                 <ul class="space-y-2">
                    <li v-for="(doc, i) in stallRental?.paymentDetails.breakdown" :key="i" class="flex justify-between items-center px-3 py-2 text-sm">
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
                    <span class="font-bold">{{ formatAmount(stallRental.quarterly_payment) }}</span>
                </p>
                <p class="text-gray-600">
                    Payment Due:
                    <span class="text-red-500 font-semibold">{{ stallRental.next_payment_due ? stallRental.next_payment_due : 'N/A' }}</span>
                </p>
                <p class="text-gray-600">
                    Current Payment Status:
                    <span :class="stallRental?.current_payment === 'Paid' ? 'text-green-600' : 'text-red-600'">{{ stallRental.current_payment }}</span>
                </p>
                <p class="font-bold text-secondary">
                    Total: <span class="text-green-600 font-semibold">{{ formatAmount(stallRental.quarterly_payment) }}</span>
                </p>
            </div>


            <!-- Requirements -->
            <div>
            <h3 class="font-semibold text-secondary mb-2 text-sm">Requirements</h3>
            <ul class="space-y-2 text-sm">
                <li
                v-for="req in stallRental.requirements"
                :key="req.name"
                class="flex items-center justify-between bg-gray-50 p-2 rounded"
                >
                <span class="text-gray-600">{{ req.name }}</span>
                <div class="flex gap-2">
                    <button class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-600 hover:bg-blue-200">
                     <a
                        v-if="req.url"
                        :href="`${req.url}/${req.attachment}`"
                        target="_blank"
                    >
                        View Attachment
                    </a>
                    </button>
                </div>
                </li>
            </ul>
            </div>


            <!-- Approvals -->
            <div  class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm" v-if="stallRental?.approvals?.length > 0">
                <h3 class="text-md font-semibold mb-3 text-primary">Approvals</h3>
                    <ul class="space-y-2 text-sm">
                    <li class="flex items-center space-x-2">
                        <ul class="space-y-2">
                            <li v-for="(approval, i) in stallRental?.approvals" :key="i" class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg shadow-sm">
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
                <button @click="goToEdit(stallRental.id)" :class="stallRental?.permits?.status === 0 || stallRental?.expiringSoon ? 'p-2 rounded-lg h bg-blue-600 text-white over:bg-blue-700' : 'p-2 rounded-lg h bg-blue-100 text-white over:bg-blue-100'"  
                :disabled="stallRental?.permits?.status !== 0 || stallRental?.expiringSoon">
                    Edit
                </button>
                <button @click="goToReupload(stallRental.id)" class="p-2 rounded-lg hover:bg-red-200" 
                :class="stallRental?.permits?.status === 2 || stallRental?.expiringSoon ? 'bg-red-600 text-white' : 'bg-red-200 text-white'" :disabled="stallRental?.permits?.status !== 2 || stallRental?.expiringSoon">
                    Reupload Requirements
                </button>
                <button @click="goToEdit(stallRental.id)" :class="stallRental?.expiringSoon ? 'p-2 rounded-lg h bg-green-600 text-white over:bg-green-700' : 'p-2 rounded-lg h bg-green-100 text-white over:bg-green-100'" 
                :disabled="!stallRental?.expiringSoon">
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
            <BaseTable :headers="headers" :data="stallRental?.payments_history" :class="'center text-center sticky right-0 bg-white z-10'">
                <template #row="{ data }">
                    <tr v-for="(bsn, i) in data" :key="stallRental.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 capitalize">{{bsn?.date}}</td>
                        <td class="px-4 py-3 capitalize">{{ formatAmount(bsn?.amount) }}</td>
                        <td class="px-4 py-3 capitalize">{{ bsn?.reference_number }}</td>
                        <td class="px-4 py-3">{{ bsn?.receipt }}</td>
                    </tr>
                    <tr v-if="stallRental?.payments_history?.length === 0">
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
    stallRental: Object,
    auth: Object,
});


const initials = computed(() => {
  const names = props.stallRental?.vendor?.first_name.split(" ");
  return names.map((n) => n[0]).join("");
});

const headers = [
    'Date',
    'Amount',
    'Reference Number',
    'Receipt'
];

const goToEdit = (id) => {
  router.get(`/my-rentals/stall-leasing/${id}/edit`)
};

const goToReupload = (id) => {
  router.get(`/my-rentals/stall-leasing/${id}/reupload`)
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

