<template>
    <AuthLayout page-title="Redemptions Management">
        <div class="bg-white/70 backdrop-blur-2xl rounded-2xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden">
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Date</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Resident</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Reward</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Points Spent</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Status</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="redemption in redemptions.data" :key="redemption.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-rose-950/80">{{ new Date(redemption.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 text-rose-950 font-semibold">
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
