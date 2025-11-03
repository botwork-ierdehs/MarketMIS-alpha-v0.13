<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{getPageName()}}
            </h2>
        </template>
         <h1 class="mb-8 text-3xl font-bold">
          <Link class="text-primary hover:text-secondary" :href="`/admin/users?role=${getRouteName()}`">{{getPageName()}}</Link>
          <span class="text-secondary font-medium">/</span>
          {{checkIfEdit() ? `${props?.user?.first_name} ${props?.user?.last_name}` : 'Create New'}}
        </h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="stores">
              <div class="flex flex-col md:flex-row gap-4 p-5">
                <div class="w-full md:w-1/2">
                  <InputLabel for="first_name" value="First Name" />
                  <TextInput
                      id="first_name"
                      v-model="form.first_name"
                      type="text"
                      class="mt-1 block w-full"
                      required
                      autofocus
                      autocomplete="first_name"
                  />
                  <InputError class="mt-2" :message="form?.errors?.first_name" />
                </div>
                <div class="w-full md:w-1/2">
                  <InputLabel for="middle_name" value="Middle Name" />
                  <TextInput
                      id="middle_name"
                      v-model="form.middle_name"
                      type="text"
                      class="mt-1 block w-full"
                      autofocus
                      autocomplete="middle_name"
                  />
                  <InputError class="mt-2" :message="form?.errors?.first_name" />
                </div>
                <div class="w-full md:w-1/2">
                  <InputLabel for="last_name" value="Last Name" />
                  <TextInput
                      id="last_name"
                      v-model="form.last_name"
                      type="text"
                      class="mt-1 block w-full"
                      required
                      autofocus
                      autocomplete="last_name"
                  />
                  <InputError class="mt-2" :message="form?.errors?.first_name" />
                </div>
              </div>
              <div class="flex flex-col md:flex-row gap-4 px-5">
                <div class="w-full md:w-1/2">
                  <InputLabel for="birthDay" value="Date of Birth" class="mb-1 block w-full"/>
                  <DatePick
                    v-model="form.birthDay"
                    id="birthDay"
                    required
                    autofocus
                    autocomplete="birthDay"
                  />
                  <InputError class="mt-2" :message="form?.errors?.birthDay" />
                </div>
                <div class="w-full md:w-1/2">
                  <InputLabel for="gender_id" value="Gender" class="mb-1 block w-full"/>
                  <SelectInput
                    v-model="form.gender_id"
                    id="gender_id"
                    required
                    autocomplete="gender_id"
                  >
                    <option
                        v-for="gender in genders"
                        :key="gender.id"
                        :value="gender.id"
                    >{{ gender.name }}</option> 
                  </SelectInput>
                  <InputError class="mt-2" :message="form?.errors?.gender_id" />
                </div>
                <div class="w-full md:w-1/2" v-if="getRouteName() === 'sub-admin'">
                    <InputLabel for="department" value="Department" class="mb-1 block w-full"/>
                    <SelectInput
                    v-model="form.department_id"
                    id="department"
                    required
                    autocomplete="department"
                    >
                    <option
                        v-for="department in departments"
                        :key="department.id"
                        :value="department.id"
                    >{{ department.name }}</option> 
                    </SelectInput>
                    <InputError class="mt-2" :message="form?.errors?.department_id" />
                </div>
              </div>
            <div class="mt-4 flex flex-col md:flex-row gap-4 px-5">
                <div class="w-full md:w-full">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        :required="!checkIfEdit()"
                        :disabled ="checkIfEdit()"
                        autocomplete="email"
                    />
                    <InputError class="mt-2" :message="form?.errors?.email" />
                </div>
                <div class="w-full md:w-full">
                    <InputLabel for="mobile" value="Mobile Number" />
                    <TextInput
                        id="mobile"
                        v-model="form.mobile"
                        type="number"
                        class="mt-1 block w-full"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form?.errors?.mobile" />
                </div>
              </div>

              <div class="flex flex-col md:flex-row gap-4 p-5">
                <div class="w-full">
                <InputLabel for="address" value="Address" />
                <SelectInput v-model="form.address" id="address" required class="w-full">
                  <option
                    v-for="stall in barangays.barangays"
                    :key="stall.name"
                    :value="stall.name"
                  >
                    {{ stall.name }}
                  </option>
                </SelectInput>
                <InputError :message="form.errors.address" />
                </div>
              </div>
              <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                <Link :href="`/admin/users?role=${getRouteName()}`" class="text-red-600 hover:underline" tabindex="-1">Cancel</Link>
                <loading-button :loading="form.processing" class="bg-primary ml-auto" type="submit">Save</loading-button>
              </div>
          </form>
        </div>
        </div>
    </AppLayout>
</template>


<script setup>
import { defineProps } from 'vue';
import { Link, usePage, useForm } from '@inertiajs/vue3';
import { formatDateShort, isEditPage } from '@/data/helper';
import barangays from '@/data/barangays.json';
import AppLayout from '@/Layouts/AppLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import DatePick from '@/Shared/DatePick.vue';
import SelectInput from '@/Shared/SelectInput.vue';
import { ROLE_ID } from '@/data/data';
const page = usePage();

const props = defineProps({
    user: Object,
    genders: Object,
    departments: Object
});

const checkIfEdit = () => {
  return isEditPage(page.url);
};

const getRouteName = () => {
  const query = new URLSearchParams(window.location.search);
  const myParam = query.get('role');
  return myParam;
};

const roleId = () => {
  const myParam = getRouteName();
  if(myParam === 'inspector') {
    return ROLE_ID.USER;
  }
  return ROLE_ID.SUB_ADMIN;
};

const form = useForm({ 
  first_name: checkIfEdit() ? props?.user?.first_name : '',
  middle_name: checkIfEdit() ? props?.user?.middle_name : '',
  last_name: checkIfEdit() ? props?.user?.last_name : '',
  email: checkIfEdit() ? props?.user?.email : '',
  mobile: checkIfEdit() ? props?.user?.mobile : '',
  address: checkIfEdit() ? props?.user?.address : '',
  birthDay: checkIfEdit() ? formatDateShort(props?.user?.birthDay) : '',
  status: checkIfEdit() ? props?.user?.email_verified_at : '',
  gender_id: checkIfEdit() ? props?.user?.gender_id : '',
  role_id: checkIfEdit() ? props?.user?.role_id : roleId(),
  department_id: checkIfEdit() ? props?.user?.department_id : null,
  user_type_id: checkIfEdit() ? props?.user?.user_type_id : getRouteName() === 'inspector' ? 3 : null,
});



const getPageName = () => {
    let pageName;
    const myParam = getRouteName();
    switch (myParam.toLowerCase()) {
        case 'sub-admin':
            pageName = 'Admins'
            break;
        case 'inspector':
            pageName = 'Inspectors'
        break;  
        default:
            pageName = 'Vendors'
            break;
    }
    return pageName;
};

const submit = () => {
    form.post(route('admin.users.store'), {
        onFinish: () => form.reset(
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile',
            'address',
            'birthday',
            'gender_id',
            'sub_admin_type_id',
            'user_type_id',
            'department_id'
        )
    });
};

const update = () => {
  delete form.email;
  form.put(route('admin.users.update', props?.user?.id), {
    onFinish: () => form.reset(
      'first_name',
      'middle_name',
      'last_name',
      'email',
      'mobile',
      'address',
      'birthday',
      'gender_id',
      'sub_admin_type_id',
      'user_type_id',
      'department_id'
    )
  });
};

const stores = () => {
  const edit = checkIfEdit();
  if(edit){
    return update();
  }

  return submit();
}
</script>

