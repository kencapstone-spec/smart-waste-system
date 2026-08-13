<template>
    <AuthLayout page-title="Resident Management">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-rose-950 tracking-tight">Residents</h2>
            <button
                @click="showRegisterModal = true"
                class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all"
            >
                + Register Resident
            </button>
        </div>

        <div class="mb-6 flex gap-3 flex-wrap">
            <button
                @click="filter = 'all'"
                :class="filter === 'all' ? 'bg-green-600 text-white' : 'bg-white text-rose-950/80 border'"
                class="px-4 py-2 rounded-md text-sm font-medium transition"
            >All</button>
            <button
                @click="filter = 'pending'"
                :class="filter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-white text-rose-950/80 border'"
                class="px-4 py-2 rounded-md text-sm font-medium transition"
            >Pending</button>
            <button
                @click="filter = 'active'"
                :class="filter === 'active' ? 'bg-green-600 text-white' : 'bg-white text-rose-950/80 border'"
                class="px-4 py-2 rounded-md text-sm font-medium transition"
            >Active</button>
            <button
                @click="filter = 'rejected'"
                :class="filter === 'rejected' ? 'bg-red-500 text-white' : 'bg-white text-rose-950/80 border'"
                class="px-4 py-2 rounded-md text-sm font-medium transition"
            >Rejected</button>
        </div>

        <div class="bg-white/70 backdrop-blur-2xl sm:rounded-2xl shadow-xl shadow-rose-900/5 sm:border border-white/60 -mx-4 sm:mx-0 overflow-hidden">
            <div class="overflow-x-auto  scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="User" class="w-4 h-4 opacity-70" /> Name</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Phone" class="w-4 h-4 opacity-70" /> Phone</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="MapPin" class="w-4 h-4 opacity-70" /> Zone</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Activity" class="w-4 h-4 opacity-70" /> Status</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Registered</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Settings" class="w-4 h-4 opacity-70" /> Actions</div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="resident in filteredResidents" :key="resident.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-rose-950 font-semibold">{{ resident.name }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ resident.phone }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ resident.zone?.name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span :class="statusClass(resident.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                {{ resident.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ formatDate(resident.created_at) }}</td>
                        <td class="px-6 py-4 flex items-center gap-1.5">
                            <button @click="viewResident(resident)" class="px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors bg-blue-50 text-blue-700 hover:bg-blue-100">View</button>
                            <button
                                v-if="resident.status === 'pending'"
                                @click="approveResident(resident)"
                                class="px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors bg-emerald-50 text-emerald-700 hover:bg-emerald-100"
                            >Approve</button>
                            <button
                                v-if="resident.status === 'pending'"
                                @click="rejectResident(resident)"
                                class="px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors bg-rose-50 text-rose-700 hover:bg-rose-100"
                            >Reject</button>
                            <button
                                v-if="resident.status === 'active'"
                                @click="deactivateResident(resident)"
                                class="px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors bg-amber-50 text-amber-700 hover:bg-amber-100"
                            >Deactivate</button>
                            <button
                                v-if="resident.status === 'rejected'"
                                @click="reactivateResident(resident)"
                                class="px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors bg-emerald-50 text-emerald-700 hover:bg-emerald-100"
                            >Reactivate</button>
                            <button
                                @click="deleteResident(resident)"
                                class="px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors bg-red-50 text-red-700 hover:bg-red-100"
                            >Delete</button>
                        </td>
                    </tr>
                    <tr v-if="filteredResidents.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No residents found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Manual Registration Modal -->
        <Modal :show="showRegisterModal" title="Register Resident" @close="showRegisterModal = false">
            <form @submit.prevent="submitRegister" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-rose-950 mb-1">Full Name</label>
                    <input
                        v-model="registerForm.name"
                        type="text"
                        placeholder="Juan Dela Cruz"
                        class="w-full border border-rose-100 bg-rose-50/50 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none transition-all"
                        required
                    />
                    <p v-if="registerForm.errors.name" class="text-red-500 text-xs mt-1">{{ registerForm.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-rose-950 mb-1">Phone Number</label>
                    <input
                        v-model="registerForm.phone"
                        type="tel"
                        placeholder="09XXXXXXXXX"
                        class="w-full border border-rose-100 bg-rose-50/50 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none transition-all"
                        required
                    />
                    <p v-if="registerForm.errors.phone" class="text-red-500 text-xs mt-1">{{ registerForm.errors.phone }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-rose-950 mb-1">Address</label>
                    <input
                        v-model="registerForm.address"
                        type="text"
                        placeholder="House No., Purok, Barangay"
                        class="w-full border border-rose-100 bg-rose-50/50 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none transition-all"
                        required
                    />
                    <p v-if="registerForm.errors.address" class="text-red-500 text-xs mt-1">{{ registerForm.errors.address }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-rose-950 mb-1">Purok / Zone</label>
                    <select
                        v-model="registerForm.zone_id"
                        class="w-full border border-rose-100 bg-rose-50/50 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none transition-all"
                        required
                    >
                        <option value="" disabled>Select a Purok</option>
                        <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                    </select>
                    <p v-if="registerForm.errors.zone_id" class="text-red-500 text-xs mt-1">{{ registerForm.errors.zone_id }}</p>
                </div>
                <p class="text-xs text-rose-900/50 bg-rose-50 rounded-lg px-3 py-2">
                    ℹ️ Manually registered residents are <strong>auto-approved</strong> and can log in immediately.
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showRegisterModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors px-4 py-2">Cancel</button>
                    <button
                        type="submit"
                        :disabled="registerForm.processing"
                        class="bg-rose-900 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all disabled:opacity-70"
                    >Register</button>
                </div>
            </form>
        </Modal>

        <!-- View Resident Modal -->
        <Modal :show="showViewModal" title="Resident Details" max-width="lg" @close="showViewModal = false">
            <div v-if="selectedResident" class="space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Name</p>
                        <p class="font-medium text-gray-800">{{ selectedResident.name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Phone</p>
                        <p class="text-gray-800">{{ selectedResident.phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Address</p>
                        <p class="text-gray-800">{{ selectedResident.address ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Zone</p>
                        <p class="text-gray-800">{{ selectedResident.zone?.name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Status</p>
                        <span :class="statusClass(selectedResident.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">{{ selectedResident.status }}</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Registered</p>
                        <p class="text-gray-800">{{ formatDate(selectedResident.created_at) }}</p>
                    </div>
                </div>

                <div class="border-t pt-4 flex gap-3 justify-end">
                    <template v-if="selectedResident.status === 'pending'">
                        <button @click="rejectResident(selectedResident)" class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600 transition">Reject</button>
                        <button @click="approveResident(selectedResident)" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all transition">Approve</button>
                    </template>
                    <template v-if="selectedResident.status === 'active'">
                        <button @click="deactivateResident(selectedResident)" class="bg-orange-500 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-600 transition">Deactivate</button>
                    </template>
                    <template v-if="selectedResident.status === 'rejected'">
                        <button @click="reactivateResident(selectedResident)" class="bg-green-500 text-white px-4 py-2 rounded-md text-sm hover:bg-green-600 transition">Reactivate</button>
                    </template>
                    <button @click="deleteResident(selectedResident)" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 transition">Delete</button>
                </div>
            </div>
        </Modal>

        <!-- Approve Confirmation Modal -->
        <Modal :show="showApproveModal" title="Approve Resident" @close="showApproveModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Approve <span class="font-semibold">{{ selectedResident?.name }}</span>? They will receive an SMS notification.</p>
            <div class="flex justify-end gap-3">
                <button @click="showApproveModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitApprove" :disabled="actionForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Approve</button>
            </div>
        </Modal>

        <!-- Reject Confirmation Modal -->
        <Modal :show="showRejectModal" title="Reject Resident" @close="showRejectModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Reject <span class="font-semibold">{{ selectedResident?.name }}</span>? They will receive an SMS notification.</p>
            <div class="flex justify-end gap-3">
                <button @click="showRejectModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitReject" :disabled="actionForm.processing" class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600">Reject</button>
            </div>
        </Modal>

        <!-- Deactivate Confirmation Modal -->
        <Modal :show="showDeactivateModal" title="Deactivate Resident" @close="showDeactivateModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Deactivate <span class="font-semibold">{{ selectedResident?.name }}</span>? This will change their status to rejected and they will be notified via SMS.</p>
            <div class="flex justify-end gap-3">
                <button @click="showDeactivateModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitDeactivate" :disabled="actionForm.processing" class="bg-orange-500 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-600">Deactivate</button>
            </div>
        </Modal>

        <!-- Reactivate Confirmation Modal -->
        <Modal :show="showReactivateModal" title="Reactivate Resident" @close="showReactivateModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Reactivate <span class="font-semibold">{{ selectedResident?.name }}</span>? This will restore their access to the system and they will be notified via SMS.</p>
            <div class="flex justify-end gap-3">
                <button @click="showReactivateModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitReactivate" :disabled="actionForm.processing" class="bg-green-500 text-white px-4 py-2 rounded-md text-sm hover:bg-green-600">Reactivate</button>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" title="Delete Resident Account" @close="showDeleteModal = false">
            <div class="mb-6">
                <p class="text-gray-800 text-sm font-semibold mb-2">Are you sure you want to permanently delete <span class="font-bold text-red-600">{{ selectedResident?.name }}</span>?</p>
                <p class="text-red-500 text-xs">Warning: This will permanently delete their account and any associated points/records. This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitDelete" :disabled="actionForm.processing" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">Yes, Delete</button>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { User, Phone, MapPin, Activity, Settings } from '@lucide/vue'
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    residents: Object,
    zones: Array,
})

const filter = ref('all')
const showRegisterModal = ref(false)
const showViewModal = ref(false)
const showApproveModal = ref(false)
const showRejectModal = ref(false)
const showDeactivateModal = ref(false)
const showReactivateModal = ref(false)
const showDeleteModal = ref(false)
const selectedResident = ref(null)
const actionForm = useForm({})

const registerForm = useForm({
    name: '',
    phone: '',
    address: '',
    zone_id: '',
})

const submitRegister = () => {
    registerForm.post(route('official.residents.store'), {
        onSuccess: () => {
            showRegisterModal.value = false
            registerForm.reset()
        },
    })
}

const filteredResidents = computed(() => {
    if (filter.value === 'all') return props.residents.data
    return props.residents.data.filter(r => r.status === filter.value)
})

const statusClass = (status) => ({
    active: 'bg-green-100 text-green-700',
    pending: 'bg-yellow-100 text-yellow-700',
    rejected: 'bg-red-100 text-red-700',
}[status] ?? 'bg-gray-100 text-gray-700')

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})

const viewResident = (resident) => {
    selectedResident.value = resident
    showViewModal.value = true
}

const approveResident = (resident) => {
    selectedResident.value = resident
    showViewModal.value = false
    showApproveModal.value = true
}

const rejectResident = (resident) => {
    selectedResident.value = resident
    showViewModal.value = false
    showRejectModal.value = true
}

const submitApprove = () => {
    actionForm.post(route('official.residents.approve', selectedResident.value.id), {
        onSuccess: () => showApproveModal.value = false,
    })
}

const submitReject = () => {
    actionForm.post(route('official.residents.reject', selectedResident.value.id), {
        onSuccess: () => showRejectModal.value = false,
    })
}

const deactivateResident = (resident) => {
    selectedResident.value = resident
    showViewModal.value = false
    showDeactivateModal.value = true
}

const reactivateResident = (resident) => {
    selectedResident.value = resident
    showViewModal.value = false
    showReactivateModal.value = true
}

const deleteResident = (resident) => {
    selectedResident.value = resident
    showViewModal.value = false
    showDeleteModal.value = true
}

const submitDeactivate = () => {
    actionForm.post(route('official.residents.deactivate', selectedResident.value.id), {
        onSuccess: () => showDeactivateModal.value = false,
    })
}

const submitReactivate = () => {
    actionForm.post(route('official.residents.reactivate', selectedResident.value.id), {
        onSuccess: () => showReactivateModal.value = false,
    })
}

const submitDelete = () => {
    actionForm.delete(route('official.residents.destroy', selectedResident.value.id), {
        onSuccess: () => showDeleteModal.value = false,
    })
}
</script>