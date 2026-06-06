<template>
    <AuthLayout page-title="My Points">
        <!-- Top Stats Banner -->
        <div class="relative bg-gradient-to-br from-rose-950 via-rose-900 to-red-900 rounded-3xl p-8 text-white mb-8 shadow-2xl shadow-rose-900/20 overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <p class="text-rose-200/80 font-medium tracking-wide text-sm mb-2 uppercase">Total Points Earned</p>
                    <div class="flex items-center gap-3">
                        <span class="text-4xl">⭐</span>
                        <span class="text-5xl md:text-6xl font-extrabold tracking-tight">{{ totalPoints }}</span>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-sm font-semibold bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/10 inline-block">
                        From {{ points.length }} award{{ points.length !== 1 ? 's' : '' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900">Points History</h3>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white/70 backdrop-blur-2xl rounded-3xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden mb-8">
            <div v-if="points.length > 0">
                <div class="overflow-x-auto pb-4">
                    <table class="w-full text-sm whitespace-nowrap">
                    <thead class="bg-white/40 border-b border-white/50 backdrop-blur-sm">
                        <tr>
                            <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Points</th>
                            <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Awarded By</th>
                            <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Task Context</th>
                            <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Remarks</th>
                            <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="point in points" :key="point.id" class="hover:bg-rose-50/30 transition-colors">
                            <td class="px-8 py-5">
                                <span class="text-emerald-600 font-extrabold text-base bg-emerald-50 px-3 py-1 rounded-lg">+{{ point.points }}</span>
                            </td>
                            <td class="px-8 py-5 text-gray-900 font-medium">{{ point.awarded_by?.name ?? '—' }}</td>
                            <td class="px-8 py-5 text-rose-950/80">{{ point.collection_task?.schedule?.street?.name ?? '—' }}</td>
                            <td class="px-8 py-5 text-gray-500 italic">{{ point.remarks || 'No remarks' }}</td>
                            <td class="px-8 py-5 text-gray-500 font-medium">{{ formatDate(point.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            <div v-else class="px-8 py-16 text-center text-gray-400">
                <div class="text-4xl mb-4 opacity-50">🌱</div>
                <p class="font-medium">No points awarded yet. Proper waste disposal earns you points!</p>
            </div>
        </div>

        <!-- Mobile Stacked Cards -->
        <div class="md:hidden space-y-4 mb-8">
            <div v-for="point in points" :key="point.id" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-5 border border-white/60 shadow-md shadow-rose-900/5 flex flex-col gap-3">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-1">{{ formatDate(point.created_at) }}</p>
                        <p class="font-bold text-gray-900 text-sm">Task on {{ point.collection_task?.schedule?.street?.name ?? 'Unknown Street' }}</p>
                        <p class="text-xs text-gray-500 mt-1">By: {{ point.awarded_by?.name ?? '—' }}</p>
                    </div>
                    <span class="text-emerald-600 font-extrabold text-lg bg-emerald-50 px-3 py-1 rounded-lg">+{{ point.points }}</span>
                </div>
                <div v-if="point.remarks" class="pt-3 border-t border-gray-50 text-sm text-rose-950/80 italic">
                    "{{ point.remarks }}"
                </div>
            </div>
            <div v-if="points.length === 0" class="py-12 text-center text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                <div class="text-3xl mb-3 opacity-50">🌱</div>
                <p class="font-medium text-sm">No points awarded yet.</p>
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
