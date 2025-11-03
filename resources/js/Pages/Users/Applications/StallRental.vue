<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stalls">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stall
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold text-primary">Stalls Leasing</h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <FlashMessage v-if="$page.props.flash.success" :message="$page.props.flash.success" type="success" />
            <FlashMessage v-if="$page.props.flash.error" :message="$page.props.flash.error" type="error" />
            <div class="flex items-center justify-between mb-6">
                <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
                    <label class="block text-gray-700">Type:</label>
                    <select v-model="form.type" class="form-select mt-1 w-full">
                        <option :value="null">All</option>
                        <option
                            v-for="stat in statusOptions"
                            :key="stat.id"
                            :value="stat.id"
                        >{{ stat.name }}</option> 
                    </select>
                </search-filter>
                <Link type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                :href="`/my-rentals/stall-leasing/create`" class="bg-btn-gradient text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary-700 transition-colors">Rent New</Link>
            </div>
            <BaseTable :headers="headers" :data="stallRentals?.data" :class="'center text-center sticky right-0 bg-white z-10'">
                <template #row="{ data }">
                    <tr v-for="(bsn, i) in data" :key="stallRentals.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 capitalize text-center">{{bsn?.permits?.permit_number ? `${bsn?.permits?.permit_number }`: '-'}}</td>
                        <td class="px-4 py-3 capitalize text-center">{{`${fullName(bsn?.vendor)}`}}</td>
                        <td class="px-4 py-3 capitalize text-center">{{ bsn.name }}</td>
                        <td class="px-4 py-3 capitalize text-center">{{ bsn?.stalls?.name }}</td>
                        <td class="px-4 py-3 capitalize text-center">{{bsn?.permits?.issued_date && bsn?.permits?.expiry_date ? `${formatDate(bsn?.permits?.issued_date)} - ${formatDate(bsn.permits?.expiry_date)}` : '-' }}</td>
                        <td class="px-4 py-3 capitalize text-center">
                            <Badge :color="bsn?.permits?.type === 'new' ? 'green' : 'blue'">
                                {{ bsn?.permits?.type }}
                             </Badge>
                        </td>

                        <td class="px-4 py-3 capitalize text-center">{{formatAmount(bsn?.quarterly_payment)}}</td>
                        <td class="px-4 py-3 capitalize text-center">{{ bsn?.next_payment_due ? formatDate(new Date(bsn?.next_payment_due)) : 'N/A'}}</td>
                        <td class="px-4 py-3 capitalize text-center">{{formatAmount(bsn?.penalty)}}</td>
                        <td class="px-4 py-3 capitalize text-center">
                            <p class="font-semibold text-blue-600" v-if="bsn?.current_payment === 'Paid'">Paid</p>

                            <button
                                v-if="bsn?.current_payment === 'Not Paid'"
                                :disabled="bsn?.permits?.status !== 1"
                                class="inline-block bg-blue-600 text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary-700 transition-colors"
                                @click="payNow(bsn)"
                                tabindex="-1"
                            >
                                Pay Now
                            </button>
                        </td>

                        <td class="px-4 py-3 capitalize text-center">
                             <Badge :color="status(bsn.permits.status).color">
                                {{ status(bsn.permits.status).status }}
                             </Badge>
                        </td>
                        <td class="px-4 py-3 capitalize">
                            <Link class="flex items-center pr-2" :href="`/my-rentals/stall-leasing/${bsn.id}`" tabindex="-1">
                                <icon name="cheveron-right" class="block w-6 h-6 fill-primary" />
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="stallRentals?.total === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="stallRentals?.links" />
        </div>
        <ConfirmationModal :show="isPayment" @close="cancelPayment">
                <template #title>
                    Pay Now
                </template>
                <div>
                    
                </div>
                <template #content>
                    <div class="space-y-4 p-4 text-left">
                        <p class="text-gray-600">Please complete your payment below:</p>
                        <div class="flex justify-center mb-4">
                            <Gcash class="h-auto" />
                        </div>
                       <div class="flex flex-col md:flex-row gap-4 mt-5">

                           <div class="w-full">
                                <InputLabel for="receipt" value="Receipt" class="mb-1 block w-full"/>
                                <SingleFileUpload
                                v-model="formData.receipt"
                                label="Upload Official Receipt"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="mt-1 block w-full"
                                :error="formData?.errors?.receipt"
                                />
                                <InputError class="mt-2" :message="formData?.errors?.receipt" />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-4 mt-5">
                            <div class="w-full">
                                <InputLabel for="amount" value="Amount" />
                                <TextInput
                                id="amount"
                                v-model="formData.amount"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="amount"
                                />
                                <InputError class="mt-2" :message="formData?.errors?.amount" />
                            </div>
                            <div class="w-full">
                                <InputLabel for="reference_number" value="Reference Number" />
                                <TextInput
                                id="reference_number"
                                v-model="formData.reference_number"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                autocomplete="reference_number"
                                />
                                <InputError class="mt-2" :message="formData?.errors?.reference_number" />
                            </div>
                        </div>
                    </div>
                </template>

                <template #footer>
                    <div class="flex justify-end space-x-3 p-4">
                      
                        <SecondaryButton @click="cancelPayment">
                          Cancel
                        </SecondaryButton>
                      <form @submit.prevent="submit">

                       <!-- Submit button -->
                         <loading-button  
                            :loading="form.processing"
                            class="bg-primary ml-auto"
                            type="submit">
                            Submit
                         </loading-button>
                      
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
    Link,
    router,
    useForm
} from '@inertiajs/vue3';
import { throttle } from 'lodash'
import pickBy from 'lodash/pickBy'
import { jsPDF } from "jspdf";
import autoTable from 'jspdf-autotable';

