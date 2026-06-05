<template>
    <AuthLayout page-title="My Points">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="md:col-span-1 bg-green-600 rounded-lg shadow-sm p-6 text-white">
                <p class="text-sm text-green-100 mb-1">Total Points Earned</p>
                <p class="text-5xl font-bold">{{ totalPoints }}</p>
                <p class="text-xs text-green-200 mt-2">from {{ points.length }} award{{ points.length !== 1 ? 's' : '' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-base font-semibold text-gray-700">Points History</h3>
            </div>

            <div v-if="points.length > 0">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Points</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Awarded By</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Collection Task</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Remarks</th>
                            <th class="text-left px-6 py-3 text-gray-600 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="point in points" :key="point.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="text-green-700 font-semibold">+{{ point.points }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ point.awarded_by?.name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ point.collection_task?.schedule?.street?.name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ point.remarks ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ formatDate(point.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="px-6 py-12 text-center text-gray-400">
                <p class="text-sm">No points awarded yet. Proper waste disposal earns you points!</p>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    points: Array,
    totalPoints: Number,
})

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})
</script>
