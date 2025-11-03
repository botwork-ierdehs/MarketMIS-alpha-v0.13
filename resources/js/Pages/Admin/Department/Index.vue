<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="Departments">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Departments
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <BaseTable :headers="headers" :data="departments?.data">
                <!-- Optional: Customize header -->
                <template #header>
                    <th class="px-4 py-3 text-left">Department ID</th>
                    <th class="px-4 py-3 text-left">Code</th>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Admins</th>
                </template>

                <!-- Optional: Customize rows -->
                <template #row="{ data }">
                    <tr v-for="(department, i) in data" :key="department.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ department.id }}</td>
                        <td class="px-4 py-3 capitalize">{{ department?.admins?.code }}</td>
                        <td class="px-4 py-3 capitalize">{{ department.name }}</td>
                        <td class="px-4 py-3">
                                <ul class="list-disc list-inside text-green" v-if="department?.admins?.users?.length > 0">
                                    <li v-for="(item, index) in department?.admins?.users" :key="index">
                                    {{ `${item?.name}-${item?.email}` }}
                                    </li>
                                </ul>
                           </td>
                    </tr>
                     <tr v-if="departments?.total === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="departments?.links" />
        </div>
    </AppLayout>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import BaseTable from '@/Shared/BaseTable.vue';
import Pagination from '@/Shared/Pagination.vue';

const headers = [
    'ID',
    'Code',
    'Name',
    'Admins'
];
const props = defineProps({
    departments: Object,
});
</script>
