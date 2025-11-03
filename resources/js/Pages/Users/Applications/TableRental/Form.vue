<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stall Types">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
               Stall Types
            </h2>
        </template>
         <h1 class="mb-8 text-3xl font-bold">
          <Link class="text-primary hover:text-secondary" :href="`/my-rentals/stall-leasing`">Table Spaces</Link>
          <span class="text-secondary font-medium">/</span>
          {{titlePage()}}
        </h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <!-- Stepper -->
            <div class="flex items-center justify-between mt-6" v-if="!checkIfEdit() && !checkIfReupload()">
              <!-- Step 1 -->
              <div class="flex flex-col items-center text-center w-1/3">
                <div
                  class="flex items-center justify-center w-12 h-12 rounded-full border-2"
                  :class="step === 1 ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-400'"
                >
                  <!-- User/Business Icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5.121 17.804A7.968 7.968 0 0112 15c1.933 0 3.683.69 5.121 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <span class="mt-2 text-sm" :class="step === 1 ? 'text-primary font-semibold' : 'text-gray-400'">Vendor Info</span>
              </div>

              <!-- Divider -->
              <div class="flex-1 border-t-2 mx-2" :class="step > 1 ? 'border-primary' : 'border-gray-200'"></div>

              <!-- Step 2 -->
              <div class="flex flex-col items-center text-center w-1/3">
                <div
                  class="flex items-center justify-center w-12 h-12 rounded-full border-2"
                  :class="step === 2 ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-400'"
                >
                  <!-- Document Icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 7h10M7 11h10M7 15h6M5 5v14h14V5H5z" />
                  </svg>
                </div>
                <span class="mt-2 text-sm" :class="step === 2 ? 'text-primary font-semibold' : 'text-gray-400'">Requirements</span>
              </div>

              <!-- Divider -->
              <div class="flex-1 border-t-2 mx-2" :class="step > 2 ? 'border-primary' : 'border-gray-200'"></div>

              <!-- Step 3 -->
              <div class="flex flex-col items-center text-center w-1/3">
                <div
                  class="flex items-center justify-center w-12 h-12 rounded-full border-2"
                  :class="step === 3 ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-400'"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32" height="32">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01M21 21l-6-6" />
                  </svg>



                </div>
                <span class="mt-2 text-sm" :class="step === 3 ? 'text-primary font-semibold' : 'text-gray-400'">Rental Agreement</span>
              </div>
              <div class="flex flex-col items-center text-center w-1/3">
                <div
                  class="flex items-center justify-center w-12 h-12 rounded-full border-2"
                  :class="step === 4 ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-400'"
                >
                  <!-- Payment Icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="32" height="32">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4M4 6h16M4 12h16M4 18h16" />
                  </svg>


                </div>
                <span class="mt-2 text-sm" :class="step === 4 ? 'text-primary font-semibold' : 'text-gray-400'">Finish Application</span>
              </div>
            </div>
            <form @submit.prevent="checkIfEdit() || checkIfReupload() ? update() : nextButton()">
              <!-- Step 1: Input Details -->
              <div v-if="!checkIfReupload() && (step === 1 || checkIfEdit())" class="p-6 space-y-6">
                <div class="flex flex-col md:flex-row gap-4 px-5">
                    <div class="w-full md:w-full">
                      <InputLabel for="Business Name" value="Business Name" />
                      <TextInput
                          id="business_name"
                          v-model="form.business_name"
                          type="text"
                          class="mt-1 block w-full"
                          required
                          autofocus
                          autocomplete="business_name"
                      />
                      <InputError class="mt-2" :message="form?.errors?.business_name" />
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4 px-5">
                  <div class="w-full md:w-full">
                    <InputLabel for="Add ons" value="Bulbs" />
                    <TextInput
                        id="bulbs"
                        v-model="form.bulb"
                        type="number"
                        class="mt-1 block w-full"
                        autofocus
                        autocomplete="bulbs"
                      />
                    <InputError class="mt-2" :message="form?.errors?.bulb" />
                  </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4 px-5">
                  <div class="w-full md:w-full">
                    <InputLabel for="gender_id" value="Table" class="mb-1 block w-full"/>
                    <SelectInput
                      v-model="form.stall_id"
                      id="stall_id"
                      required
                      :disabled="checkIfEdit()"
                      autocomplete="stall_id"
                    >
                      <option
                          v-for="stall in stallTypes"
                          :key="stall.id"
                          :value="checkIfEdit() ? tableRental?.stalls?.id : stall.id"
                      >{{ stall.name }}</option> 
                    </SelectInput>
                    <InputError class="mt-2" :message="form?.errors?.stall_id" />
                  </div>
                </div>

                <!-- Show stall info -->
                <div v-if="selectedStall" class="flex flex-col md:flex-row gap-4 px-5">
                  <div class="w-full md:w-full">
                      <div  class="border p-4 rounded bg-gray-50">
                        <h2 class="text-lg font-bold mb-2 text-secondary">Transient Details</h2>
                        <p><strong>Name:</strong> {{ selectedStall?.name }}</p>
                        <p><strong>Size:</strong> 
                          {{ selectedStall?.size ? `${selectedStall?.size.length}X${selectedStall?.size.width}` : '-' }}
                        </p>
                        <p><strong>Category:</strong> {{ selectedStall?.categories?.name }}</p>
                        <p><strong>Description:</strong> {{ selectedStall?.categories?.description }}</p>

                        <h3 class="mt-2 font-semibold text-secondary">Fees:</h3>
                        <ul class="ml-4 list-disc">
                          <li v-for="fee in selectedStall?.categories?.fee" :key="fee.id">
                            <strong>{{ fee.type }}:</strong> {{ feeDetails(fee) }}
                          </li>
                        </ul>
                      </div>
                  </div>
                </div>
              </div>

              <!-- Step 2: Requirements -->
              <div v-if="step === 2 || checkIfReupload()" class="p-6 space-y-6">
                <DocumentUpload
                    :requiredDocs="requirements?.stall_new"
                    v-model:files="localFileReq" 
                  />
                <InputError class="mt-2" :message="form.errors.requirements" />
              </div>

              <div v-if="!checkIfEdit() && !checkIfReupload()">
                <!-- Step 2: Requirements -->
                <div v-if="step === 3" class="p-6 space-y-6">
                  <h2 class="text-lg font-bold text-secondary mb-2">
                    Please review the rental agreement below and upload signature:
                  </h2>
                  <PdfMonitor
                    pdfUrl="/sample.pdf"
                  />
                  <SingleImageUpload v-model="signature" :width="120" :height="60" v-model:files="localFileSignature"/>
                </div>
  
                <div v-if="step === 4" class="p-6 space-y-6">
                  <h2 class="text-lg font-bold text-secondary mb-2">
                    Summary:
                  </h2>
                    <!-- Header -->
                    <div class="flex items-center justify-between">
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
                        {{$page?.props?.auth?.user?.status ? 'Active' : 'Not Active'}}
                        </span>
                    </div>
  
                    <!-- Info -->
                    <div class="flex-1">
                        <h2 class="text-lg font-semibold text-primary">{{ fullName($page?.props?.auth?.user) }}</h2>
                        <p class="text-sm text-gray-600">
                        Email: <a :href="'mailto:' + $page?.props?.auth?.user?.email" class="text-blue-600 hover:underline">{{ $page?.props?.auth?.user.email }}</a>
                        </p>
                        <p class="text-sm text-gray-600">Mobile: {{ $page?.props?.auth?.user.mobile }}</p>
                    </div>
                    </div>
  
                    <!-- Stall Info -->
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <h3 class="font-semibold text-secondary">Stall Details</h3>
                        <p class="text-gray-600">Stall Name: {{ selectedStall?.name }}</p>
                        <p class="text-gray-600">Size: {{ selectedStall?.size ? `${selectedStall?.size?.length}X${selectedStall?.size?.width} ` : '-'}}</p>
                        <p class="text-gray-600">Birthday: {{ formatDateShort($page?.props?.auth?.user.birthday) }}</p>
                        <p class="text-gray-600">Address: {{ $page?.props?.auth?.user.address }}</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-secondary">Category</h3>
                        <p class="text-gray-600">{{ selectedStall?.categories.name }}</p>
                    </div>
                    </div>
  
                    <!-- Payment Details -->
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                      <h3 class="font-semibold text-secondary">Payment Details</h3>
  
                      <ul class="space-y-2">
                        <li v-for="(doc, i) in total_payments?.breakdown" :key="i" class="flex justify-between items-center px-3 py-2 text-sm">
                          <div class="flex items-center space-x-2">
                            <span>
                              <strong class="text-secondary">{{doc?.size ? `• ${doc?.type}(${doc.size}): ` : `• ${doc?.type}: `}} </strong>
                              <span class="text-gray-600"> {{formatAmount(doc?.amount)}}</span>
                            </span>
                          </div>
                        </li>
                      </ul>
                      <p class="font-bold text-gray-600">
                          Calculated Quarterly Payment: <span class="text-green-600 font-semibold">{{ formatAmount(total_payments?.total) }}</span>
                      </p>
                    </div>
                </div>
              </div>
              <!-- Step navigation -->
              <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                <Link :href="checkIfEdit() ? `/my-rentals/stall-leasing/${tableRental?.id}` : '/my-rentals/stall-leasing'" class="text-red-600 hover:underline" tabindex="-1">Cancel</Link>
                
                <div class="ml-auto flex gap-2">
                  <div v-if="!checkIfEdit() && !checkIfReupload() || checkIfRenewal()">
                    <button
                      v-if="step > 1"
                      type="button"
                      class="px-4 py-2 mr-2 rounded-md bg-gray-200 hover:bg-gray-300"
                      @click="step--"
                    >
                      Back
                    </button>
                    <button
                      v-if="step < 4"
                      type="button"
                      class="px-4 py-2 rounded-md bg-primary text-white hover:bg-secondary"
                      @click="nextButton"
                    >
                      {{step === 3 ? 'Agree' : 'Next'}}
                    </button>
                  </div>
                  <loading-button
                    v-if="step === 4 || checkIfEdit() || checkIfReupload()"
                    :loading="form.processing"
                    class="bg-primary ml-auto"
                    type="submit"
                  >
                    {{checkIfEdit() ? 'Submit':'Continue'}}
                  </loading-button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <ConfirmationModal :show="isSubmit" @close="closeSubmit">
                <template #title>
                    Confirm
                </template>

                <template #content>
                    <div class="space-y-4 p-4">
                        <!-- Question -->
                        <p class="text-gray-700 text-sm md:text-base">
                            Do you confirm your intention to submit this request?
                            Please submit the duly accomplished agreement form, downloaded after this process, to the Office of Public Market.
                        </p>
                    </div>
                </template>

                <template #footer>
                    <div class="flex justify-end space-x-3 p-4">
                      <SecondaryButton @click="closeSubmit">
                          Cancel
                        </SecondaryButton>
                        <form @submit.prevent="checkIfRenewal() ? renewals(): submit()">
                       <!-- stores button -->
                         <loading-button class="bg-green-600 text-white ml-2 px-4 py-2 rounded-md  text-white hover:bg-green-700"  :disabled="isGenerating">
                            Submit
                         </loading-button>
                         <!-- Hidden contract filler -->
                          <Contract
                            ref="contractRef"
                            src="/sample.pdf"
                            :data="contractData"
                            :field-map="fieldMap"
                            file-name="contract-filled.pdf"
                            :signature="signature"
                            class="hidden"
                          />
                      </form>
                    </div>
                </template>
        </ConfirmationModal>
    </AppLayout>
