<template>
    <AuthLayout page-title="My Assigned Schedules">
        <div class="bg-white/70 backdrop-blur-2xl sm:rounded-2xl shadow-xl shadow-rose-900/5 sm:border border-white/60 -mx-4 sm:mx-0 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-base font-semibold text-gray-700">Active Schedules Assigned to Me</h2>
            </div>

            <div v-if="schedules.length > 0">
                <div class="overflow-x-auto  scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent pb-4">
                    <table class="w-full text-sm whitespace-nowrap">
                    <thead class="border-b border-rose-100/50">
                        <tr>
                            <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Title</th>
                            <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Purok</th>
                            <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="MapPin" class="w-4 h-4 opacity-70" /> Zone</div></th>
                            <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Frequency</th>
                            <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Clock" class="w-4 h-4 opacity-70" /> Time</div></th>
                            <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Start Date</th>
                            <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">End Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="schedule in schedules" :key="schedule.id" class="hover:bg-rose-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ schedule.title }}</td>
                            <td class="px-6 py-4 text-rose-950/80">{{ schedule.zone?.name ?? '—' }}</td>
                            <td class="px-6 py-4 text-rose-950/80">{{ schedule.zone?.name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 capitalize">{{ schedule.frequency }}</span>
                            </td>
                            <td class="px-6 py-4 text-rose-950/80">{{ schedule.collection_time }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ formatDate(schedule.start_date) }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ schedule.end_date ? formatDate(schedule.end_date) : '—' }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>

            <div v-else class="px-6 py-12 text-center text-gray-400">
                <p class="text-sm">No active schedules assigned to you.</p>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { MapPin, Clock } from '@lucide/vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    schedules: Array,
})

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})
</script>
