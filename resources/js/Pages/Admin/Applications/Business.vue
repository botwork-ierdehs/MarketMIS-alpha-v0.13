<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Business Permit">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Business Permit
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold text-primary">Business Permit</h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
             <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
            </search-filter>
            </div>
            <BaseTable :headers="headers" :data="business?.data">
                <template #row="{ data }">
                    <tr v-for="(bsn, i) in data" :key="business.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 capitalize">{{ bsn.name }}</td>
                        <td class="px-4 py-3 capitalize">{{ bsn.trade_or_franchise_name }}</td>
                        <td class="px-4 py-3 capitalize">{{ bsn.business_address }}</td>
                        <td class="px-4 py-3 capitalize">{{ bsn.business_phone }}</td>
                        <td class="px-4 py-3 capitalize">{{ bsn.business_email }}</td>
                        <td class="px-4 py-3 capitalize">{{ bsn.business_telephone }}</td>
                        <td class="px-4 py-3 capitalize">{{ bsn.area_of_sqr_meter }}</td>
                        <!-- <td class="px-4 py-3 capitalize">{{`${formatDate(bsn.start_date)} - ${formatDate(bsn.end_date)}`}}</td>
                        <td class="px-4 py-3 capitalize">{{`${bsn.user.first_name} ${bsn.user.first_name}`}}</td>
                        <td class="px-4 py-3 capitalize">
                             <Badge :color="status(bsn.status).color">
                                {{ status(bsn.status).status }}
                             </Badge>
                        </td> -->
                        
                    </tr>
                    <tr v-if="business?.total === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="business?.links" />
        </div>
    </AppLayout>
</template>


<script setup>
import {
    defineProps, watch, reactive
} from 'vue';
import {
    Link,
    router
} from '@inertiajs/vue3';
import { throttle, mapValues } from 'lodash'
import pickBy from 'lodash/pickBy'

import AppLayout from '@/Layouts/AppLayout.vue'
import BaseTable from '@/Shared/BaseTable.vue';
import Pagination from '@/Shared/Pagination.vue';
import SearchFilter from '@/Shared/SearchFilter.vue'

const headers = [
    'Business Name',
    'Trade Name of Franchise Name',
    'Business Address',
    'Business Phone Number',
    'Business Email Address',
    'Business Telephone',
    'Area of Square Meter',
];


const props = defineProps({
    business: Object,
    filters: Object
});

const form = reactive({ 
    search: props?.filters?.search,
});

watch(
  form,
  throttle(() => {
    router.get(`/admin/applications/business-permit`, pickBy(form), { preserveState: true })
  }, 150),
  { deep: true }
);

function reset() {
    form.search = '';
    form.category = null;
    Object.assign(form, mapValues(form, () => null));
}
</script>
