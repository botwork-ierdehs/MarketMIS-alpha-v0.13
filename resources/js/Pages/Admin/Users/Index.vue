<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Users">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Users
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold text-primary">{{getPageName()}}</h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             <FlashMessage v-if="$page.props.flash.success" :message="$page.props.flash.success" type="success" />
             <FlashMessage v-if="$page.props.flash.error" :message="$page.props.flash.error" type="error" />
            <div class="flex items-center justify-between mb-6">
             <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
                <label class="block text-gray-700">Gender:</label>
                <select v-model="form.gender" class="form-select mt-1 w-full">
                    <option :value="null">All</option>
                    <option :value="1">Female</option>
                    <option :value="2">Male</option>
                    <option :value="3">Other</option>
                </select>
            </search-filter>
                <Link type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                :href="`/admin/users/create?role=${props?.filters?.role}`" class="bg-btn-gradient text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary-700 transition-colors" v-if=" props?.filters?.role !== 'vendor'">{{props?.filters?.role === 'inspector' ? 'Add Inspector' : 'Add Admin'}}</Link>
            </div>
            <BaseTable :headers="headers" :data="users?.data">
                <!-- Optional: Customize header -->
                <template #header>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Mobile</th>
                    <th class="px-4 py-3 text-left">Address</th>
                    <th class="px-4 py-3 text-left">Birthday</th>
                    <th class="px-4 py-3 text-left">Gender</th>
                    <th class="px-4 py-3 text-left">Status</th>
                </template>

                <!-- Optional: Customize rows -->
                <template #row="{ data }">
                    <tr v-for="(user, i) in data" :key="user.id" class="hover:bg-gray-50" >
                        <td class="px-4 py-3 capitalize">{{ user.name }}</td>
                        <td class="px-4 py-3">{{ user.email }}</td>
                        <td class="px-4 py-3 capitalize">{{ user.mobile }}</td>
                        <td class="px-4 py-3 capitalize">{{ user.address }}</td>
                        <td class="px-4 py-3 capitalize">{{ formatDate(user.birthDay) }}</td>
                        <td class="px-4 py-3 capitalize">{{ user?.genders?.name }}</td>
                        <td class="px-4 py-3 capitalize">
                             <Badge :color="user?.status ? 'green' : 'red'">
                                {{ user?.status ? 'Active': 'Not Active' }}
                             </Badge>
                        </td>
                        <td class="px-4 py-3 capitalize" v-if="checkDateStatus(new Date(user?.joinDate)) && !user?.status">
                           <Link
                            method="post"
                            as="button"
                            :href="route('verification.send', user.id)"
                            class="w-full bg-btn-gradient text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary-700 transition-colors"
                        >
                            Resend Verification Email
                        </Link>
                           
                        </td>
                        <td class="w-px border-t" v-if=" props?.filters?.role !== 'user'">
                            <Link class="flex items-center px-4" :href="`/admin/users/update/${user.id}/edit?role=${props?.filters?.role}`" tabindex="-1">
                                <icon name="cheveron-right" class="block w-6 h-6 fill-primary" />
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="users?.total === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="users?.links" />
        </div>
    </AppLayout>
</template>


<script setup>
import {
    checkDateStatus,
    formatDate
} from '@/data/helper';
import {
    defineProps, watch, reactive, ref
} from 'vue';
import {
    Link,
    router,
    useForm
} from '@inertiajs/vue3';
import { throttle, mapValues } from 'lodash'
import pickBy from 'lodash/pickBy'

import AppLayout from '@/Layouts/AppLayout.vue'
import BaseTable from '@/Shared/BaseTable.vue';
import Pagination from '@/Shared/Pagination.vue';
import SearchFilter from '@/Shared/SearchFilter.vue'
import Badge from '@/Shared/Badge.vue'
import Icon from '@/Shared/Icons.vue'
import FlashMessage from '@/Shared/FlashMessage.vue';

const verificationLinkSent = ref(null);

const headers = [
    'Name',
    'Email',
    'Mobile',
    'Address',
    'Birthday',
    'Gender',
    'Status',
    'Actions'
];
const props = defineProps({
    users: Object,
    filters: Object,
});

const form = reactive({ 
    search: props?.filters?.search,
    category: props?.filters?.category,
});

const forms = useForm({
    _method: 'PUT',
    first_name: '',
    middle_name: '',
    last_name: '',
    email: '',
    birthday: '',
    address: '',
    photo: '',
});

watch(
  form,
  throttle(() => {
    router.get(`/admin/users?role=${props?.filters?.role}`, pickBy(form), { preserveState: true })
  }, 150),
  { deep: true }
);


function getPageName(){
    let pageName;

    switch (props?.filters?.role?.toLowerCase()) {
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
}


const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const updateProfileInformation = () => {
    forms.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};
function reset() {
    console.log(form)
    Object.assign(form, mapValues(form, () => null));
}


</script>
