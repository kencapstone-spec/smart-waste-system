<template>
    <AuthLayout page-title="Waste Collection Schedules">
        <div v-if="myStreet" class="mb-4 bg-green-50 border border-green-200 rounded-lg px-5 py-3 text-sm text-green-800">
            Showing schedules for <span class="font-semibold">{{ myStreet.name }}</span>
            <span v-if="myStreet.zone"> — {{ myStreet.zone.name }}</span>
        </div>
        <div v-else class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg px-5 py-3 text-sm text-yellow-800">
            You are not assigned to a street yet. Please contact the Barangay Office.
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Title</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Frequency</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Collection Time</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Start Date</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">End Date</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Personnel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="schedule in schedules" :key="schedule.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ schedule.title }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 capitalize">{{ schedule.frequency }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ schedule.collection_time }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ formatDate(schedule.start_date) }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ schedule.end_date ? formatDate(schedule.end_date) : '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            <span v-for="a in schedule.assignments" :key="a.id" class="block">{{ a.personnel?.name }}</span>
                            <span v-if="!schedule.assignments?.length">—</span>
                        </td>
                    </tr>
                    <tr v-if="schedules.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No schedules found for your street.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    schedules: Array,
    myStreet: Object,
})

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})
</script>
