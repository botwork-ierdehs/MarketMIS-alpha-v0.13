<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stall Types">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Fees
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold text-primary">Fees</h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <BaseTable :headers="headers" :data="fees?.data">
                <!-- Optional: Customize header -->
                <template #header>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Amount</th>
                    <th class="px-4 py-3 text-left">Description</th>
                    <th class="px-4 py-3 text-left"></th>
                </template>

                <!-- Optional: Customize rows -->
                <template #row="{ data }">
                    <tr v-for="(fee, i) in data" :key="fees.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 capitalize text-left">{{ fee?.type }}</td>
                        <td class="px-4 py-3 capitalize text-left">{{ `₱${Number(fee.amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}` }}</td>
                        <td class="px-4 py-3 capitalize text-left">{{ fee?.description }}</td>                            
                        <td class="w-px border-t">
                            <Link class="flex items-left px-4" :href="route('admin.fees.edit', fee?.id)" tabindex="-1">
                                <icon name="cheveron-right" class="block w-6 h-6 fill-primary" />
                            </Link>
                        </td>
                    </tr>
                   
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="fees?.links" />
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
import Icon from '@/Shared/Icons.vue'

const headers = [
    'ID',
    'Name',
];


const props = defineProps({
    fees: Object,
});

const form = reactive({ 
    search: props?.filters?.search,
});

watch(
  form,
  throttle(() => {
    router.get(`/admin/system-setting/fees`, pickBy(form), { preserveState: true })
  }, 150),
  { deep: true }
);


</script>
