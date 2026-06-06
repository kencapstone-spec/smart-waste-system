<template>
    <AuthLayout page-title="Dashboard">
        
        <!-- Welcome Header (All roles) -->
        <div class="mb-8">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
                Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-900 to-red-700">{{ auth.user.name.split(' ')[0] }}</span>! 👋
            </h2>
            <p class="text-gray-500 mt-2">Here is what's happening today.</p>
        </div>

        <!-- Super Admin Dashboard -->
        <div v-if="auth.user.role === 'super_admin'" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-8 border border-white/60 shadow-xl shadow-rose-900/5 hover:shadow-[0_8px_32px_rgba(225,29,72,0.08)] transition-shadow">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl mb-4">👥</div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Users</p>
                    <p class="text-4xl font-extrabold text-gray-900">{{ stats.totalUsers }}</p>
                </div>
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-8 border border-white/60 shadow-xl shadow-rose-900/5 hover:shadow-[0_8px_32px_rgba(225,29,72,0.08)] transition-shadow">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl mb-4">🏠</div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Residents</p>
                    <p class="text-4xl font-extrabold text-gray-900">{{ stats.totalResidents }}</p>
                </div>
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-8 border border-white/60 shadow-xl shadow-rose-900/5 hover:shadow-[0_8px_32px_rgba(225,29,72,0.08)] transition-shadow">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl mb-4">🗺️</div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">System Zones</p>
                    <p class="text-4xl font-extrabold text-gray-900">{{ stats.totalZones }}</p>
                </div>
            </div>
        </div>

        <!-- Barangay Official Dashboard -->
        <div v-else-if="auth.user.role === 'barangay_official'" class="space-y-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-6 border border-white/60 shadow-xl shadow-rose-900/5 text-center relative overflow-hidden">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Active Schedules</p>
                    <p class="text-3xl md:text-4xl font-extrabold text-gray-900">{{ stats.activeSchedules }}</p>
                </div>
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-6 border border-white/60 shadow-xl shadow-rose-900/5 text-center relative overflow-hidden group">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Pending Reports</p>
                    <p class="text-3xl md:text-4xl font-extrabold text-amber-500 group-hover:scale-110 transition-transform">{{ stats.pendingReports }}</p>
                </div>
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-6 border border-white/60 shadow-xl shadow-rose-900/5 text-center relative overflow-hidden group">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Pending Residents</p>
                    <p class="text-3xl md:text-4xl font-extrabold text-rose-500 group-hover:scale-110 transition-transform">{{ stats.pendingResidents }}</p>
                </div>
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-6 shadow-lg shadow-emerald-500/20 text-center text-white relative overflow-hidden">
                    <p class="text-xs md:text-sm font-semibold text-emerald-100 uppercase tracking-wider mb-2">Active Residents</p>
                    <p class="text-3xl md:text-4xl font-extrabold">{{ stats.activeResidents }}</p>
                </div>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 mb-4 mt-8">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a :href="route('official.schedules.index')" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-6 border border-white/60 hover:border-white hover:bg-white/90 hover:shadow-2xl hover:shadow-rose-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-2xl transition-colors">📅</div>
                    <div>
                        <p class="font-bold text-gray-900">Manage Schedules</p>
                        <p class="text-xs text-gray-500 mt-0.5">Create & update schedules</p>
                    </div>
                </a>
                <a :href="route('official.reports.index')" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-6 border border-white/60 hover:border-white hover:bg-white/90 hover:shadow-2xl hover:shadow-rose-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-2xl transition-colors">📝</div>
                    <div>
                        <p class="font-bold text-gray-900">View Reports</p>
                        <p class="text-xs text-gray-500 mt-0.5">Respond to resident issues</p>
                    </div>
                </a>
                <a :href="route('official.residents.index')" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-6 border border-white/60 hover:border-white hover:bg-white/90 hover:shadow-2xl hover:shadow-rose-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-2xl transition-colors">👥</div>
                    <div>
                        <p class="font-bold text-gray-900">Manage Residents</p>
                        <p class="text-xs text-gray-500 mt-0.5">Approve registrations</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Personnel Dashboard -->
        <div v-else-if="auth.user.role === 'personnel'" class="space-y-8">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-6 border border-white/60 shadow-xl shadow-rose-900/5 text-center relative">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">My Schedules</p>
                    <p class="text-3xl md:text-4xl font-extrabold text-gray-900">{{ stats.assignedSchedules }}</p>
                </div>
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-6 border border-white/60 shadow-xl shadow-rose-900/5 text-center relative group">
                    <p class="text-xs md:text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Tasks Today</p>
                    <p class="text-3xl md:text-4xl font-extrabold text-blue-600 group-hover:scale-110 transition-transform">{{ stats.tasksToday }}</p>
                </div>
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-6 shadow-lg shadow-emerald-500/20 text-center text-white relative col-span-2 md:col-span-1">
                    <p class="text-xs md:text-sm font-semibold text-emerald-100 uppercase tracking-wider mb-2">Completed</p>
                    <p class="text-3xl md:text-4xl font-extrabold">{{ stats.completedTasks }}</p>
                </div>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 mb-4 mt-8">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a :href="route('personnel.schedules.index')" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-6 border border-white/60 hover:border-white hover:bg-white/90 hover:shadow-2xl hover:shadow-rose-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-2xl transition-colors">📅</div>
                    <div>
                        <p class="font-bold text-gray-900">View Schedules</p>
                        <p class="text-xs text-gray-500 mt-0.5">Check your upcoming routes</p>
                    </div>
                </a>
                <a :href="route('personnel.tasks.index')" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-6 border border-white/60 hover:border-white hover:bg-white/90 hover:shadow-2xl hover:shadow-rose-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-2xl transition-colors">✅</div>
                    <div>
                        <p class="font-bold text-gray-900">Update Tasks</p>
                        <p class="text-xs text-gray-500 mt-0.5">Mark completed & award points</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Resident Dashboard -->
        <div v-else class="space-y-8">
            <!-- Points Card -->
            <div class="relative bg-gradient-to-br from-rose-950 via-rose-900 to-red-900 rounded-3xl p-8 overflow-hidden shadow-2xl shadow-rose-900/20 text-white">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <p class="text-rose-200/80 font-medium tracking-wide text-sm mb-2">AVAILABLE POINTS</p>
                        <div class="flex items-center gap-3">
                            <span class="text-4xl animate-bounce">⭐</span>
                            <span class="text-5xl md:text-6xl font-extrabold tracking-tight">{{ stats.totalPoints }}</span>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <a :href="route('resident.rewards.index')" class="inline-block bg-white text-rose-900 px-6 py-3 rounded-xl font-bold text-sm shadow-lg hover:bg-rose-50 transition-colors">
                            Redeem Rewards
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 md:gap-6">
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-6 border border-white/60 shadow-xl shadow-rose-900/5">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-xl mb-4">📝</div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Reports</p>
                    <p class="text-2xl font-extrabold text-gray-900">{{ stats.totalReports }}</p>
                </div>
                <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-6 border border-white/60 shadow-xl shadow-rose-900/5">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-xl mb-4">🔐</div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Status</p>
                    <div class="mt-1">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold capitalize"
                            :class="auth.user.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                            {{ auth.user.status }}
                        </span>
                    </div>
                </div>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 mb-4 mt-8">Quick Navigation</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a :href="route('resident.schedules.index')" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-6 border border-white/60 hover:border-white hover:bg-white/90 hover:shadow-2xl hover:shadow-rose-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-2xl transition-colors">📅</div>
                    <div>
                        <p class="font-bold text-gray-900">Collection Schedules</p>
                        <p class="text-xs text-gray-500 mt-0.5">When is the garbage truck coming?</p>
                    </div>
                </a>
                <a :href="route('resident.reports.index')" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-6 border border-white/60 hover:border-white hover:bg-white/90 hover:shadow-2xl hover:shadow-rose-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 group-hover:bg-rose-50 flex items-center justify-center text-2xl transition-colors">📝</div>
                    <div>
                        <p class="font-bold text-gray-900">Submit Report</p>
                        <p class="text-xs text-gray-500 mt-0.5">Report missed collections or issues</p>
                    </div>
                </a>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const page = usePage()
const auth = computed(() => page.props.auth)

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
})
</script>