</template>


<script setup>
import { defineProps, ref, watch, computed, onMounted } from 'vue';
import { Link, usePage, useForm, router } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

import { formatAmount, formatDateShort, formatFees, formatStallStatus, fullName, isEditPage, isRenewalPage, isReuploadPage, mapTablesToGeoJSON } from '@/data/helper';

import AppLayout from '@/Layouts/AppLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import DocumentUpload from '@/Shared/DocumentUpload.vue';
import PdfMonitor from '@/Shared/PdfMonitor.vue';
import Contract from '@/Shared/Contract.vue';
import SingleImageUpload from '@/Shared/SingleImageUpload.vue';
import MultiSelect from '@/Shared/MultiSelect.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Shared/SelectInput.vue';

const page = usePage();
const localFileReq = ref([]); 
const localFileSignature = ref([]); 
const step = ref(1);
const selectedStall = ref(null);
const contractData = ref({});
const signature = ref(null);
const isSubmit = ref(false);
const stallData = ref([]);
const isGenerating = ref(false); // spinner state
const contractRef = ref(null); 

const props = defineProps({
    stallTypes: Object,
    stallTypesAll: Object,
    tableRental: Object,
    requirements: Object,
    paymentDetails: Object,
    fees: Object,
    auth: Object,
    total_payments: Object
});

