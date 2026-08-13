<template>
    <AuthLayout page-title="Waste Collection Schedules">
        
        <div v-if="myZone" class="mb-8 bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 rounded-2xl p-6 text-sm text-emerald-900 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-200/50 rounded-full flex items-center justify-center text-2xl shrink-0"><component :is="MapPin" class="w-5 h-5 inline text-rose-500" /></div>
            <div>
                <p class="text-emerald-700/80 font-semibold uppercase tracking-wider text-xs mb-0.5">Your Location</p>
                <p class="font-bold text-lg">
                    {{ myZone.name }}
                </p>
            </div>
        </div>
        <div v-else class="mb-8 bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200 rounded-2xl p-6 text-sm text-amber-900 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-200/50 rounded-full flex items-center justify-center text-2xl shrink-0">⚠️</div>
            <div>
                <p class="font-bold text-lg">Location Not Assigned</p>
                <p class="text-amber-800/80 mt-0.5">You are not assigned to a purok yet. Please contact the Barangay Office.</p>
            </div>
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-6">Upcoming Collections</h3>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white/70 backdrop-blur-2xl rounded-3xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden mb-8">
            <div class="overflow-x-auto  scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="bg-white/40 border-b border-white/50 backdrop-blur-sm">
                    <tr>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Title & Time</th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Frequency</th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Dates</th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Personnel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="schedule in schedules" :key="schedule.id" class="hover:bg-rose-50/30 transition-colors">
                        <td class="px-8 py-5">
                            <p class="font-bold text-gray-900 text-base mb-1">{{ schedule.title }}</p>
                            <p class="text-rose-600 font-semibold text-sm flex items-center gap-1">
                                <span><component :is="Clock" class="w-5 h-5 inline text-gray-500" /></span> {{ schedule.collection_time }}
                            </p>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-100 text-rose-700 capitalize">{{ schedule.frequency }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-gray-900 font-medium text-sm mb-0.5">Start: {{ formatDate(schedule.start_date) }}</p>
                            <p class="text-gray-500 text-xs">End: {{ schedule.end_date ? formatDate(schedule.end_date) : 'Ongoing' }}</p>
                        </td>
                        <td class="px-8 py-5 text-rose-950/80">
                            <span v-for="a in schedule.assignments" :key="a.id" class="block font-medium">{{ a.personnel?.name }}</span>
                            <span v-if="!schedule.assignments?.length" class="text-gray-400 italic">Unassigned</span>
                        </td>
                    </tr>
                    <tr v-if="schedules.length === 0">
                        <td colspan="4" class="px-8 py-16 text-center text-gray-400">
                            <div class="text-4xl mb-4 opacity-50"><component :is="Calendar" class="w-12 h-12 mx-auto text-indigo-500" /></div>
                            <p class="font-medium text-base">No schedules found for your purok.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Mobile Stacked Cards -->
        <div class="md:hidden space-y-4 mb-8">
            <div v-for="schedule in schedules" :key="schedule.id" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-5 border border-white/60 shadow-md shadow-rose-900/5 flex flex-col gap-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4">
                    <span class="px-3 py-1 rounded-lg text-[10px] font-bold bg-rose-100 text-rose-700 capitalize">{{ schedule.frequency }}</span>
                </div>
                
                <div class="pr-20">
                    <h4 class="font-extrabold text-gray-900 text-lg mb-1">{{ schedule.title }}</h4>
                    <p class="text-rose-600 font-bold text-sm flex items-center gap-1.5">
                        <span class="text-lg"><component :is="Clock" class="w-5 h-5 inline text-gray-500" /></span> {{ schedule.collection_time }}
                    </p>
                </div>
                
                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-50">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Start Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ formatDate(schedule.start_date) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Personnel</p>
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ schedule.assignments?.map(a => a.personnel?.name).join(', ') || 'Unassigned' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div v-if="schedules.length === 0" class="py-12 text-center text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                <div class="text-4xl mb-3 opacity-50"><component :is="Calendar" class="w-12 h-12 mx-auto text-indigo-500" /></div>
                <p class="font-medium text-sm">No schedules found for your purok.</p>
            </div>
        </div>

    </AuthLayout>
</template>

<script setup>
import { MapPin, Clock, Calendar } from '@lucide/vue';
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    schedules: Array,
    myZone: Object,
})

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})
</script>
