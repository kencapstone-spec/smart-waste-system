<template>
    <Head title="Authentication" />
    <div class="w-full min-h-screen bg-rose-50/30 flex flex-col md:flex-row font-sans">
        
        <!-- Left Side: Premium Branding (Hidden on Mobile) -->
        <div class="hidden md:flex md:w-5/12 lg:w-1/2 bg-gradient-to-br from-rose-950 via-rose-900 to-red-900 relative overflow-hidden flex-col justify-center px-12 lg:px-20">
            
            <!-- Animated mesh pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0"
                    style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.4) 1px, transparent 0); background-size: 48px 48px;">
                </div>
            </div>

            <!-- Floating Orbs for depth -->
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-rose-500/20 rounded-full blur-[120px] mix-blend-screen translate-x-1/3 -translate-y-1/3 animate-float"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-red-500/20 rounded-full blur-[120px] mix-blend-screen -translate-x-1/3 translate-y-1/3 animate-float-delayed"></div>

            <div class="relative z-10 w-full max-w-lg mx-auto">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl flex items-center justify-center text-4xl mb-12 shadow-2xl shadow-black/20">
                    ♻️
                </div>
                
                <h1 class="text-5xl lg:text-6xl font-extrabold text-white mb-6 tracking-tight leading-[1.1]">
                    Smart<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-200 to-rose-400">
                        Waste Collection
                    </span>
                </h1>
                
                <p class="text-rose-100/80 text-lg lg:text-xl leading-relaxed font-medium mb-12">
                    Join your community in keeping the environment clean. Report issues, check schedules, and earn rewards for proper waste disposal.
                </p>

                <div class="flex items-center gap-4 text-sm font-bold text-white/60 tracking-wider uppercase">
                    <span>Barangay San Isidro</span>
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Talibon, Bohol</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Clean White Forms -->
        <div class="w-full md:w-7/12 lg:w-1/2 bg-white flex flex-col relative h-screen shadow-[-20px_0_40px_rgba(0,0,0,0.05)] z-20">
            
            <!-- Static Header Area -->
            <div class="w-full p-6 sm:px-12 sm:pt-12 shrink-0 bg-white">
                <Link href="/" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-rose-600 transition-colors group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Home
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar flex flex-col items-center justify-center p-6 sm:px-12 sm:pb-12">
                <div class="w-full max-w-[440px] mx-auto py-2">
                    
                    <!-- Sleek Segmented Control Tab Switcher -->
                    <div class="bg-rose-50/50 p-1.5 rounded-2xl flex items-center mb-10 w-full shadow-inner border border-rose-100">
                        <button 
                            @click="switchTab(true)" 
                            class="flex-1 py-3 rounded-xl text-sm font-bold transition-all duration-300"
                            :class="isLogin ? 'bg-white text-rose-900 shadow-[0_4px_12px_rgba(225,29,72,0.05)] border border-rose-100' : 'text-rose-900/60 hover:text-rose-900'"
                        >
                            Sign In
                        </button>
                        <button 
                            @click="switchTab(false)" 
                            class="flex-1 py-3 rounded-xl text-sm font-bold transition-all duration-300"
                            :class="!isLogin ? 'bg-white text-rose-900 shadow-[0_4px_12px_rgba(225,29,72,0.05)] border border-rose-100' : 'text-rose-900/60 hover:text-rose-900'"
                        >
                            Create Account
                        </button>
                    </div>

                    <!-- Login Form -->
                    <div v-if="isLogin" class="animate-fadeSlideUp w-full">
                        <div class="mb-10 text-center sm:text-left">
                            <h2 class="text-3xl font-extrabold text-rose-950 mb-3 tracking-tight">Welcome Back</h2>
                            <p class="text-rose-900/60 font-medium">Please enter your phone number to receive a secure login code.</p>
                        </div>

                        <form @submit.prevent="sendOtp" class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-rose-950 mb-2">Phone Number</label>
                                <input
                                    v-model="loginForm.phone"
                                    type="tel"
                                    placeholder="09XXXXXXXXX"
                                    class="w-full bg-rose-50/50 border border-rose-100 rounded-2xl px-5 py-4 text-rose-950 placeholder-rose-900/40 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium text-lg"
                                />
                                <p v-if="loginForm.errors.phone" class="text-rose-500 text-sm mt-2 font-bold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    {{ loginForm.errors.phone }}
                                </p>
                            </div>

                            <button
                                type="submit"
                                :disabled="loginForm.processing"
                                class="w-full bg-gradient-to-r from-rose-700 to-rose-900 text-white py-4 rounded-2xl text-base font-bold hover:from-rose-800 hover:to-rose-950 transition-all duration-300 shadow-[0_8px_25px_rgba(225,29,72,0.25)] hover:shadow-[0_12px_35px_rgba(225,29,72,0.35)] hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2 mt-4"
                            >
                                <span>Send Secure Code</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>
                    </div>

                    <!-- Register Form -->
                    <div v-else class="animate-fadeSlideUp w-full">
                        <div class="mb-10 text-center sm:text-left">
                            <h2 class="text-3xl font-extrabold text-rose-950 mb-3 tracking-tight">Join Your Neighbors</h2>
                            <p class="text-rose-900/60 font-medium">Register your household details to get started with SmartWaste.</p>
                        </div>

                        <form @submit.prevent="submitRegister" class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-rose-950 mb-2">Full Name</label>
                                <input v-model="registerForm.name" type="text" placeholder="Juan Dela Cruz" class="w-full bg-rose-50/50 border border-rose-100 rounded-2xl px-5 py-4 text-rose-950 placeholder-rose-900/40 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium" />
                                <p v-if="registerForm.errors.name" class="text-rose-500 text-sm mt-2 font-bold">{{ registerForm.errors.name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-rose-950 mb-2">Phone Number</label>
                                <input v-model="registerForm.phone" type="tel" placeholder="09XXXXXXXXX" class="w-full bg-rose-50/50 border border-rose-100 rounded-2xl px-5 py-4 text-rose-950 placeholder-rose-900/40 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium" />
                                <p v-if="registerForm.errors.phone" class="text-rose-500 text-sm mt-2 font-bold">{{ registerForm.errors.phone }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-rose-950 mb-2">Address</label>
                                <input v-model="registerForm.address" type="text" placeholder="House No., Street, Barangay" class="w-full bg-rose-50/50 border border-rose-100 rounded-2xl px-5 py-4 text-rose-950 placeholder-rose-900/40 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium" />
                                <p v-if="registerForm.errors.address" class="text-rose-500 text-sm mt-2 font-bold">{{ registerForm.errors.address }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Street</label>
                                <div class="relative">
                                    <select v-model="registerForm.street_id" class="w-full bg-rose-50/50 border border-rose-100 rounded-2xl px-5 py-4 appearance-none focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium cursor-pointer" :class="!registerForm.street_id ? 'text-rose-900/40' : 'text-rose-950'">
                                        <option value="" disabled>Select your street</option>
                                        <option v-for="street in streets" :key="street.id" :value="street.id" class="text-rose-950">{{ street.name }} ({{ street.zone.name }})</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-5 pointer-events-none">
                                        <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center border border-rose-100">
                                            <svg class="w-4 h-4 text-rose-900/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                <p v-if="registerForm.errors.street_id" class="text-rose-500 text-sm mt-2 font-bold">{{ registerForm.errors.street_id }}</p>
                            </div>

                            <button type="submit" :disabled="registerForm.processing" class="w-full bg-gradient-to-r from-rose-700 to-rose-900 text-white py-4 rounded-2xl text-base font-bold hover:from-rose-800 hover:to-rose-950 transition-all duration-300 shadow-[0_8px_25px_rgba(225,29,72,0.25)] hover:shadow-[0_12px_35px_rgba(225,29,72,0.35)] hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2 mt-8">
                                <span>Register Account</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- OTP Modal -->
        <Modal :show="showOtpModal" title="Security Verification" @close="showOtpModal = false">
            <div class="text-center pb-2 px-4">
                <div class="w-20 h-20 bg-rose-50 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner border border-rose-100">
                    <span class="text-3xl">🔐</span>
                </div>
                <p class="text-slate-500 font-medium mb-6">
                    We sent a 6-digit secure code to <br/>
                    <span class="font-bold text-slate-900 text-xl tracking-wide block mt-2">{{ loginForm.phone }}</span>
                </p>

                <form @submit.prevent="verifyOtp">
                    <div class="mb-8">
                        <input
                            v-model="otpForm.code"
                            type="text"
                            maxlength="6"
                            placeholder="••••••"
                            class="w-full bg-slate-50 border-2 border-slate-200 rounded-2xl px-4 py-4 text-3xl font-extrabold focus:outline-none focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 tracking-[0.5em] text-center text-slate-900 transition-all duration-300"
                        />
                        <p v-if="otpForm.errors.code" class="text-rose-500 text-sm mt-3 font-bold">{{ otpForm.errors.code }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="otpForm.processing"
                        class="w-full bg-gradient-to-r from-rose-700 to-rose-900 text-white py-4 rounded-2xl text-base font-bold hover:from-rose-800 hover:to-rose-950 transition-all duration-300 shadow-[0_8px_25px_rgba(225,29,72,0.25)] hover:shadow-[0_12px_35px_rgba(225,29,72,0.35)] disabled:opacity-70"
                    >
                        Verify & Sign In
                    </button>
                </form>
            </div>
            
            <template #footer>
                <div class="w-full flex justify-center py-2">
                    <button @click="showOtpModal = false" class="text-sm font-bold text-slate-400 hover:text-slate-700 transition-colors">Cancel Authentication</button>
                </div>
            </template>
        </Modal>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" :closeable="false" max-width="sm">
            <div class="text-center py-8 px-4">
                <div class="w-24 h-24 bg-emerald-50 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner border border-emerald-100">
                    <span class="text-5xl">✨</span>
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 mb-4 tracking-tight">Application Sent!</h2>
                <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                    Your account is currently pending approval by the Barangay Official. You will receive an SMS notification once your account is active.
                </p>
                <button
                    @click="switchTab(true); showSuccessModal = false;"
                    class="block w-full bg-slate-900 text-white py-4 rounded-2xl text-base font-bold hover:bg-slate-800 transition-all duration-300 shadow-xl shadow-slate-900/20"
                >
                    Return to Login
                </button>
            </div>
        </Modal>

    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, usePage, router, Head, Link } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    activeTab: String,
    streets: Array,
})

const page = usePage()
const isLogin = ref(props.activeTab === 'login')

watch(() => props.activeTab, (newVal) => {
    isLogin.value = newVal === 'login'
})

const switchTab = (toLogin) => {
    isLogin.value = toLogin;
    router.visit(toLogin ? route('login') : route('register'), {
        preserveState: true,
        preserveScroll: true,
    })
}

// ========================
// Login Logic
// ========================
const showOtpModal = ref(false)
const loginForm = useForm({ phone: '' })
const otpForm = useForm({ phone: '', code: '' })

watch(() => page.props.flash?.otpSent, (val) => {
    if (val) {
        otpForm.phone = loginForm.phone
        showOtpModal.value = true
    }
})

const sendOtp = () => { loginForm.post(route('login.send-otp')) }
const verifyOtp = () => { otpForm.post(route('login.verify-otp')) }

// ========================
// Register Logic
// ========================
const showSuccessModal = ref(false)
const registerForm = useForm({
    name: '',
    phone: '',
    address: '',
    street_id: '',
})

watch(() => page.props.flash?.success, (val) => {
    if (val) showSuccessModal.value = true
})

const submitRegister = () => { registerForm.post(route('register.store')) }
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1; /* slate-300 */
    border-radius: 20px;
}

.animate-fadeSlideUp {
    animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeSlideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
