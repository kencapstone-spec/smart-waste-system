<template>
    <div class="min-h-screen bg-gray-50/50 flex flex-col md:flex-row">
        
        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex flex-col w-72 fixed inset-y-0 left-0 z-50 bg-white/80 backdrop-blur-2xl border-r border-rose-100 shadow-[4px_0_24px_rgba(225,29,72,0.02)]">
            <div class="px-8 py-8 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-900 flex items-center justify-center text-xl shadow-lg shadow-rose-900/20 text-white">
                    🗑️
                </div>
                <span class="text-rose-950 font-extrabold text-2xl tracking-tight">SmartWaste</span>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                <template v-for="item in navItems" :key="item.label">
                    <a
                        :href="item.href"
                        :class="[
                            'flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-all duration-300',
                            isActive(item.href)
                                ? 'bg-gradient-to-r from-rose-50 to-white text-rose-900 shadow-sm border border-rose-100/50'
                                : 'text-gray-500 hover:bg-gray-50 hover:text-rose-900'
                        ]"
                    >
                        <span class="text-xl" :class="isActive(item.href) ? 'opacity-100 scale-110 transition-transform' : 'opacity-60'">{{ item.icon }}</span>
                        <span>{{ item.label }}</span>
                    </a>
                </template>
            </nav>

            <div class="p-6 border-t border-rose-50">
                <div class="bg-white rounded-2xl p-4 border border-rose-50 shadow-sm flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-100 to-rose-50 flex items-center justify-center text-rose-900 font-bold text-sm">
                        {{ userInitial }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth.user.name }}</p>
                        <p class="text-xs text-gray-500 capitalize font-medium">{{ auth.user.role.replace('_', ' ') }}</p>
                    </div>
                </div>
                <form @submit.prevent="logout">
                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-rose-600 hover:text-white hover:bg-rose-600 rounded-xl transition-all duration-300"
                    >
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 md:ml-72 flex flex-col min-h-screen">
            
            <!-- Mobile Top Header -->
            <header class="md:hidden sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-rose-100 shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-900 flex items-center justify-center text-sm shadow-md text-white">
                            🗑️
                        </div>
                        <h1 class="text-lg font-bold text-rose-950">{{ pageTitle }}</h1>
                    </div>
                    
                    <!-- Mobile Profile Menu Trigger (Logout) -->
                    <form @submit.prevent="logout">
                        <button type="submit" class="w-9 h-9 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-900 font-bold text-sm">
                            {{ userInitial }}
                        </button>
                    </form>
                </div>
            </header>

            <!-- Desktop Header -->
            <header class="hidden md:flex items-center justify-between px-10 py-8">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ pageTitle }}</h1>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded-full border shadow-sm font-medium">
                        {{ new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' }) }}
                    </div>
                </div>
            </header>

            <!-- Flash Notifications -->
            <div v-if="flash.success || flash.error" class="px-6 md:px-10 pt-4 md:pt-0">
                <div
                    v-if="flash.success"
                    class="bg-emerald-50 border border-emerald-100 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-medium flex items-center gap-3 shadow-sm mb-4"
                >
                    <span class="text-lg">✅</span> {{ flash.success }}
                </div>
                <div
                    v-if="flash.error"
                    class="bg-rose-50 border border-rose-100 text-rose-800 px-5 py-4 rounded-2xl text-sm font-medium flex items-center gap-3 shadow-sm mb-4"
                >
                    <span class="text-lg">⚠️</span> {{ flash.error }}
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 px-6 md:px-10 pb-28 md:pb-12">
                <slot />
            </main>
        </div>

        <!-- Mobile Bottom Navigation -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-2xl border-t border-rose-100 shadow-[0_-10px_40px_rgba(225,29,72,0.08)] pb-safe">
            <div class="flex items-center overflow-x-auto hide-scrollbar px-2 py-2 gap-1">
                <a
                    v-for="item in navItems" :key="item.label"
                    :href="item.href"
                    class="flex-shrink-0 flex flex-col items-center justify-center w-20 py-2 rounded-2xl transition-all duration-300"
                    :class="isActive(item.href) ? 'text-rose-900 scale-105' : 'text-gray-400 hover:text-gray-600'"
                >
                    <div class="relative">
                        <span class="text-2xl block mb-1" :class="isActive(item.href) ? 'drop-shadow-md' : ''">{{ item.icon }}</span>
                        <div v-if="isActive(item.href)" class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-rose-600"></div>
                    </div>
                    <span class="text-[10px] font-bold tracking-tight truncate w-full text-center px-1" :class="isActive(item.href) ? 'text-rose-950' : ''">{{ item.label }}</span>
                </a>
            </div>
        </nav>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'

const page = usePage()
const auth = computed(() => page.props.auth)
const flash = computed(() => page.props.flash)

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
    { label: 'Logs', href: route('super-admin.system-logs.index'), icon: '📋' },
    { label: 'Profile', href: route('profile.edit'), icon: '👤' },
]

const officialNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: '🏠' },
    { label: 'Schedules', href: route('official.schedules.index'), icon: '📅' },
    { label: 'Reports', href: route('official.reports.index'), icon: '📝' },
    { label: 'Residents', href: route('official.residents.index'), icon: '👥' },
    { label: 'Redemptions', href: route('official.redemptions.index'), icon: '🎁' },
    { label: 'Profile', href: route('profile.edit'), icon: '👤' },
]

const personnelNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: '🏠' },
    { label: 'Tasks', href: route('personnel.tasks.index'), icon: '✅' },
    { label: 'Profile', href: route('profile.edit'), icon: '👤' },
]

const residentNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: '🏠' },
    { label: 'Schedules', href: route('resident.schedules.index'), icon: '📅' },
    { label: 'Reports', href: route('resident.reports.index'), icon: '📝' },
    { label: 'Points', href: route('resident.points.index'), icon: '⭐' },
    { label: 'Rewards', href: route('resident.rewards.index'), icon: '🎁' },
    { label: 'Profile', href: route('profile.edit'), icon: '👤' },
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

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.pb-safe {
    padding-bottom: env(safe-area-inset-bottom);
}
</style>