const checkIfEdit = () => {
  return isEditPage(page.url);
};

const checkIfReupload = () => {
  console.log('isReuploadPage(page.url)', isReuploadPage(page.url))
  return isReuploadPage(page.url);
};

const checkIfRenewal = () => {
  console.log('checkIfRenewal(page.url)', isRenewalPage(page.url))
  return isRenewalPage(page.url);
};

const titlePage = () => {
  if(checkIfReupload() || checkIfRenewal() || checkIfEdit()){
    return props?.tableRental?.name
  }

  return 'Create New'
};

const form = useForm({ 
  type: checkIfRenewal() ?  2 : 1,
  business_name: checkIfRenewal() || checkIfEdit() || checkIfReupload() ?  props?.tableRental?.name : '',
  stall_id: checkIfRenewal() || checkIfEdit() || checkIfReupload() ? props?.tableRental?.stalls?.id : '',
  requirements: [],
  acknowledgeContract: checkIfRenewal() || checkIfEdit() || checkIfReupload() ? props?.tableRental?.acknowledgeContract : '',
  reference_number: checkIfRenewal() || checkIfEdit() || checkIfReupload() ? props?.tableRental?.paymentRecord?.reference_number : '',
  attachment_signature: [],
  total_payment: checkIfRenewal() || checkIfEdit() || checkIfReupload() ? props?.tableRental?.paymentRecord?.total_payments : 0,
  fees: checkIfRenewal() || checkIfEdit() || checkIfReupload() ? Array.isArray(props?.tableRental?.fees_additional)
  ? props?.tableRental?.fees_additional.map(Number)
  : [] : [],
  step: step.value, 
  bulb: checkIfRenewal() || checkIfEdit() || checkIfReupload() ? props?.tableRental?.bulb : null,
  stall_rental_id: checkIfRenewal() || checkIfEdit() || checkIfReupload() ? props?.tableRental?.id : null,
});

