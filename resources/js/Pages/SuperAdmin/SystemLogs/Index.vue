<template>
    <AuthLayout page-title="System Logs">
        <div class="bg-white/70 backdrop-blur-2xl rounded-2xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden">
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">User</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Action</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Model</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">IP Address</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="log in logs.data" :key="log.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-800">{{ log.user?.name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ log.action }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ log.model_type ?? '—' }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ log.ip_address ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ formatDate(log.created_at) }}</td>
                    </tr>
                    <tr v-if="logs.data.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No logs found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    logs: Object,
})

const formatDate = (date) => {
    return new Date(date).toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>