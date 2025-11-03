<!-- Any Inertia page or normal Vue route -->
<template>
    <AppLayout title="User Types">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Types
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <BaseTable :headers="headers" :data="userTypes?.data">
                <!-- Optional: Customize header -->
                <template #header>
                    <th class="px-4 py-3 text-left">User Type ID</th>
                    <th class="px-4 py-3 text-left">Name</th>
                </template>

                <!-- Optional: Customize rows -->
                <template #row="{ data }">
                    <tr v-for="(userType, i) in data" :key="userType.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ userType.id }}</td>
                        <td class="px-4 py-3 capitalize">{{ userType.name }}</td>
                    </tr>
                    <tr v-if="data.length === 0 && userTypes?.links.length === 0">
                        <td class="px-6 py-4 border-t text-center" colspan="7">No records found.</td>
                    </tr>
                </template>
            </BaseTable>
        </div>
        <div class="mt-6 flex justify-center">
            <pagination :links="userTypes?.links" />
        </div>
    </AppLayout>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import BaseTable from '@/Shared/BaseTable.vue';
import Pagination from '@/Shared/Pagination.vue';

const headers = [
    'ID',
    'Name',
];
const props = defineProps({
    userTypes: Object,
});
</script>
