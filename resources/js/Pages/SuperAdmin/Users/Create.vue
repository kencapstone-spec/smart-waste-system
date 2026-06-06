<template>
    <AuthLayout page-title="Add User">
        <div class="mb-6">
            <Link :href="route('super-admin.users.index')" class="inline-flex items-center gap-2 text-sm text-rose-900/60 hover:text-rose-900 transition-colors transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Users
            </Link>
        </div>

        <div class="max-w-lg bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-6">Create New User</h2>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="form.name" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input v-model="form.phone" type="tel" placeholder="09XXXXXXXXX" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select v-model="form.role" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option value="">Select role</option>
                        <option value="barangay_official">Barangay Official</option>
                        <option value="personnel">Personnel</option>
                    </select>
                    <p v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <Link :href="route('super-admin.users.index')" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="bg-green-600 text-white px-5 py-2 rounded-md text-sm hover:bg-green-700 disabled:opacity-60">Create</button>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const form = useForm({
    name: '',
    phone: '',
    role: '',
})

const submit = () => {
    form.post(route('super-admin.users.store'))
}
</script>
