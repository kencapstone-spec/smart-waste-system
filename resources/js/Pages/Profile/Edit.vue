<template>
    <AuthLayout page-title="Profile Settings">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border p-6 sm:p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Update Your Profile</h2>
                
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                            required
                        >
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                            required
                            placeholder="e.g. 09XXXXXXXXX"
                        >
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                    </div>

                    <template v-if="user.role === 'resident'">
                        <div class="border-t border-gray-100 pt-5 mt-5">
                            <h3 class="text-sm font-semibold text-gray-800 mb-4">Location Details</h3>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">House No., Building, Barangay</label>
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                        required
                                    >
                                    <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Street / Area</label>
                                    <select
                                        v-model="form.street_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                        required
                                    >
                                        <option value="" disabled>Select your street</option>
                                        <option v-for="street in streets" :key="street.id" :value="street.id">
                                            {{ street.name }} ({{ street.zone.name }})
                                        </option>
                                    </select>
                                    <p v-if="form.errors.street_id" class="mt-1 text-sm text-red-600">{{ form.errors.street_id }}</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="pt-4 flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-green-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
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