import AppLayout from '@/Layouts/AppLayout.vue'
import BaseTable from '@/Shared/BaseTable.vue';
import Pagination from '@/Shared/Pagination.vue';
import Icon from '@/Shared/Icons.vue'
import { formatAmount, formatDate, fullName } from '@/data/helper';
import Badge from '@/Shared/Badge.vue';
import FlashMessage from '@/Shared/FlashMessage.vue';
import SearchFilter from '@/Shared/SearchFilter.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SingleFileUpload from '@/Shared/SingleFileUpload.vue';
import LoadingButton from '@/Shared/LoadingButton.vue'
import Gcash from '@/Shared/Gcash.vue';

const isPayment = ref(false);
const payee = ref(null);
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
    'Status',
    ''];


const props = defineProps({
    stallRentals: Object,
    filters: Object
});

const form = reactive({ 
    search: props?.filters?.search,
    type: props?.filters?.type,
});

const formData = useForm({ 
  receipt: null,
  reference_number: '',
  amount: 0,
  stall_rental_id: null,
});

watch(
  form,
  throttle(() => {
    router.get(`/my-rentals/stall-leasing`, pickBy(form), { preserveState: true })
  }, 150),
  { deep: true }
);

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

const statusOptions = [
  { id: 1, name: "New" },
  { id: 2, name: "Renewal" },
];

function reset() {
    form.search = '';
    form.type = null;
    Object.assign(form, mapValues(form, () => null));
}

function cancelPayment() {
    isPayment.value = false;
}

function payNow(data) {
    payee.value = data;
    formData.amount = data?.quarterly_payment;
    formData.stall_rental_id = Number(data?.id);
    isPayment.value = true;
}

const generateInvoicePDF = (data) => {
  const doc = new jsPDF();

  // Title
  doc.setFontSize(18);
  doc.text("MARKETMIS - Payment Invoice", 14, 20);

  // Basic details
  doc.setFontSize(12);
  doc.text(`Invoice Date: ${data.date}`, 14, 35);
  doc.text(`Transaction ID: ${data.transaction_id}`, 14, 42);
  doc.text(`Stall Holder: ${data.applicant_name}`, 14, 49);
  doc.text(`Payment Method: ${data.payment_method}`, 14, 56);

  // Add table
  autoTable(doc, {
    startY: 70,
    head: [["Description", "Amount"]],
    body: [["Stall Rental Payment", `${formatAmount(data.amount)}`]],
    theme: "grid",
  });

  // Total
  doc.setFontSize(14);
  doc.text(`Total: ${formatAmount(data.amount)}`, 150, doc.lastAutoTable.finalY + 10);

  // Footer
  doc.setFontSize(10);
  doc.text("Thank you for your payment!", 14, doc.lastAutoTable.finalY + 20);
  doc.text("Generated by MarketMIS", 14, doc.lastAutoTable.finalY + 26);

  // Download PDF
  doc.save(`Invoice-${data.transaction_id}.pdf`);
};
const submit = () => {
  formData.post(route("applications.payment"), {
    onSuccess: () => {
        generateInvoicePDF({
            applicant_name: fullName(payee.value?.vendor),
            transaction_id: `TXN-${Date.now()}`,
            amount: payee.value.quarterly_payment,
            date: new Date().toLocaleDateString(),
            payment_method: 'Lizbiz Pay',
        });
      formData.reset();
    isPayment.value = false;
    }
  })
};

</script>
