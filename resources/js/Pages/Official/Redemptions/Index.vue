<template>
    <AuthLayout page-title="Redemptions Management">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Date</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Resident</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Reward</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Points Spent</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="redemption in redemptions.data" :key="redemption.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-600">{{ new Date(redemption.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 text-gray-800 font-medium">
                            {{ redemption.resident.name }}
                            <div class="text-xs text-gray-500">{{ redemption.resident.phone }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-800">{{ redemption.reward.name }}</td>
                        <td class="px-6 py-4 font-bold text-yellow-600">{{ redemption.points_spent }} ⭐</td>
                        <td class="px-6 py-4">
                            <span :class="{
                                'bg-yellow-100 text-yellow-700': redemption.status === 'pending',
                                'bg-green-100 text-green-700': redemption.status === 'approved',
                                'bg-red-100 text-red-700': redemption.status === 'rejected'
                            }" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                {{ redemption.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <template v-if="redemption.status === 'pending'">
                                <button @click="process(redemption, 'approve')" class="text-green-600 hover:underline text-xs font-medium">Approve</button>
                                <button @click="process(redemption, 'reject')" class="text-red-500 hover:underline text-xs font-medium">Reject</button>
                            </template>
                            <span v-else class="text-xs text-gray-400">Processed</span>
                        </td>
                    </tr>
                    <tr v-if="redemptions.data.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No redemptions found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    redemptions: Object,
})

const process = (redemption, action) => {
    if (confirm(`Are you sure you want to ${action} this redemption?`)) {
        router.post(route(`official.redemptions.${action}`, redemption.id), {}, { preserveScroll: true })
    }
}
</script>