// Field mapping across multiple pages
const fieldMap = {
  lesseeName: { page: 0, x: 160, y: 530 },
  lesseeNamePage2: { page: 2, x: 110, y: 370 },
  barangay: { page: 0, x: 320, y: 520 },
  stallNo: { page: 0, x: 300, y: 440 },
  // blockNo: { page: 0, x: 370, y: 440 },
  // section: { page: 0, x: 350, y: 530 },
  day: { page: 2, x: 120, y: 300 }, // goes on 2nd page
  month: { page: 2, x: 500, y: 310 }, // goes on 2nd page
  signature: { page: 2, x: 450, y: 200, w: 100, h: 50 }, // also on 2nd page
};

const initials = computed(() => {
  const names = props.auth?.user?.first_name.split(" ");
  return names.map((n) => n[0]).join("");
});

const submit = () => {
  form.requirements = checkIfEdit()
    ? props?.tableRental?.requirements.map(f => ({
        requirement_checklist_id: f.checklist_id,
        attachment: f.attachment,
      }))
    : localFileReq.value.map(f => ({
        requirement_checklist_id: f.documentType,
        attachment: f.file,
      }));

  form.attachment_signature = signature.value;
  form.total_payment = props?.total_payments?.total;
  form.acknowledgeContract = true;
  form.fees_additional = formatFees(props.fees, form.bulb);
  form.fees = formatFees(props.fees, form.bulb);
  isGenerating.value = true; // show spinner
  contractRef.value?.fillContract(); // wait for download
  if(isGenerating.value) {
    form.post(route("applications.table.store"), {
      preserveScroll: true,
  
      onSuccess: async () => {
        isGenerating.value = false; // hide spinner
        router.visit(route("applications.table.index"), {
          preserveScroll: true,
          preserveState: false,
        });
      },
  
      onError: (errors) => {
        console.error("Form errors:", errors);
      },
    });
  }
};

