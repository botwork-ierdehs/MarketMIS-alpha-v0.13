<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stalls">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
               Stalls
            </h2>
        </template>
         <h1 class="mb-8 text-3xl font-bold">
          <Link class="text-primary hover:text-secondary" :href="`/admin/system-setting/rental-space`">Rental Space</Link>
          <span class="text-secondary font-medium">/</span>
          {{checkIfEdit() ? `${props?.stall.name}` : 'Create New'}}
        </h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="stores">
              <div class="flex flex-col md:flex-row gap-4 p-5">
                <div class="w-full md:w-1/2">
                    <InputLabel for="Name" value="Name" />
                    <TextInput
                        id="Name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form?.errors?.name" />
                </div>
                <div class="w-full md:w-1/2">
                  <InputLabel for="stall_category_id" value="Category" class="mb-1 block w-full"/>
                  <SelectInput
                    v-model="form.stall_category_id"
                    id="stall_category_id"
                    required
                    autocomplete="stall_category_id"
                  >
                    <option
                        v-for="cat in stallsCategories"
                        :key="cat.id"
                        :value="cat.id"
                    >{{ cat.name }}</option> 
                  </SelectInput>
                  <InputError class="mt-2" :message="form?.errors?.stall_category_id" />
                </div>
              </div>
              <div class="flex flex-col md:flex-row gap-4 px-5">
                <div class="w-full md:w-1/2">
                  <InputLabel for="width" value="Size - Length" />
                  <TextInput
                      id="length"
                      v-model="form.length"
                      type="number"
                      class="mt-1 block w-full"
                      placeholder = "Length"
                      autofocus
                      :disabled="!form.stall_category_id || categoryNotStall()"
                      autocomplete="length"
                  />
                  <InputError class="mt-2" :message="form?.errors?.length" />
                </div>
                <div class="w-full md:w-1/2">
                  <InputLabel for="width" value="Size - Width" />
                  <TextInput
                      id="width"
                      v-model="form.width"
                      type="number"
                      class="mt-1 block w-full"
                      placeholder = "Width"
                      autofocus
                      :disabled="!form.stall_category_id || categoryNotStall()"
                      autocomplete="width"
                  />
                  <InputError class="mt-2" :message="form?.errors?.width" />
                </div>
              </div>
              <div class="flex flex-col md:flex-row gap-4 p-5">
                <div class="w-full md:w-1/2">
                  <InputLabel for="width" value="Coordinates - Longitude" />
                  <TextInput
                      id="long"
                      v-model="form.long"
                      type="number"
                      class="mt-1 block w-full"
                      placeholder = "Longitude"
                      autofocus
                      :disabled="!form.stall_category_id || categoryNotStall()"
                      autocomplete="long"
                  />
                  <InputError class="mt-2" :message="form?.errors?.long" />
                </div>
                <div class="w-full md:w-1/2">
                  <InputLabel for="length" value="Coordinates - Latitude" />
                  <TextInput
                      id="la"
                      v-model="form.lat"
                      type="number"
                      class="mt-1 block w-full"
                      placeholder = "Latitude"
                      autofocus
                      :disabled="!form.stall_category_id || categoryNotStall()"
                      autocomplete="lat"
                  />
                  <InputError class="mt-2" :message="form?.errors?.lat" />
                </div>
              </div>
              <div class="flex flex-col gap-4 p-5">
                <div class="w-full">
                    <InputLabel for="location_description" value="Location Description" />
                      <textarea
                        id="remarks"
                        v-model="form.location_description"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                            focus:border-red-500 focus:ring-red-500 sm:text-sm resize-none"
                    ></textarea>
                    <InputError class="mt-2" :message="form?.errors?.location_description" />
                </div>
              </div>
              <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                <Link href="/admin/system-setting/rental-space" class="text-red-600 hover:underline" tabindex="-1">Cancel</Link>
                <loading-button :loading="form.processing" class="bg-primary ml-auto" type="submit">Save</loading-button>
              </div>
          </form>
        </div>
        </div>
    </AppLayout>
</template>


<script setup>
import { defineProps, watch, ref } from 'vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { formatAmount, isEditPage } from '@/data/helper';
import AppLayout from '@/Layouts/AppLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import SelectInput from '@/Shared/SelectInput.vue';
const page = usePage();
const selectedCategory = ref(null);

const props = defineProps({
    stallsCategories: Object,
    stall: Object,
    feeMasterlist: Object,
});

const checkIfEdit = () => {
  return isEditPage(page.url);
};


const form = useForm({ 
  name: checkIfEdit() ? props?.stall?.name : '',
  coordinates: '',
  lat: checkIfEdit() && props?.stall?.coordinates ? JSON.parse(props?.stall?.coordinates)[0] : '',
  long: checkIfEdit() && props?.stall?.coordinates ? JSON.parse(props?.stall?.coordinates)[1] : '',
  length: checkIfEdit() ? JSON.parse(props?.stall?.size)?.length : '',
  width: checkIfEdit() ? JSON.parse(props?.stall?.size)?.width : '',
  size: '',
  stall_category_id: checkIfEdit() ? props?.stall?.stall_category_id : '',
  is_transient: checkIfEdit() ? props?.stall?.is_transient : false,
  location_description: checkIfEdit() ? props?.stall?.location_description : '',
});

const getAreaOfSqrMeter = () => { 
    form.size = JSON.stringify({
        length: Number(form.length),
        width: Number(form.width)
    })
};

const getMapCoordinates = () => { 
    form.coordinates = JSON.stringify([Number(form.lat), Number(form.long)])
};

const categoryNotStall = () => { 
  const values = props?.stallsCategories?.find(cat => cat.id == form.stall_category_id);
  const result = values?.is_table_rental || values?.is_transient;
  if(result){
    form.length = '';
    form.width = '';
    form.long = '';
    form.lat = '';
    form.size = '';
    form.coordinates = '';
  }
  return result;
};

watch(
  () => [form.length, form.width, form.long, form.lat, form.stall_category_id],
  () => {
    getAreaOfSqrMeter();
    getMapCoordinates();
    categoryNotStall();
  }
);


const submit = () => {
    delete form.width;
    delete form.length;
    delete form.long;
    delete form.lat;
    form.post(route('admin.stalls.store'));
};

const update = () => {
  delete form.width;
  delete form.length;
  delete form.long;
  delete form.lat;
  form.put(route('admin.stalls.update', props?.stall?.id));
};

const stores = () => {
  const edit = checkIfEdit();
  if(edit){
    return update();
  }

  return submit();
}
</script>

