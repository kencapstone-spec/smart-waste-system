<template>
    <AuthLayout page-title="My Assigned Schedules">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-base font-semibold text-gray-700">Active Schedules Assigned to Me</h2>
            </div>

            <div v-if="schedules.length > 0">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Title</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Street</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Zone</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Frequency</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Time</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Start Date</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">End Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="schedule in schedules" :key="schedule.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ schedule.title }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ schedule.street?.name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ schedule.street?.zone?.name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 capitalize">{{ schedule.frequency }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ schedule.collection_time }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ formatDate(schedule.start_date) }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ schedule.end_date ? formatDate(schedule.end_date) : '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="px-6 py-12 text-center text-gray-400">
                <p class="text-sm">No active schedules assigned to you.</p>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    schedules: Array,
})

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})
</script>