const renewals = async () => {
    if (localFileReq.value.some(file => file.status === 'uploading')) {
      form.errors.requirements = "Please wait until all files are uploaded.";
      return;
    }
    if (localFileReq.value.length === 0) {
      form.errors.requirements = "The requirements are required.";
      return;
    }

    // ✅ Build a FormData payload (always use FormData for file uploads)
    const fd = new FormData();

    // Laravel route expects PUT, so we spoof it
    fd.append('_method', 'PUT');

    // --- Append normal fields (add any others you need) ---
    fd.append('stall_id', form.stall_id || '');
    fd.append('business_name', form.business_name || '');
    fd.append('type', 2);
    fd.append('bulb', form.bulb || '');
    form.fees = formatFees(form.fees, form.bulb, 'edit');
    fd.append('total_payment', form.total_payment || '');
    fd.append('acknowledgeContract', form.acknowledgeContract ? 1 : 0);

    // --- Append requirements + files ---
    localFileReq.value.forEach((f, i) => {
      fd.append(`requirements[${i}][requirement_checklist_id]`, f.documentType);
      if (f.file instanceof File) {
        fd.append(`requirements[${i}][attachment]`, f.file);
      }
    });

    // --- Optional: append signature if exists ---
    if (form.attachment_signature instanceof File) {
      fd.append('attachment_signature', form.attachment_signature);
    }
    isGenerating.value = true; // show spinner
    contractRef.value?.fillContract(); // wait for download
    // ✅ Send request using POST (with _method spoofed as PUT)
    Inertia.post(route('applications.table.update', props?.tableRental?.id), fd, {
      preserveScroll: true,
      onStart: () => { isGenerating.value = true; },
      onSuccess: () => {
        router.visit(route("applications.table.index"), {
          preserveScroll: true,
          preserveState: false,
        });
      },
      onError: (errors) => {
        isGenerating.value = false;
        console.error("Form errors:", errors);
      },
    });
};

const update = () => {
  // check if we're in re-upload flow
  if (checkIfReupload()) {
    // 🧪 Basic validation before sending
    if (localFileReq.value.some(file => file.status === 'uploading')) {
      form.errors.requirements = "Please wait until all files are uploaded.";
      return;
    }
    if (localFileReq.value.length === 0) {
      form.errors.requirements = "The requirements are required.";
      return;
    }

    // ✅ Build a FormData payload (always use FormData for file uploads)
    const fd = new FormData();

    // Laravel route expects PUT, so we spoof it
    fd.append('_method', 'PUT');

    // --- Append normal fields (add any others you need) ---
    fd.append('stall_id', form.stall_id || '');
    fd.append('business_name', form.business_name || '');
    fd.append('type', form.type || '');
    fd.append('bulb', form.bulb || '');
    form.fees = formatFees(form.fees, form.bulb, 'edit');
    fd.append('total_payment', form.total_payment || '');
    fd.append('acknowledgeContract', form.acknowledgeContract ? 1 : 0);

    // --- Append requirements + files ---
    localFileReq.value.forEach((f, i) => {
      fd.append(`requirements[${i}][requirement_checklist_id]`, f.documentType);
      if (f.file instanceof File) {
        fd.append(`requirements[${i}][attachment]`, f.file);
      }
    });

    // --- Optional: append signature if exists ---
    if (form.attachment_signature instanceof File) {
      fd.append('attachment_signature', form.attachment_signature);
    }

    // ✅ Send request using POST (with _method spoofed as PUT)
    Inertia.post(route('applications.table.update', props?.tableRental?.id), fd, {
      preserveScroll: true,
      onStart: () => { isGenerating.value = true; },
      onSuccess: () => {
        isGenerating.value = false;
        router.visit(route("applications.table.index"), {
          preserveScroll: true,
          preserveState: false,
        });
      },
      onError: (errors) => {
        isGenerating.value = false;
        console.error("Form errors:", errors);
      },
    });

  } else {
    // ✅ Normal update without file re-upload
    const fd = new FormData();
    fd.append('_method', 'PUT');
    fd.append('stall_id', form.stall_id || '');
    fd.append('business_name', form.business_name || '');
    fd.append('type', 1);
    fd.append('bulb', form.bulb || 0);
    fd.append('total_payment', props?.total_payments?.total || 0);
    fd.append('acknowledgeContract', form.acknowledgeContract ? 1 : 0);


    if(checkIfRenewal()){
      fd.append('type', 2);

      if (localFileReq.value.some(file => file.status === 'uploading')) {
        form.errors.requirements = "Please wait until all files are uploaded.";
        return;
      }
      if (localFileReq.value.length === 0) {
        form.errors.requirements = "The requirements are required.";
        return;
      }

      // --- Append requirements + files ---
      localFileReq.value.forEach((f, i) => {
        fd.append(`requirements[${i}][requirement_checklist_id]`, f.documentType);
        if (f.file instanceof File) {
          fd.append(`requirements[${i}][attachment]`, f.file);
        }
      });

    }

    if (form.attachment_signature instanceof File) {
      fd.append('attachment_signature', form.attachment_signature);
    }

    

    Inertia.post(route('applications.table.update', props?.tableRental?.id), fd, {
      preserveScroll: true,
      onStart: () => { isGenerating.value = true; },
      onSuccess: () => {
        isGenerating.value = false;
        contractRef.value?.fillContract();
        router.visit(route("applications.table.index"), {
          preserveScroll: true,
          preserveState: false,
        });
      },
      onError: (errors) => {
        isGenerating.value = false;
        console.error("Form errors:", errors);
      },
    });
  }
};

