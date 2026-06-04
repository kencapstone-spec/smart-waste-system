<template>
    <GuestLayout>
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-semibold text-gray-800 mb-2">Sign In</h1>
            <p class="text-gray-500 text-sm mb-6">Smart Waste Collection System</p>

            <form @submit.prevent="sendOtp">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input
                        v-model="loginForm.phone"
                        type="tel"
                        placeholder="09XXXXXXXXX"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    />
                    <p v-if="loginForm.errors.phone" class="text-red-500 text-xs mt-1">{{ loginForm.errors.phone }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="loginForm.processing"
                    class="w-full bg-green-600 text-white py-2 rounded-md text-sm font-medium hover:bg-green-700 transition"
                >
                    Send OTP
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-4">
                No account?
                <a :href="route('register')" class="text-green-600 hover:underline">Register here</a>
            </p>
        </div>

        <!-- OTP Modal -->
        <Modal :show="showOtpModal" title="Enter OTP" @close="showOtpModal = false">
            <p class="text-gray-500 text-sm mb-6">
                We sent a 6-digit code to <span class="font-medium text-gray-700">{{ loginForm.phone }}</span>
            </p>

            <form @submit.prevent="verifyOtp">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">OTP Code</label>
                    <input
                        v-model="otpForm.code"
                        type="text"
                        maxlength="6"
                        placeholder="000000"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 tracking-widest text-center"
                    />
                    <p v-if="otpForm.errors.code" class="text-red-500 text-xs mt-1">{{ otpForm.errors.code }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="otpForm.processing"
                    class="w-full bg-green-600 text-white py-2 rounded-md text-sm font-medium hover:bg-green-700 transition"
                >
                    Verify OTP
                </button>
            </form>

            <template #footer>
                <button @click="showOtpModal = false" class="text-sm text-gray-500 hover:text-gray-700">Back</button>
            </template>
        </Modal>
    </GuestLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import Modal from '@/Components/Modal.vue'

const page = usePage()
const showOtpModal = ref(false)

const loginForm = useForm({
    phone: '',
})

const otpForm = useForm({
    phone: '',
    code: '',
})

watch(() => page.props.flash?.otpSent, (val) => {
    if (val) {
        otpForm.phone = loginForm.phone
        showOtpModal.value = true
    }
})

const sendOtp = () => {
    loginForm.post(route('login.send-otp'))
}

const verifyOtp = () => {
    otpForm.post(route('login.verify-otp'))
}
</script>