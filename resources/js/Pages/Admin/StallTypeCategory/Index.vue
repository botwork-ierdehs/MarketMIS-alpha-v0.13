<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stall Types">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stall Types
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold text-primary">Stall Types</h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
             <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset"/>
                <!-- <Link type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                :href="`/admin/system-setting/stall-types/create`" class="bg-btn-gradient text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary-700 transition-colors">Add Stall Type</Link> -->
            </div>
            <BaseTable :headers="headers" :data="stallsCategories?.data">
                <!-- Optional: Customize header -->
                <template #header>
                    <th class="px-4 py-3 text-left">Stall Type ID</th>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Description</th>
                    <th class="px-4 py-3 text-left">Volante/Transient</th>
                    <th class="px-4 py-3 text-left">Fees</th>
                </template>

                <!-- Optional: Customize rows -->
                <template #row="{ data }">
                    <tr v-for="(stall, i) in data" :key="stallsCategories.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ stall?.id }}</td>
                        <td class="px-4 py-3 capitalize">{{ stall?.name }}</td>
                        <td class="px-4 py-3 capitalize">{{ stall?.description }}</td>
                        <td class="px-4 py-3 capitalize">{{ stall?.is_transient ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3" v-html="handleFeesChange(stall?.fee) ? handleFeesChange(stall?.fee) : 'No Fees'"></td>

               
                        <td class="w-px border-t">
                            <Link class="flex items-center px-4" :href="route('admin.stall.type.edit', stall?.id)" tabindex="-1">
                                <icon name="cheveron-right" class="block w-6 h-6 fill-primary" />
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="stallsCategories?.total === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="stallsCategories?.links" />
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
import Icon from '@/Shared/Icons.vue'

const headers = [
    'ID',
    'Name',
];


const props = defineProps({
    stallsCategories: Object,
    filters: Object
});

const form = reactive({ 
    search: props?.filters?.search,
});

watch(
  form,
  throttle(() => {
    router.get(`/admin/system-setting/stall-types`, pickBy(form), { preserveState: true })
  }, 150),
  { deep: true }
);


function reset() {
    form.search = '';
    Object.assign(form, mapValues(form, () => null));
}

function handleFeesChange(fees) {

    return fees.map(fee => {
        const { is_daily, is_monthly, is_styro, is_per_kilo, type, amount } = fee;
        const amountValue = `₱${Number(amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
        let feeDescription = null;
        if(is_styro && is_per_kilo && is_daily) {
            feeDescription = 'day of styrofoam';
        }

        if(is_per_kilo && is_daily) {
            feeDescription = 'day of product per kilo';
        }

        if(is_monthly && !is_daily && !is_styro && !is_per_kilo) {
            feeDescription = 'monthly'; 
        }
        
        if(is_daily && !is_monthly && !is_styro && !is_per_kilo) {
            feeDescription = 'day'; 
        }

        return feeDescription ? `${type}: ${amountValue}/${feeDescription}` : `${type}: ${amountValue}`;

        }).join('<br>');


}
</script>
