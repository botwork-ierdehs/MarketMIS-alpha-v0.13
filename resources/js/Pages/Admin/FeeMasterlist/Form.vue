<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stall Types">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
               Stall Types
            </h2>
        </template>
         <h1 class="mb-8 text-3xl font-bold">
          <Link class="text-primary hover:text-secondary" :href="`/admin/system-setting/fees`">Fees</Link>
          <span class="text-secondary font-medium">/</span>
          {{checkIfEdit() ? `${props?.feeMasterlist?.type}` : 'Create New'}}
        </h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="stores">
              <div class="flex flex-col md:flex-row gap-4 p-5">
                <div class="w-full md:w-full">
 
                  <InputLabel for="Name" value="Name" />
                  <TextInput
                      id="Name"
                      v-model="form.type"
                      type="text"
                      class="mt-1 block w-full"
                      required
                      autofocus
                      autocomplete="name"
                  />
                  <InputError class="mt-2" :message="form?.errors?.name" />
                </div>
              </div>
              <div class="flex flex-col md:flex-row gap-4 p-5">
                <div class="w-full md:w-full">
                  <InputLabel for="Amount" value="Amount" />
                  <TextInput
                      id="Amount"
                      v-model="form.amount"
                      type="text"
                      class="mt-1 block w-full"
                      required
                      autofocus
                      autocomplete="amount"
                  />
                  <InputError class="mt-2" :message="form?.errors?.amount" />
                </div>
              </div>
              <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                <Link href="/admin/system-setting/fees" class="text-red-600 hover:underline" tabindex="-1">Cancel</Link>
                <loading-button :loading="form.processing" class="bg-primary ml-auto" type="submit">Save</loading-button>
              </div>
          </form>
        </div>
        </div>
    </AppLayout>
</template>


<script setup>
import { defineProps, ref, watch } from 'vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { isEditPage } from '@/data/helper';
import AppLayout from '@/Layouts/AppLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import SelectInput from '@/Shared/SelectInput.vue';
import Radio from '@/Shared/Radio.vue';

const page = usePage();

const props = defineProps({
    feeMasterlist: Object
});

const checkIfEdit = () => {
  return isEditPage(page.url);
};

const selectedFeeMasterlist = ref(null)
const selectedTransient = ref(null)

const form = useForm({ 
    type: props?.feeMasterlist?.type || '',
    amount: props?.feeMasterlist?.amount || '',
    is_daily: props?.feeMasterlist?.is_daily || false,
    is_monthly: props?.feeMasterlist?.is_monthly || false,
    is_per_kilo: props?.feeMasterlist?.is_per_kilo || false,
    is_styro: props?.feeMasterlist?.is_styro || false,
    selectedFeeMasterlist: '',
});

const schedOptions = [
  { label: 'Daily', value: true, key: 'is_daily' },
  { label: 'Monthly', value: true, key: 'is_monthly' },

];

const transientOptions = [
  { label: 'Per Kilo', value: true, key: 'is_per_kilo' },
  { label: 'Per Styrofoam', value: true, key: 'is_styro' },

];

const update = () => {
  form.put(route('admin.fees.update', props?.feeMasterlist?.id));
};

// watch(() => selectedFeeMasterlist.value, (newValue) => {
//   console.log(newValue);
//   form[newValue.key] = newValue.value;
// });

// watch(() => selectedTransient.value, (newValue) => {
//   form[newValue.key] = newValue.value;
// });

const stores = () => {
    return update();
}
</script>

