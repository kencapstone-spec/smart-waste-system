<template>
    <AuthLayout page-title="System Logs">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">User</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Action</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Model</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">IP Address</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-800">{{ log.user?.name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ log.action }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ log.model_type ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ log.ip_address ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ formatDate(log.created_at) }}</td>
                    </tr>
                    <tr v-if="logs.data.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No logs found.</td>
                    </tr>
                </tbody>
            </table>
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