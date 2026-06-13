<template>
    <div class="min-h-screen bg-gradient-to-br from-rose-50 via-white to-rose-100 flex flex-col md:flex-row relative overflow-x-hidden">
        
        <!-- Animated mesh pattern -->
        <div class="fixed inset-0 z-0 opacity-30 pointer-events-none">
            <div class="absolute top-0 left-0 w-full h-full"
                style="background-image: radial-gradient(circle at 25% 25%, rgba(225,29,72,0.15) 1px, transparent 1px), radial-gradient(circle at 75% 75%, rgba(225,29,72,0.05) 1px, transparent 1px); background-size: 50px 50px;">
            </div>
        </div>

        <!-- Floating orbs -->
        <div class="fixed top-20 right-20 w-96 h-96 bg-rose-300/20 rounded-full blur-3xl animate-float pointer-events-none z-0"></div>
        <div class="fixed bottom-20 left-10 w-72 h-72 bg-red-300/15 rounded-full blur-3xl animate-float-delayed pointer-events-none z-0"></div>

        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex flex-col w-[280px] fixed inset-y-6 left-6 z-50 bg-white/60 backdrop-blur-3xl border border-white/60 rounded-3xl shadow-[0_8px_32px_rgba(225,29,72,0.12)]">
            <div class="px-8 py-8 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-900 flex items-center justify-center shadow-lg shadow-rose-900/20 text-white">
                    <component :is="Trash2" class="w-5 h-5" />
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
                        <component :is="item.icon" class="w-5 h-5" :class="isActive(item.href) ? 'opacity-100 scale-110 transition-transform' : 'opacity-60'" />
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
        <div class="relative z-10 flex-1 md:ml-[320px] flex flex-col min-h-screen">
            
            <!-- Mobile Top Header -->
            <header class="md:hidden sticky top-0 z-40 bg-white/70 backdrop-blur-2xl border-b border-white/50 shadow-sm">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-900 flex items-center justify-center shadow-md text-white">
                            <component :is="Trash2" class="w-4 h-4" />
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
                    <component :is="CheckCircle" class="w-5 h-5" /> {{ flash.success }}
                </div>
                <div
                    v-if="flash.error"
                    class="bg-rose-50 border border-rose-100 text-rose-800 px-5 py-4 rounded-2xl text-sm font-medium flex items-center gap-3 shadow-sm mb-4"
                >
                    <component :is="AlertTriangle" class="w-5 h-5" /> {{ flash.error }}
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 px-4 sm:px-6 md:px-10 pb-28 md:pb-12 w-full max-w-full">
                <slot />
            </main>
        </div>

        <!-- Mobile Bottom Navigation -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/70 backdrop-blur-3xl border-t border-white/50 shadow-[0_-10px_40px_rgba(225,29,72,0.12)] pb-safe">
            <div class="flex items-center justify-around px-2 py-2">
                <a
                    v-for="item in mainNavItems" :key="item.label"
                    :href="item.href"
                    class="flex flex-col items-center justify-center w-16 py-2 rounded-2xl transition-all duration-300"
                    :class="isActive(item.href) ? 'text-rose-900 scale-105' : 'text-gray-400 hover:text-gray-600'"
                >
                    <div class="relative">
                        <component :is="item.icon" class="w-6 h-6 mx-auto mb-1" :class="isActive(item.href) ? 'drop-shadow-md text-rose-900' : ''" />
                        <div v-if="isActive(item.href)" class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-rose-600"></div>
                    </div>
                    <span class="text-[10px] font-bold tracking-tight truncate w-full text-center px-1" :class="isActive(item.href) ? 'text-rose-950' : ''">{{ item.label }}</span>
                </a>

                <!-- More Button -->
                <button
                    v-if="moreNavItems.length > 0"
                    @click="showMobileMenu = true"
                    class="flex flex-col items-center justify-center w-16 py-2 rounded-2xl transition-all duration-300 text-gray-400 hover:text-rose-900"
                >
                    <component :is="MoreHorizontal" class="w-6 h-6 mx-auto mb-1" />
                    <span class="text-[10px] font-bold tracking-tight w-full text-center px-1">More</span>
                </button>
            </div>
        </nav>

        <!-- Mobile More Menu Overlay -->
        <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showMobileMenu" class="md:hidden fixed inset-0 z-[60] bg-gray-900/40 backdrop-blur-sm" @click="showMobileMenu = false"></div>
        </transition>

        <transition enter-active-class="transition ease-out duration-300 transform" enter-from-class="translate-y-full" enter-to-class="translate-y-0" leave-active-class="transition ease-in duration-200 transform" leave-from-class="translate-y-0" leave-to-class="translate-y-full">
            <div v-if="showMobileMenu" class="md:hidden fixed bottom-0 left-0 right-0 z-[70] bg-white rounded-t-3xl shadow-2xl overflow-hidden pb-safe">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-rose-50/30">
                    <h3 class="font-bold text-rose-950 text-lg">More Options</h3>
                    <button @click="showMobileMenu = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-rose-100 text-rose-900 font-bold hover:bg-rose-200 transition-colors"><component :is="X" class="w-5 h-5" /></button>
                </div>
                <div class="p-4 space-y-2 max-h-[60vh] overflow-y-auto">
                    <a
                        v-for="item in moreNavItems" :key="item.label"
                        :href="item.href"
                        class="flex items-center gap-4 p-4 rounded-2xl transition-colors"
                        :class="isActive(item.href) ? 'bg-rose-100 border border-rose-200' : 'bg-gray-50 hover:bg-rose-50 border border-transparent'"
                    >
                        <component :is="item.icon" class="w-6 h-6" />
                        <span class="font-semibold" :class="isActive(item.href) ? 'text-rose-900' : 'text-gray-700'">{{ item.label }}</span>
                    </a>
                </div>
            </div>
        </transition>

    </div>
