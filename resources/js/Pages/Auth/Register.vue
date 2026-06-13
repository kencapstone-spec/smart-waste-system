<template>
    <GuestLayout>
        <div class="bg-white/10 backdrop-blur-xl p-8 rounded-3xl shadow-2xl border border-white/20 w-full">
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-4 shadow-inner text-white">
                    <component :is="UserPlus" class="w-6 h-6" />
                </div>
                <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">Create Account</h1>
                <p class="text-rose-200/80 text-sm">Join SmartWaste today</p>
            </div>

            <form @submit.prevent="submit">
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-rose-100 mb-1.5">Full Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Juan Dela Cruz"
                            class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-rose-200/40 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all duration-300"
                        />
                        <p v-if="form.errors.name" class="text-red-300 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-rose-100 mb-1.5">Phone Number</label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            placeholder="09XXXXXXXXX"
                            class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-rose-200/40 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all duration-300"
                        />
                        <p v-if="form.errors.phone" class="text-red-300 text-xs mt-1">{{ form.errors.phone }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-rose-100 mb-1.5">Address</label>
                        <input
                            v-model="form.address"
                            type="text"
                            placeholder="House No., Street, Barangay"
                            class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-rose-200/40 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all duration-300"
                        />
                        <p v-if="form.errors.address" class="text-red-300 text-xs mt-1">{{ form.errors.address }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-rose-100 mb-1.5">Zone</label>
                        <div class="relative">
                            <select
                                v-model="form.zone_id"
                                class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-2.5 text-white appearance-none focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all duration-300"
                                :class="!form.zone_id ? 'text-rose-200/40' : 'text-white'"
                            >
                                <option value="" disabled class="text-gray-900">Select a zone</option>
                                <option v-for="zone in zones" :key="zone.id" :value="zone.id" class="text-gray-900">
                                    {{ zone.name }}
                                </option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-white/50">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        <p v-if="form.errors.zone_id" class="text-red-300 text-xs mt-1">{{ form.errors.zone_id }}</p>
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-white text-rose-900 py-3 rounded-xl text-sm font-bold hover:bg-rose-50 transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_25px_rgba(255,255,255,0.2)] disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    Register Account
                </button>
            </form>

            <p class="text-center text-sm text-rose-200/60 mt-6">
                Already have an account?
                <a :href="route('login')" class="text-white font-medium hover:text-rose-200 hover:underline transition-colors">Sign in here</a>
            </p>
        </div>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" :closeable="false" max-width="sm">
            <div class="text-center py-6 px-2">
                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-500">
                    <component :is="CheckCircle" class="w-10 h-10" />
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2 tracking-tight">Registration Submitted</h2>
                <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                    Your account is currently pending approval by the Barangay Official. You will receive an SMS notification once your account is active.
                </p>
                <a
                    :href="route('login')"
                    class="block w-full bg-rose-900 text-white py-3 rounded-xl text-sm font-bold hover:bg-rose-800 transition-all duration-300 shadow-lg shadow-rose-900/20"
                >
                    Return to Login
                </a>
            </div>
        </Modal>
    </GuestLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { UserPlus, CheckCircle } from '@lucide/vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    zones: Array,
})

const page = usePage()
const showSuccessModal = ref(false)

const form = useForm({
    name: '',
    phone: '',
    address: '',
    zone_id: '',
})

watch(() => page.props.flash?.success, (val) => {
    if (val) showSuccessModal.value = true
})

const submit = () => {
    form.post(route('register.store'))
}
</script>