const feeDetails = (fee) => {
  return `${formatAmount(fee.amount)} per table`
}

const nextButton = () => {
  if (step.value === 1) {
    if(checkIfRenewal()){
      form.put(route('applications.table.renew', props?.tableRental?.id), {
      onSuccess: () => {
          step.value++;
        }
      })
    }
    else {
      form.fees = formatFees(props.fees, form.bulb);
      form.post(route("applications.table.store"), {
        preserveState: true,
        onSuccess: () => {
          step.value++;
        }
      })
    }
  } 
  else if (step.value === 2) {
      const totalRequired = props?.requirements?.stall_new?.length || 0;
      const uploaded = localFileReq.value.filter(file => file.status === "uploaded").length;
      const uploading = localFileReq.value.some(file => file.status === "uploading");

      console.log("Required:", totalRequired, "Uploaded:", uploaded, "Uploading:", uploading);

      // Validation logic
      if (uploading) {
        form.errors.requirements = "Please wait until all files are uploaded.";
      } 
      else if (uploaded < totalRequired) {
        form.errors.requirements = "Please upload all required documents.";
      } 
      else if (uploaded === 0) {
        form.errors.requirements = "The requirements are required.";
      } 
      else {
        form.errors.requirements = null;
        step.value++;
      }
  }
  else if (step.value === 3) {
    localFileSignature.value = signature.value,
    step.value++;

  }
  else if(step.value === 4) {
    form.step = step.value;
    console.log('checkIfRenewal', checkIfRenewal());
    contractData.value = {
      lesseeName: fullName(props?.auth?.user),
      lesseeNamePage2: fullName(props?.auth?.user),
      barangay: props?.auth?.user?.address,
      stallNo: selectedStall?.value?.name ?  selectedStall?.value?.name : '',
      signature: signature.value,
      day: new Date().getDate(),
      month: new Date().getMonth() + 1,
    };
    isSubmit.value = true;
  }
};

const onStallSelect = (stallId) => {
  if (!stallId) {
    selectedStall.value = null;
    return;
  }
  const found = props.stallTypesAll.find((s) => s.id === stallId);
  console.log("Selected Stall:", found);
  selectedStall.value = found || null;
};

const closeSubmit = () => {
    isSubmit.value = false;
};

// Initialize from props
stallData.value = props.stallsData || [];

// Watch for prop changes
watch(
  () => form.stall_id,
  (newId) => {
    onStallSelect(newId);
  },
  { immediate: true } // run it on load too
);


onMounted(() => {
  if (checkIfEdit() || checkIfRenewal()) {
    onStallSelect(props?.tableRental?.stalls?.id);
  }
});

</script>

