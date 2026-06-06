<template>
    <GuestLayout>
        <div class="flex flex-col md:flex-row w-full max-w-4xl bg-white/70 backdrop-blur-3xl border border-white/50 rounded-[32px] shadow-[0_32px_64px_rgba(225,29,72,0.15)] overflow-hidden min-h-[550px] relative transition-all duration-500">
            
            <div class="w-full md:w-1/2 px-8 pt-24 pb-16 md:p-12 md:pt-20 flex flex-col justify-center relative">
                
                <a href="/" class="absolute top-8 left-8 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-gray-900 transition-colors duration-200 z-30 group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Back to Home
                </a>

                <div class="w-full max-w-sm mx-auto">
                    
                    <div class="text-center mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm">
                            🗑️
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2 tracking-tight">Sign In</h1>
                        <p class="text-gray-500 text-sm">Welcome back to SmartWaste</p>
                    </div>

                    <form @submit.prevent="sendOtp">
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <input
                                v-model="loginForm.phone"
                                type="tel"
                                placeholder="09XXXXXXXXX"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-rose-900/10 focus:border-rose-900 transition-all duration-300"
                            />
                            <p v-if="loginForm.errors.phone" class="text-red-600 text-xs mt-2 bg-red-50 border border-red-100 p-2 rounded-lg font-medium">{{ loginForm.errors.phone }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="loginForm.processing"
                            class="w-full bg-rose-900 text-white py-3 rounded-xl text-sm font-bold hover:bg-rose-900/90 transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            Send OTP
                        </button>
                    </form>
                </div>
            </div>

            <div class="w-full md:w-1/2 bg-gradient-to-br from-[#8C0E32] to-[#4F0018] p-12 text-white flex flex-col justify-center items-center text-center relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
                
                <div class="max-w-xs relative z-10">
                    <span class="text-4xl block mb-4 animate-bounce">👋</span>
                    <h2 class="text-3xl font-bold mb-4 tracking-tight">Hello, Neighbor!</h2>
                    <p class="text-rose-100/80 text-sm leading-relaxed mb-8">
                        Don't have an account yet? Register your details to start participating in our Smart Waste Collection system.
                    </p>
                    
                    <a 
                        :href="route('register')" 
                        class="inline-block border-2 border-white/60 text-white font-bold text-sm px-8 py-3 rounded-xl hover:bg-white hover:text-rose-900 transition-all duration-300"
                    >
                        Sign Up
                    </a>
                </div>
            </div>
        </div>

        <Modal :show="showOtpModal" title="Security Verification" @close="showOtpModal = false">
            <div class="text-center pb-2">
                <div class="w-16 h-16 bg-rose-50 border border-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🔐</span>
                </div>
                <p class="text-gray-500 text-sm mb-6">
                    We sent a 6-digit code to <br/>
                    <span class="font-bold text-gray-900 text-lg tracking-wide">{{ loginForm.phone }}</span>
                </p>

                <form @submit.prevent="verifyOtp">
                    <div class="mb-6">
                        <input
                            v-model="otpForm.code"
                            type="text"
                            maxlength="6"
                            placeholder="••••••"
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-2xl font-bold focus:outline-none focus:border-rose-900 focus:ring-4 focus:ring-rose-900/10 tracking-[0.5em] text-center text-gray-800 transition-all duration-300"
                        />
                        <p v-if="otpForm.errors.code" class="text-red-500 text-sm mt-2 font-medium">{{ otpForm.errors.code }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="otpForm.processing"
                        class="w-full bg-rose-900 text-white py-3 rounded-xl text-sm font-bold hover:bg-rose-800 transition-all duration-300 shadow-lg shadow-rose-900/20 disabled:opacity-70"
                    >
                        Verify & Sign In
                    </button>
                </form>
            </div>
            
            <template #footer>
                <div class="w-full flex justify-center">
                    <button @click="showOtpModal = false" class="text-sm font-semibold text-gray-400 hover:text-rose-950/80 transition-colors">Cancel</button>
                </div>
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