<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Stalls">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stalls
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold text-primary">Rental Space</h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <FlashMessage v-if="$page.props.flash.success" :message="$page.props.flash.success" type="success" />
            <FlashMessage v-if="$page.props.flash.error" :message="$page.props.flash.error" type="error" />
            <div class="flex items-center justify-between mb-6">
             <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
                <label class="block text-gray-700">Stall Category:</label>
                <select v-model="form.category" class="form-select mt-1 w-full">
                    <option :value="null">All</option>
                    <option
                        v-for="categories in stallsCategories"
                        :key="categories.id"
                        :value="categories.id"
                    >{{ categories.name }}</option> 
                </select>
            </search-filter>
                <Link type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                :href="`/admin/system-setting/rental-space/create`" class="bg-btn-gradient text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary-700 transition-colors">Create New</Link>
            </div>
            <BaseTable :headers="headers" :data="stalls?.data">
                <!-- Optional: Customize header -->
                <template #header>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Size</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Fee</th>
                    <th class="px-4 py-3 text-left">Status</th>
                </template>

                <!-- Optional: Customize rows -->
                <template #row="{ data }">
                    <tr v-for="(stall, i) in data" :key="stalls.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 capitalize">{{ stall.name }}</td>
                        <td class="px-4 py-3 capitalize">{{stall?.size ? `${JSON.parse(stall.size)?.length} X ${JSON.parse(stall.size)?.width}` : '-'}}</td>
                        <td class="px-4 py-3 capitalize">{{stall.categories.name}}</td>
                        <td class="px-4 py-3">{{ handleFeesChange(stall?.fee) }}</td>
                        <td class="px-4 py-3 capitalize">
                             <Badge :color="status(stall.status).color">
                                {{ status(stall.status).status }}
                             </Badge>
                        </td>
                        <td class="w-px border-t">
                            <Link class="flex items-center px-4" :href="route('admin.stalls.edit', stall.id)" tabindex="-1">
                                <icon name="cheveron-right" class="block w-6 h-6 fill-primary" />
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="stalls?.total === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="stalls?.links" />
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
import Badge from '@/Shared/Badge.vue'
import Icon from '@/Shared/Icons.vue'
import { formatAmount } from '@/data/helper';
import FlashMessage from '@/Shared/FlashMessage.vue';

const headers = [
    'Name',
    'Admins'
];


const props = defineProps({
    stalls: Object,
    stallsCategories: Object,
    filters: Object
});

const form = reactive({ 
    search: props?.filters?.search,
    gender: props?.filters?.gender,
});

watch(
  form,
  throttle(() => {
    router.get(`/admin/system-setting/rental-space`, pickBy(form), { preserveState: true })
  }, 150),
  { deep: true }
);
const status = (stat) => {
    let statuss = {
        color: 'blue',
        status: 'Vacant'
    };
    if( stat === 3) {
        statuss = {
            color: 'orange',
            status: 'Reserved'
        }
    }
    if(stat === 1) {
        statuss = {
            color: 'green',
            status: 'Occupied'
        }
    }

    if(stat === 4) {
        statuss = {
            color: 'red',
            status: 'Under Maintenance'
        }
    }
    return statuss;
};

function reset() {
    form.search = '';
    form.category = null;
    Object.assign(form, mapValues(form, () => null));
}

function handleFeesChange(fees) {
    if(fees?.id=== 5){
        return ` ${formatAmount(fees?.amount)} for every area of square meter`;
    }

    if(fees?.id=== 4){
        return ` ${formatAmount(fees?.amount)} for every table`;
    }

    return '-';

}
</script>
