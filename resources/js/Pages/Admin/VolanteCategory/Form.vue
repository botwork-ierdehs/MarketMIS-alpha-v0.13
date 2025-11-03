<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Volante Types">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
               Volante Types
            </h2>
        </template>
         <h1 class="mb-8 text-3xl font-bold">
          <Link class="text-primary hover:text-secondary" :href="`/admin/system-setting/volante-types`">Volante Types</Link>
          <span class="text-secondary font-medium">/</span>
          {{checkIfEdit() ? `${props?.volanteCategories?.name}` : 'Create New'}}
        </h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="stores">
              <div class="flex flex-col md:flex-row gap-4 p-5">
                <div class="w-full">
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
              </div>
              <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                <Link href="/admin/system-setting/volante-types" class="text-red-600 hover:underline" tabindex="-1">Cancel</Link>
                <loading-button :loading="form.processing" class="bg-primary ml-auto" type="submit">Save</loading-button>
              </div>
          </form>
        </div>
        </div>
    </AppLayout>
</template>


<script setup>
import { defineProps, watch } from 'vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { isEditPage } from '@/data/helper';
import AppLayout from '@/Layouts/AppLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const page = usePage();

const props = defineProps({
    volanteCategories: Object,
});

const checkIfEdit = () => {
  return isEditPage(page.url);
};


const form = useForm({ 
  name: checkIfEdit() ? props?.volanteCategories?.name : '',
});



const submit = () => {
    form.post(route('admin.volante.type.store'));
};

const update = () => {
  delete form.email;
  form.put(route('admin.volante.type.update', props?.volanteCategories?.id));
};

const stores = () => {
  const edit = checkIfEdit();
  if(edit){
    return update();
  }

  return submit();
}
</script>

