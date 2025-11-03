<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Volante Type">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Volante Types
            </h2>
        </template>
        <h1 class="mb-8 text-3xl font-bold text-primary">Volante Types</h1>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
             <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset"/>
                <Link type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                :href="`/admin/system-setting/volante-types/create`" class="bg-btn-gradient text-white font-bold uppercase px-5 py-2 rounded focus:outline-none shadow hover:bg-primary-700 transition-colors">Add Volante Type</Link>
            </div>
            <BaseTable :headers="headers" :data="volanteCategories?.data">
                <!-- Optional: Customize header -->
                <template #header>
                    <th class="px-4 py-3 text-left">Volante Type ID</th>
                    <th class="px-4 py-3 text-left">Name</th>
                </template>

                <!-- Optional: Customize rows -->
                <template #row="{ data }">
                    <tr v-for="(volante, i) in data" :key="volanteCategories.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ volante?.id }}</td>
                        <td class="px-4 py-3 capitalize">{{ volante?.name }}</td>
                        <td class="w-px border-t">
                            <Link class="flex items-center px-4" :href="route('admin.volante.type.edit', volante?.id)" tabindex="-1">
                                <icon name="cheveron-right" class="block w-6 h-6 fill-primary" />
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="volanteCategories?.total === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="volanteCategories?.links" />
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
    volanteCategories: Object,
    filters: Object
});

const form = reactive({ 
    search: props?.filters?.search,
});

watch(
  form,
  throttle(() => {
    router.get(`/admin/system-setting/volante-types`, pickBy(form), { preserveState: true })
  }, 150),
  { deep: true }
);


function reset() {
    form.search = '';
    Object.assign(form, mapValues(form, () => null));
}
</script>
