<template>
    <GuestLayout>
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-semibold text-gray-800 mb-2">Create Account</h1>
            <p class="text-gray-500 text-sm mb-6">Smart Waste Collection System</p>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Juan Dela Cruz"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    />
                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input
                        v-model="form.phone"
                        type="tel"
                        placeholder="09XXXXXXXXX"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    />
                    <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1">{{ form.errors.phone }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input
                        v-model="form.address"
                        type="text"
                        placeholder="House No., Street, Barangay"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    />
                    <p v-if="form.errors.address" class="text-red-500 text-xs mt-1">{{ form.errors.address }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street</label>
                    <select
                        v-model="form.street_id"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        <option value="">Select a street</option>
                        <option v-for="street in streets" :key="street.id" :value="street.id">
                            {{ street.name }} ({{ street.zone.name }})
                        </option>
                    </select>
                    <p v-if="form.errors.street_id" class="text-red-500 text-xs mt-1">{{ form.errors.street_id }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-green-600 text-white py-2 rounded-md text-sm font-medium hover:bg-green-700 transition"
                >
                    Register
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-4">
                Already have an account?
                <a :href="route('login')" class="text-green-600 hover:underline">Sign in</a>
            </p>
        </div>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" :closeable="false" max-width="sm">
            <div class="text-center py-4">
                <div class="text-green-500 text-5xl mb-4">✓</div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Registration Submitted</h2>
                <p class="text-gray-500 text-sm mb-6">
                    Your account is pending approval by the Barangay Official. You will receive an SMS once approved.
                </p>
                <a
                    :href="route('login')"
                    class="block w-full bg-green-600 text-white py-2 rounded-md text-sm font-medium hover:bg-green-700 transition"
                >
                    Back to Login
                </a>
            </div>
        </Modal>
    </GuestLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    streets: Array,
})

const page = usePage()
const showSuccessModal = ref(false)

const form = useForm({
    name: '',
    phone: '',
    address: '',
    street_id: '',
})

watch(() => page.props.flash?.success, (val) => {
    if (val) showSuccessModal.value = true
})

const submit = () => {
    form.post(route('register.store'))
}
</script>