</template>

<script setup>
import { X } from '@lucide/vue';
import { ref, computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import {
    Trash2, Home as HomeIcon, Users, Map as MapIcon, ClipboardList,
    User as UserIcon, Calendar, FileText, Gift, Award, CheckCircle, Star, AlertTriangle, MoreHorizontal
} from '@lucide/vue'

const showMobileMenu = ref(false)

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
    { label: 'Dashboard', href: route('dashboard'), icon: HomeIcon },
    { label: 'Users', href: route('super-admin.users.index'), icon: Users },
    { label: 'Zones', href: route('super-admin.zones.index'), icon: MapIcon },

    { label: 'Logs', href: route('super-admin.system-logs.index'), icon: ClipboardList },
    { label: 'Profile', href: route('profile.edit'), icon: UserIcon },
]

const officialNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: HomeIcon },
    { label: 'Schedules', href: route('official.schedules.index'), icon: Calendar },
    { label: 'Reports', href: route('official.reports.index'), icon: FileText },
    { label: 'Residents', href: route('official.residents.index'), icon: Users },
    { label: 'Redemptions', href: route('official.redemptions.index'), icon: Gift },
    { label: 'Rewards', href: route('official.rewards.index'), icon: Award },
    { label: 'Profile', href: route('profile.edit'), icon: UserIcon },
]

const personnelNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: HomeIcon },
    { label: 'Tasks', href: route('personnel.tasks.index'), icon: CheckCircle },
    { label: 'Profile', href: route('profile.edit'), icon: UserIcon },
]

const residentNav = [
    { label: 'Dashboard', href: route('dashboard'), icon: HomeIcon },
    { label: 'Schedules', href: route('resident.schedules.index'), icon: Calendar },
    { label: 'Reports', href: route('resident.reports.index'), icon: FileText },
    { label: 'Points', href: route('resident.points.index'), icon: Star },
    { label: 'Rewards', href: route('resident.rewards.index'), icon: Gift },
    { label: 'Profile', href: route('profile.edit'), icon: UserIcon },
]

const navItems = computed(() => {
    const role = auth.value.user?.role
    if (role === 'super_admin') return superAdminNav
    if (role === 'barangay_official') return officialNav
    if (role === 'personnel') return personnelNav
    return residentNav
})

const mainNavItems = computed(() => {
    return navItems.value.length > 5 ? navItems.value.slice(0, 4) : navItems.value
})

const moreNavItems = computed(() => {
    return navItems.value.length > 5 ? navItems.value.slice(4) : []
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