<template>
    <AuthLayout page-title="Profile Settings">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-3xl shadow-[0_4px_24px_rgba(225,29,72,0.04)] border border-gray-100 p-6 sm:p-10 mb-8 overflow-hidden relative">
                <!-- Background decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-rose-50 to-transparent rounded-full blur-3xl pointer-events-none -mr-20 -mt-20"></div>

                <div class="relative z-10 flex items-center gap-4 mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-rose-900 text-white flex items-center justify-center text-2xl shadow-lg shadow-rose-900/20 font-bold">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900">Update Profile</h2>
                        <p class="text-sm text-gray-500">Manage your account information</p>
                    </div>
                </div>
                
                <form @submit.prevent="submit" class="relative z-10 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-rose-900/10 focus:border-rose-900 transition-all font-medium"
                            required
                        >
                        <p v-if="form.errors.name" class="mt-2 text-xs font-medium text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-rose-900/10 focus:border-rose-900 transition-all font-medium"
                            required
                            placeholder="e.g. 09XXXXXXXXX"
                        >
                        <p v-if="form.errors.phone" class="mt-2 text-xs font-medium text-red-500">{{ form.errors.phone }}</p>
                    </div>

                    <template v-if="user.role === 'resident'">
                        <div class="border-t border-gray-100 pt-6 mt-6">
                            <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <span class="text-lg">📍</span> Location Details
                            </h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">House No., Building, Barangay</label>
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-rose-900/10 focus:border-rose-900 transition-all font-medium"
                                        required
                                    >
                                    <p v-if="form.errors.address" class="mt-2 text-xs font-medium text-red-500">{{ form.errors.address }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Street / Area</label>
                                    <select
                                        v-model="form.street_id"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-rose-900/10 focus:border-rose-900 transition-all font-medium appearance-none"
                                        required
                                    >
                                        <option value="" disabled>Select your street</option>
                                        <option v-for="street in streets" :key="street.id" :value="street.id">
                                            {{ street.name }} ({{ street.zone.name }})
                                        </option>
                                    </select>
                                    <p v-if="form.errors.street_id" class="mt-2 text-xs font-medium text-red-500">{{ form.errors.street_id }}</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full sm:w-auto bg-rose-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-rose-800 transition-all shadow-lg shadow-rose-900/20 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Saving Changes...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    user: Object,
    streets: Array,
})

const form = useForm({
    name: props.user.name,
    phone: props.user.phone,
    address: props.user.address ?? '',
    street_id: props.user.street_id ?? '',
})

const submit = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
    })
}
</script>
