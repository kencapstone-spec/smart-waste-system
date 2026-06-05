<template>
    <div class="min-h-screen bg-gray-100 flex">

        <!-- Sidebar -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-md transform transition-transform duration-200',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            'lg:translate-x-0 lg:static lg:inset-auto'
        ]">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <span class="text-green-600 font-bold text-lg">SmartWaste</span>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <nav class="px-4 py-4 space-y-1">
                <template v-for="item in navItems" :key="item.label">
                    <a
                        :href="item.href"
                        :class="[
                            'flex items-center gap-3 px-4 py-2 rounded-md text-sm font-medium transition',
                            isActive(item.href)
                                ? 'bg-green-50 text-green-700'
                                : 'text-gray-600 hover:bg-gray-100'
                        ]"
                    >
                        <span>{{ item.icon }}</span>
                        <span>{{ item.label }}</span>
                    </a>
                </template>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 px-4 py-4 border-t">
                <div class="flex items-center gap-3 px-4 py-2">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm">
                        {{ userInitial }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ auth.user.name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth.user.role.replace('_', ' ') }}</p>
                    </div>
                </div>
                <form @submit.prevent="logout" class="mt-2">
                    <button
                        type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 rounded-md transition"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay -->
        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/40 z-40 lg:hidden"
        />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Topbar -->
            <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between sticky top-0 z-30">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                    ☰
                </button>
                <h1 class="text-base font-semibold text-gray-800">{{ pageTitle }}</h1>
                <div class="text-sm text-gray-500">{{ auth.user.name }}</div>
            </header>

            <!-- Flash Notifications -->
            <div v-if="flash.success || flash.error" class="px-6 pt-4">
                <div
                    v-if="flash.success"
                    class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md text-sm"
                >
                    {{ flash.success }}
                </div>
                <div
                    v-if="flash.error"
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm"
                >
                    {{ flash.error }}
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'

const page = usePage()
const auth = computed(() => page.props.auth)
const flash = computed(() => page.props.flash)
const sidebarOpen = ref(false)

const props = defineProps({
    pageTitle: {
        type: String,
        default: 'Dashboard',
    },
})

const userInitial = computed(() => auth.value.user?.name?.charAt(0).toUpperCase() ?? '?')

const superAdminNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: '🏠' },
    { label: 'Users', href: route('super-admin.users.index'), icon: '👥' },
    { label: 'Zones', href: route('super-admin.zones.index'), icon: '🗺️' },
    { label: 'Streets', href: route('super-admin.streets.index'), icon: '🛣️' },
    { label: 'Rewards', href: route('super-admin.rewards.index'), icon: '🎁' },
    { label: 'System Logs', href: route('super-admin.system-logs.index'), icon: '📋' },
    { label: 'Profile Settings', href: route('profile.edit'), icon: '👤' },
]

const officialNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: '🏠' },
    { label: 'Schedules', href: route('official.schedules.index'), icon: '📅' },
    { label: 'Reports', href: route('official.reports.index'), icon: '📝' },
    { label: 'Residents', href: route('official.residents.index'), icon: '👥' },
    { label: 'Redemptions', href: route('official.redemptions.index'), icon: '🎁' },
    { label: 'Profile Settings', href: route('profile.edit'), icon: '👤' },
]

const personnelNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: '🏠' },
    { label: 'Collection Tasks', href: route('personnel.tasks.index'), icon: '✅' },
    { label: 'Profile Settings', href: route('profile.edit'), icon: '👤' },
]

const residentNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: '🏠' },
    { label: 'Schedules', href: route('resident.schedules.index'), icon: '📅' },
    { label: 'My Reports', href: route('resident.reports.index'), icon: '📝' },
    { label: 'My Points', href: route('resident.points.index'), icon: '⭐' },
    { label: 'Rewards', href: route('resident.rewards.index'), icon: '🎁' },
    { label: 'Profile Settings', href: route('profile.edit'), icon: '👤' },
]

const navItems = computed(() => {
    const role = auth.value.user?.role
    if (role === 'super_admin') return superAdminNav
    if (role === 'barangay_official') return officialNav
    if (role === 'personnel') return personnelNav
    return residentNav
})

const isActive = (href) => {
    try {
        return window.location.pathname === new URL(href, window.location.origin).pathname
    } catch {
        return false
    }
}

const logoutForm = useForm({})
const logout = () => logoutForm.post(route('logout'))
</script>