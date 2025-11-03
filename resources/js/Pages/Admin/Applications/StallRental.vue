<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stalls">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stalls
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold text-primary">Stall Leasing</h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <FlashMessage v-if="$page.props.flash.success" :message="$page.props.flash.success" type="success" />
            <FlashMessage v-if="$page.props.flash.error" :message="$page.props.flash.error" type="error" />
            <div class="flex items-center justify-between mb-6">
             <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
                <label class="block text-gray-700">Stall Category:</label>
                <select v-model="form.status" class="form-select mt-1 w-full">
                    <option :value="null">All</option>
                    <option
                        v-for="categories in stallsCategories"
                        :key="categories.id"
                        :value="categories.id"
                    >{{ categories.name }}</option> 
                </select>
            </search-filter>
            </div>
            <BaseTable :headers="headers" :data="stallRentals?.data" :class="'center'">
                <template #row="{ data }">
                    <tr v-for="(bsn, i) in data" :key="stallRentals.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 capitalize text-center">{{bsn?.permits?.permit_number ? `${bsn?.permits?.permit_number }`: '-'}}</td>
                        <td class="px-4 py-3 capitalize text-center">{{`${fullName(bsn?.vendor)}`}}</td>
                        <td class="px-4 py-3 capitalize text-center">{{ bsn.name }}</td>
                        <td class="px-4 py-3 capitalize text-center">{{ bsn?.stalls?.name }}</td>
                        <td class="px-4 py-3 capitalize text-center">{{bsn?.start_date && bsn?.end_date ? `${formatDate(bsn.start_date)} - ${formatDate(bsn.end_date)}` : '-' }}</td>
                        <td class="px-4 py-3 capitalize text-center">
                            <Badge :color="bsn?.permits?.type === 'new' ? 'green' : 'blue'">
                                {{ bsn?.permits?.type }}
                             </Badge>
                        </td>
                        <td class="px-4 py-3 capitalize text-center">{{formatAmount(bsn?.quarterly_payment)}}</td>
                        <td class="px-4 py-3 capitalize text-center">{{ bsn?.next_payment_due ? formatDate(new Date(bsn?.next_payment_due)) : 'N/A'}}</td>
                        <td class="px-4 py-3 capitalize text-center">{{formatAmount(bsn?.penalty)}}</td>
                        <td class="px-4 py-3 capitalize text-center">
                            <p class="font-semibold" :class="bsn?.current_payment === 'Paid' ? 'text-green-600':'text-red-600'">{{bsn?.current_payment}}</p>
                        </td>
                        <td class="px-4 py-3 capitalize text-center">
                             <Badge :color="status(bsn.permits.status).color">
                                {{ status(bsn.permits.status).status }}
                             </Badge>
                        </td>
                        <td class="w-px border-t" v-if=" props?.filters?.role !== 'user'">
                            <Icons name="cheveron-right" @click="getDetails(bsn)" class="block w-6 h-6 fill-primary" />
                        </td>
                    </tr>
                    <tr v-if="stallRentals?.total === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="12">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="stallRentals?.links" />
        </div>
        <Drawer v-model="showDrawer" :title="'Application Details'" :data="drawerData.value">
        <template #default>
            <div v-if="drawerData">
            <div class="h-full overflow-y-auto p-6 space-y-6">
                
                <!-- Application Info Section -->
                <div class="p-4 border rounded-xl bg-gray-50 flex justify-between items-start">
                <div class="flex items-start space-x-3">
                    <Icons name="stalls" class="w-6 h-6 fill-primary mt-1" />
                    <div>
                    <p class="font-semibold text-primary text-base leading-tight">
                        {{ `Stall Leasing Application (${drawerData?.permits?.type})` }}
                    </p>
                    <div class="flex items-center text-xs text-gray-500 space-x-2 mt-1">
                        <Badge :color="status(drawerData?.permits.status).color">
                        {{ status(drawerData?.permits.status).status }}
                        </Badge>
                        <span>{{`• Applied ${formatDateShort(drawerData.permits.created_at)}`}}</span>
                    </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2" v-if="$page.props.auth.user?.role_id === 2">
                    <button
                    class="flex items-center px-3 py-1.5 text-white bg-red-600 rounded-lg hover:bg-red-700 text-sm"
                    @click="rejectPermits"
                    >
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 
                        011.414 0L10 8.586l4.293-4.293a1 1 0 
                        111.414 1.414L11.414 10l4.293 4.293a1 1 
                        01-1.414 1.414L10 11.414l-4.293 4.293a1 
                        1 0 01-1.414-1.414L8.586 10 4.293 
                        5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Reject
                    </button>
                    <form @submit.prevent="approveConfirm">
                    <button
                        class="flex items-center px-3 py-1.5 text-white bg-green-600 rounded-lg hover:bg-green-700 text-sm"
                    >
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 
                            010 1.414l-8 8a1 1 0 
                            01-1.414 0l-4-4a1 1 0 
                            011.414-1.414L8 12.586l7.293-7.293a1 1 
                            0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Approve
                    </button>
                    </form>
                </div>
                </div>

                <!-- Vendor Information -->
                <div class="p-4 border rounded-xl space-y-2">
                <h3 class="text-md font-semibold mb-4 text-primary">Vendor Information</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center space-x-3">
                    <svg class="w-4 h-4 text-secondary" viewBox="0 0 64 64" stroke="currentColor" fill="none">
                        <circle cx="32" cy="20" r="8" />
                        <path d="M16 52c0-8 8-14 16-14s16 6 16 14" />
                    </svg>
                    <span><strong class="text-secondary">Full Name: </strong><span class="text-gray-600">{{ fullName(drawerData?.vendor) }}</span></span>
                    </li>

                    <li class="flex items-center space-x-3">
                    <svg class="w-4 h-4 text-secondary" viewBox="0 0 64 64" stroke="currentColor" fill="none">
                        <rect x="10" y="16" width="44" height="32" rx="4" ry="4" />
                        <polyline points="10,16 32,36 54,16" />
                    </svg>
                    <span><strong class="text-secondary">Email Address: </strong><span class="text-gray-600">{{ drawerData?.vendor?.email }}</span></span>
                    </li>

                    <li class="flex items-center space-x-3">
                    <svg class="w-4 h-4 text-secondary" viewBox="0 0 64 64" stroke="currentColor" fill="none">
                        <rect x="20" y="8" width="24" height="48" rx="4" ry="4" />
                        <circle cx="32" cy="50" r="2" />
                    </svg>
                    <span><strong class="text-secondary">Phone Number: </strong><span class="text-gray-600">{{ drawerData?.vendor?.mobile }}</span></span>
                    </li>

                    <li class="flex items-center space-x-3">
                    <svg class="w-4 h-4 text-secondary" viewBox="0 0 64 64" stroke="currentColor" fill="none">
                        <path d="M32 8c-8 0-14 6-14 14 0 10 14 26 14 26s14-16 14-26c0-8-6-14-14-14z" />
                        <circle cx="32" cy="22" r="4" />
                    </svg>
                    <span><strong class="text-secondary">Address: </strong><span class="text-gray-600">{{ drawerData?.vendor?.address }}</span></span>
                    </li>

                    <li class="flex items-center space-x-3">
                    <svg class="w-4 h-4 text-secondary" viewBox="0 0 64 64" stroke="currentColor" fill="none">
                        <rect x="10" y="14" width="44" height="40" rx="4" ry="4" />
                        <line x1="10" y1="24" x2="54" y2="24" />
                        <line x1="22" y1="8" x2="22" y2="20" />
                        <line x1="42" y1="8" x2="42" y2="20" />
                        <circle cx="32" cy="38" r="4" />
                    </svg>
                    <span><strong class="text-secondary">Birthday: </strong><span class="text-gray-600">{{ formatDateShort(drawerData?.vendor?.birthday) }}</span></span>
                    </li>
                </ul>
                </div>

                <!-- Business Information -->
                <div class="p-4 border rounded-xl space-y-3">
                <h3 class="text-md font-semibold mb-3 text-primary">Business Information</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center space-x-3">
                    <Icons name="business-names" class="w-4 h-4 text-secondary" />
                    <span><strong class="text-secondary">Business Name: </strong><span class="text-gray-600">{{ drawerData?.name }}</span></span>
                    </li>
                    <li class="flex items-center space-x-3">
                    <Icons name="business-category" class="w-4 h-4 text-secondary" />
                    <span><strong class="text-secondary">Category: </strong><span class="text-gray-600">{{ drawerData?.stalls?.stallsCategories?.name }}</span></span>
                    </li>
                </ul>
                </div>

                <!-- Requirements -->
                <div class="p-4 border rounded-xl space-y-3">
                <h3 class="text-sm font-semibold mb-3 text-primary">Requirements Submitted</h3>
                <ul class="space-y-3 text-sm">
                    <li v-for="(doc, i) in drawerData.requirements" :key="i" class="flex justify-between items-center bg-gray-50 px-4 py-2 rounded-lg shadow-sm">
                    <div class="flex items-center space-x-2">
                        <Icons name="requirements" class="w-5 h-5 fill-primary" />
                        <span class="text-gray-600">{{ doc.name }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a
                        v-if="doc.url"
                        :href="`${doc.url}/${doc.attachment}`"
                        class="text-blue-600 hover:text-blue-800 text-sm underline"
                        target="_blank"
                        >[View]</a>

                        <button
                        v-if="doc.url"
                        @click="printAttachment(`${doc.url}/${doc.attachment}`)"
                        class="flex items-center text-green-600 hover:text-green-800 text-sm"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18h12v4H6v-4zM6 14h12a2 2 0 002-2V9a2 2 0 00-2-2H6a2 2 0 00-2 2v3a2 2 0 002 2z" />
                        </svg>
                        Print
                        </button>
                    </div>
                    </li>
                </ul>
                </div>

                <!-- Payments -->
                <div class="p-4 border rounded-xl space-y-3">
                <h3 class="text-md font-semibold text-primary">Payments</h3>
                <ul class="space-y-3 text-sm">
                    <li v-for="(doc, i) in drawerData?.paymentDetails?.breakdown" :key="i" class="flex justify-between bg-gray-50 px-4 py-2 rounded-lg shadow-sm">
                    <span><strong class="text-secondary">{{ doc?.type }}:</strong> <span class="text-gray-600">{{ formatAmount(doc?.amount) }}</span></span>
                    </li>
                    <li class="flex justify-between px-4 py-2">
                    <strong class="text-secondary">Total:</strong>
                    <span class="text-gray-600">{{ formatAmount(drawerData?.quarterly_payment) }}</span>
                    </li>
                </ul>
                </div>

                <!-- Approvals -->
                <div class="p-4 border rounded-xl space-y-3" v-if="drawerData?.approvals?.length > 0">
                <h3 class="text-md font-semibold text-primary">Approvals</h3>
                <ul class="space-y-3 text-sm">
                    <li
                    v-for="(approval, i) in drawerData?.approvals"
                    :key="i"
                    class="flex justify-between bg-gray-50 px-4 py-2 rounded-lg shadow-sm"
                    >
                    <span>
                        <strong class="text-secondary">{{ approval?.department }}: </strong>
                        <span class="text-gray-600">{{ `${approval?.approver?.name} (${approval?.approver?.position}) - ${approval?.status === 1 ? 'Approved' : 'Rejected'}` }}</span>
                    </span>
                    </li>
                </ul>
                </div>
            </div>
            </div>
        </template>
        </Drawer>
        <ConfirmationModal :show="isRejected" @close="closeRejection">
                <template #title>
                    Reject Request
                </template>

                <template #content>
                    <div class="space-y-4 p-4">
                        <!-- Question -->
                        <p class="text-gray-700 text-sm md:text-base">
                            Are you sure you would like to reject this request?  
                            Please provide a reason below:
                        </p>

                        <!-- Remarks field -->
                        <div>
                            <textarea
                                id="remarks"
                                v-model="forms.remarks"
                                rows="3"
                                required
                                placeholder="Enter your reason for rejection..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                                    focus:border-red-500 focus:ring-red-500 sm:text-sm resize-none"
                            ></textarea>
                            <InputError class="mt-2" :message="form?.errors?.remarks" />
                        </div>
                    </div>
                </template>

                <template #footer>
                    <div class="flex justify-end space-x-3 p-4">
                        <SecondaryButton @click="closeRejection">
                            Cancel
                        </SecondaryButton>
                    <form @submit="rejectConfirm">

                        <DangerButton
                            :disabled="!forms.remarks"
                            @click="rejectConfirm"
                            class="disabled:opacity-50"
                        >
                            Reject
                        </DangerButton>
                    </form> 
                    </div>
                </template>
        </ConfirmationModal>
    </AppLayout>
</template>


<script setup>
import {
    defineProps, watch, reactive, ref
} from 'vue';
import {
    formatAmount,
    formatDate,
    formatDateShort,
    fullName
} from '@/data/helper';
import {
    router,
    useForm
} from '@inertiajs/vue3';
import { throttle, mapValues } from 'lodash'
import pickBy from 'lodash/pickBy'

import AppLayout from '@/Layouts/AppLayout.vue'
import BaseTable from '@/Shared/BaseTable.vue';
import Pagination from '@/Shared/Pagination.vue';
import SearchFilter from '@/Shared/SearchFilter.vue'
import Badge from '@/Shared/Badge.vue';
import Icons from '@/Shared/Icons.vue';
import Drawer from '@/Components/Drawer.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import FlashMessage from '@/Shared/FlashMessage.vue';

const headers = [
    'Rental No.',
    'Vendor',
    'Business Name',
    'Stall',
    'Duration',
    'Type',
    'Quarterly Payment',
    'Due Date',
    'Penalty',
    'Current Payment',
    'Status', ''];

const showDrawer = ref(false)
const drawerData = ref({})
const isRejected = ref(false)

const props = defineProps({
    stallsCategories: Object,
    stallRentals: Object,
    filters: Object,
    auth: Object,
});

const form = reactive({ 
    search: props?.filters?.search,
    category: props?.filters?.category,
    status: props?.filters?.status,
});

const forms= useForm({ 
    remarks: '',
});

watch(
  form,
  throttle(() => {
    router.get(props?.auth.user?.role_id === 2 ? '/department/applications/stall-leasing' : `/admin/rental-management/stall-leasing`, pickBy(forms), { preserveState: true })
  }, 150),
  { deep: true }
);

function reset() {
    form.search = '';
    form.category = null;
    Object.assign(form, mapValues(form, () => null));
}

function getDetails(data) {
    drawerData.value = data;
    showDrawer.value = true;
}

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


const closeRejection = () => {
    isRejected.value = false;
    showDrawer.value = true;
};

const rejectPermits = () => {
    showDrawer.value = false;
    isRejected.value = true;
};

const approveConfirm = () => {
    delete forms.remarks;
    forms.put(route('department.applications.stalls.approve', drawerData?.value?.id));
    showDrawer.value = false;
};

const rejectConfirm = () => {
    console.log('ss')
    forms.put(
        route('department.applications.stalls.reject', drawerData?.value?.id),
        {
            onSuccess: () => {
                forms.reset()
                showDrawer.value = false;
                drawerData.value = {};
                isRejected.value = false;
            },
        }
    )
};

</script>
