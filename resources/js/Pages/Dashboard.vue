<template>
    <AuthLayout page-title="Dashboard">
        <!-- Super Admin Dashboard -->
        <div v-if="auth.user.role === 'super_admin'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ stats.totalUsers }}</p>
                    <p class="text-xs text-gray-400 mt-1">Barangay officials & personnel</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Total Residents</p>
                    <p class="text-3xl font-bold text-gray-800">{{ stats.totalResidents }}</p>
                    <p class="text-xs text-gray-400 mt-1">Registered in the system</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">System Zones</p>
                    <p class="text-3xl font-bold text-gray-800">{{ stats.totalZones }}</p>
                    <p class="text-xs text-gray-400 mt-1">Active zones</p>
                </div>
            </div>
        </div>

        <!-- Barangay Official Dashboard -->
        <div v-else-if="auth.user.role === 'barangay_official'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Active Schedules</p>
                    <p class="text-3xl font-bold text-gray-800">{{ stats.activeSchedules }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Pending Reports</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ stats.pendingReports }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Pending Residents</p>
                    <p class="text-3xl font-bold text-orange-500">{{ stats.pendingResidents }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Active Residents</p>
                    <p class="text-3xl font-bold text-green-600">{{ stats.activeResidents }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a :href="route('official.schedules.index')" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                    <span class="text-2xl">📅</span>
                    <div>
                        <p class="font-medium text-gray-800">Manage Schedules</p>
                        <p class="text-xs text-gray-400">Create & update collection schedules</p>
                    </div>
                </a>
                <a :href="route('official.reports.index')" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                    <span class="text-2xl">📝</span>
                    <div>
                        <p class="font-medium text-gray-800">View Reports</p>
                        <p class="text-xs text-gray-400">Respond to resident reports</p>
                    </div>
                </a>
                <a :href="route('official.residents.index')" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                    <span class="text-2xl">👥</span>
                    <div>
                        <p class="font-medium text-gray-800">Manage Residents</p>
                        <p class="text-xs text-gray-400">Approve or reject registrations</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Personnel Dashboard -->
        <div v-else-if="auth.user.role === 'personnel'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Assigned Schedules</p>
                    <p class="text-3xl font-bold text-gray-800">{{ stats.assignedSchedules }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Tasks Today</p>
                    <p class="text-3xl font-bold text-blue-600">{{ stats.tasksToday }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Completed Tasks</p>
                    <p class="text-3xl font-bold text-green-600">{{ stats.completedTasks }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a :href="route('personnel.schedules.index')" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                    <span class="text-2xl">📅</span>
                    <div>
                        <p class="font-medium text-gray-800">My Schedules</p>
                        <p class="text-xs text-gray-400">View assigned collection schedules</p>
                    </div>
                </a>
                <a :href="route('personnel.tasks.index')" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                    <span class="text-2xl">✅</span>
                    <div>
                        <p class="font-medium text-gray-800">Collection Tasks</p>
                        <p class="text-xs text-gray-400">Update status & award points</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Resident Dashboard -->
        <div v-else class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-green-600 rounded-lg shadow-sm p-6 text-white">
                    <p class="text-sm text-green-100 mb-1">My Points Balance</p>
                    <p class="text-4xl font-bold">{{ stats.totalPoints }}</p>
                    <p class="text-xs text-green-200 mt-1">Keep up the good work!</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Reports Submitted</p>
                    <p class="text-3xl font-bold text-gray-800">{{ stats.totalReports }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <p class="text-sm text-gray-500 mb-1">Account Status</p>
                    <span class="px-3 py-1 rounded-full text-sm font-medium capitalize"
                        :class="auth.user.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                        {{ auth.user.status }}
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a :href="route('resident.schedules.index')" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                    <span class="text-2xl">📅</span>
                    <div>
                        <p class="font-medium text-gray-800">Collection Schedules</p>
                        <p class="text-xs text-gray-400">View schedules for your street</p>
                    </div>
                </a>
                <a :href="route('resident.reports.index')" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                    <span class="text-2xl">📝</span>
                    <div>
                        <p class="font-medium text-gray-800">My Reports</p>
                        <p class="text-xs text-gray-400">Submit & track your reports</p>
                    </div>
                </a>
                <a :href="route('resident.points.index')" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                    <span class="text-2xl">⭐</span>
                    <div>
                        <p class="font-medium text-gray-800">My Points</p>
                        <p class="text-xs text-gray-400">View your points history</p>
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