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
                    <span class="text-white">
                        Waste Collection
                    </span>
                </h1>
                
                <p class="text-white text-lg lg:text-xl leading-relaxed font-medium mb-12">
                    Join your community in keeping the environment clean. Report issues, check schedules, and earn rewards for proper waste disposal.
                </p>

                <div class="flex items-center gap-4 text-sm font-bold text-white tracking-wider uppercase">
                    <span>Barangay San Isidro</span>
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Talibon, Bohol</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Clean White Forms -->
        <div class="w-full md:w-7/12 lg:w-1/2 bg-white flex flex-col relative min-h-screen md:h-screen shadow-[-20px_0_40px_rgba(0,0,0,0.05)] z-20">
            
            <!-- Static Header Area -->
            <div class="w-full pt-6 pb-2 px-6 sm:px-12 sm:pt-8 sm:pb-3 shrink-0 bg-white">
                <Link href="/" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-rose-600 transition-colors group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Home
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar flex flex-col items-center px-6 pt-2 pb-12 sm:px-12 sm:pt-4 sm:pb-16">
                <div class="w-full max-w-[440px] mx-auto my-auto">
                    
                    <!-- Sleek Segmented Control Tab Switcher -->
                    <div class="bg-rose-50/50 p-1 sm:p-1.5 rounded-2xl flex items-center mb-4 sm:mb-6 w-full shadow-inner border border-rose-100">
                        <button 
                            @click="switchTab(true)" 
                            class="flex-1 py-2.5 sm:py-3 rounded-xl text-sm font-bold transition-all duration-300"
                            :class="isLogin ? 'bg-white text-rose-900 shadow-[0_4px_12px_rgba(225,29,72,0.05)] border border-rose-100' : 'text-rose-900/60 hover:text-rose-900'"
                        >
                            Sign In
                        </button>
                        <button 
                            @click="switchTab(false)" 
                            class="flex-1 py-2.5 sm:py-3 rounded-xl text-sm font-bold transition-all duration-300"
                            :class="!isLogin ? 'bg-white text-rose-900 shadow-[0_4px_12px_rgba(225,29,72,0.05)] border border-rose-100' : 'text-rose-900/60 hover:text-rose-900'"
                        >
                            Create Account
                        </button>
                    </div>

                    <!-- Login Form -->
                    <div v-if="isLogin" class="animate-fadeSlideUp w-full">
                        <div class="mb-5 sm:mb-6 text-center sm:text-left">
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-rose-950 mb-1.5 sm:mb-3 tracking-tight">Welcome Back</h2>
                            <p class="text-rose-900/60 font-medium text-sm">Enter your phone number to receive a secure login code.</p>
                        </div>

                        <form @submit.prevent="sendOtp" class="space-y-4 sm:space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-rose-950 mb-1.5 sm:mb-2">Phone Number</label>
                                <input
                                    v-model="loginForm.phone"
                                    type="tel"
                                    placeholder="09XXXXXXXXX"
                                    class="w-full bg-rose-50/50 border border-rose-100 rounded-2xl px-5 py-3 sm:py-3.5 text-rose-950 placeholder-rose-900/40 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium text-base sm:text-lg"
                                />
                                <p v-if="loginForm.errors.phone" class="text-rose-500 text-sm mt-2 font-bold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    {{ loginForm.errors.phone }}
                                </p>
                            </div>

                            <button
                                type="submit"
                                :disabled="loginForm.processing"
                                class="w-full bg-gradient-to-r from-rose-700 to-rose-900 text-white py-3 sm:py-3.5 rounded-2xl text-base font-bold hover:from-rose-800 hover:to-rose-950 transition-all duration-300 shadow-[0_8px_25px_rgba(225,29,72,0.25)] hover:shadow-[0_12px_35px_rgba(225,29,72,0.35)] hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2 mt-4"
                            >
                                <span>Send Secure Code</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>
                    </div>

                    <!-- Register Form -->
                    <div v-else class="animate-fadeSlideUp w-full">
                        <div class="mb-4 sm:mb-6 text-center sm:text-left">
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-rose-950 mb-1 sm:mb-2 tracking-tight">Join Your Neighbors</h2>
                            <p class="text-rose-900/60 font-medium text-sm">Register your household details to get started with SmartWaste.</p>
                        </div>

                        <form @submit.prevent="submitRegister" class="space-y-3 sm:space-y-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-rose-950 mb-1 sm:mb-2">Full Name</label>
                                <input v-model="registerForm.name" type="text" placeholder="Juan Dela Cruz" class="w-full bg-rose-50/50 border border-rose-100 rounded-xl sm:rounded-2xl px-4 sm:px-5 py-2.5 sm:py-3.5 text-rose-950 placeholder-rose-900/40 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium text-sm" />
                                <p v-if="registerForm.errors.name" class="text-rose-500 text-xs mt-1 font-bold">{{ registerForm.errors.name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-rose-950 mb-1 sm:mb-2">Phone Number</label>
                                <input v-model="registerForm.phone" type="tel" placeholder="09XXXXXXXXX" class="w-full bg-rose-50/50 border border-rose-100 rounded-xl sm:rounded-2xl px-4 sm:px-5 py-2.5 sm:py-3.5 text-rose-950 placeholder-rose-900/40 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium text-sm" />
                                <p v-if="registerForm.errors.phone" class="text-xs mt-1 font-bold text-rose-500">{{ registerForm.errors.phone }}</p>
                            </div>

                            <div>
                                <label class="block text-xs sm:text-sm font-bold text-rose-950 mb-1 sm:mb-2">Address</label>
                                <input v-model="registerForm.address" type="text" placeholder="House No., Purok, Barangay" class="w-full bg-rose-50/50 border border-rose-100 rounded-xl sm:rounded-2xl px-4 sm:px-5 py-2.5 sm:py-3.5 text-rose-950 placeholder-rose-900/40 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 font-medium text-sm" />
                                <p v-if="registerForm.errors.address" class="text-rose-500 text-xs mt-1 font-bold">{{ registerForm.errors.address }}</p>
                            </div>

                            <div class="relative" ref="zoneDropdownRef">
                                <label class="block text-xs sm:text-sm font-bold text-rose-950 mb-1 sm:mb-2">Purok / Zone</label>
                                <!-- Trigger -->
                                <button
                                    type="button"
                                    @click="zoneDropdownOpen = !zoneDropdownOpen"
                                    class="w-full bg-rose-50/50 border border-rose-100 rounded-xl sm:rounded-2xl px-4 sm:px-5 py-2.5 sm:py-3.5 flex items-center justify-between gap-2 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-300 text-sm font-medium"
                                    :class="zoneDropdownOpen ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-rose-100'"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="h-4 w-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span :class="registerForm.zone_id ? 'text-rose-950' : 'text-rose-900/40'">{{ selectedZoneLabel }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-rose-400 transition-transform duration-200" :class="zoneDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <!-- Options List -->
                                <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                                    <div v-if="zoneDropdownOpen" class="absolute left-0 right-0 mt-2 bg-white border border-rose-100 rounded-2xl shadow-xl z-50 overflow-hidden">
                                        <div class="max-h-52 overflow-y-auto py-1">
                                            <div v-if="!zones || zones.length === 0" class="px-4 py-3 text-xs text-gray-400 text-center">
                                                No zones available
                                            </div>
                                            <button
                                                v-for="zone in zones"
                                                :key="zone.id"
                                                type="button"
                                                @click="selectZone(zone)"
                                                class="w-full px-4 py-2.5 text-left text-sm font-medium transition-colors flex items-center gap-2"
                                                :class="registerForm.zone_id === zone.id ? 'bg-rose-50 text-rose-900 font-bold' : 'text-gray-700 hover:bg-rose-50 hover:text-rose-900'"
                                            >
                                                <span v-if="registerForm.zone_id === zone.id" class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                                <span v-else class="w-1.5 h-1.5 shrink-0"></span>
                                                {{ zone.name }}
                                            </button>
                                        </div>
                                    </div>
                                </transition>
                                <p v-if="registerForm.errors.zone_id" class="text-rose-500 text-xs mt-1 font-bold">{{ registerForm.errors.zone_id }}</p>
                            </div>

                            <button type="submit" :disabled="registerForm.processing" class="w-full bg-gradient-to-r from-rose-700 to-rose-900 text-white py-3 sm:py-3.5 rounded-2xl text-sm sm:text-base font-bold hover:from-rose-800 hover:to-rose-950 transition-all duration-300 shadow-[0_8px_25px_rgba(225,29,72,0.25)] hover:shadow-[0_12px_35px_rgba(225,29,72,0.35)] hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2 mt-4 sm:mt-6">
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
                    <span class="text-3xl"><component :is="ShieldCheck" class="w-10 h-10 mx-auto text-rose-900" /></span>
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
                    <span class="text-5xl"><component :is="Sparkles" class="w-6 h-6 inline text-rose-400" /></span>
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
import { ShieldCheck, Sparkles } from '@lucide/vue';
import { ref, watch, computed, onMounted, onUnmounted } from 'vue'
import { useForm, usePage, router, Head, Link } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    activeTab: String,
    zones: Array,
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
    zone_id: '',
})

const zoneDropdownOpen = ref(false)
const zoneDropdownRef = ref(null)

const selectedZoneLabel = computed(() => {
    if (!registerForm.zone_id) return 'Select your zone'
    const zone = props.zones.find(z => z.id === registerForm.zone_id)
    return zone ? zone.name : 'Select your zone'
})

const selectZone = (zone) => {
    registerForm.zone_id = zone.id
    zoneDropdownOpen.value = false
}

const closeDropdown = (e) => {
    if (zoneDropdownRef.value && !zoneDropdownRef.value.contains(e.target)) {
        zoneDropdownOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown)
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
