<template>
    <AuthLayout page-title="Resident Details">
        <!-- Back button -->
        <div class="mb-6">
            <Link :href="route('official.residents.index')" class="inline-flex items-center gap-2 text-sm text-rose-900/60 hover:text-rose-900 transition-colors transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Residents
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Resident Info Card -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center text-green-700 text-xl font-bold">
                            {{ initials(resident.name) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">{{ resident.name }}</h2>
                            <span :class="statusClass(resident.status)" class="px-2 py-0.5 rounded-full text-xs font-medium capitalize">
                                {{ resident.status }}
                            </span>
                        </div>
                    </div>

                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Phone</dt>
                            <dd class="text-gray-700 font-medium">{{ resident.phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Address</dt>
                            <dd class="text-gray-700">{{ resident.address ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Purok</dt>
                            <dd class="text-gray-700">{{ resident.zone?.name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Zone</dt>
                            <dd class="text-gray-700">{{ resident.zone?.name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Registered</dt>
                            <dd class="text-gray-700">{{ formatDate(resident.created_at) }}</dd>
                        </div>
                        <div v-if="resident.approved_at">
                            <dt class="text-xs text-gray-400 mb-0.5">Approved</dt>
                            <dd class="text-gray-700">{{ formatDate(resident.approved_at) }}</dd>
                        </div>
                    </dl>

                    <!-- Action Buttons for pending residents -->
                    <div v-if="resident.status === 'pending'" class="mt-6 flex gap-3">
                        <button
                            @click="showRejectModal = true"
                            :disabled="actionForm.processing"
                            class="flex-1 bg-red-500 text-white py-2 rounded-md text-sm font-medium hover:bg-red-600 transition disabled:opacity-60"
                        >Reject</button>
                        <button
                            @click="showApproveModal = true"
                            :disabled="actionForm.processing"
                            class="flex-1 bg-green-600 text-white py-2 rounded-md text-sm font-medium hover:bg-green-700 transition disabled:opacity-60"
                        >Approve</button>
                    </div>
                </div>

                <!-- Total Points Card -->
                <div class="bg-green-600 rounded-lg shadow-sm p-6 text-white">
                    <p class="text-sm text-green-100 mb-1">Total Points Earned</p>
                    <p class="text-4xl font-bold">{{ totalPoints }}</p>
                    <p class="text-xs text-green-200 mt-1">from {{ points.length }} award{{ points.length !== 1 ? 's' : '' }}</p>
                </div>
            </div>

            <!-- Points History -->
            <div class="lg:col-span-2">
                <div class="bg-white/70 backdrop-blur-2xl rounded-2xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold text-gray-700">Points History</h3>
                    </div>

                    <div v-if="points.length > 0">
                        <div class="overflow-x-auto pb-4">
                            <table class="w-full text-sm whitespace-nowrap">
                            <thead class="border-b border-rose-100/50">
                                <tr>
                                    <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Points</th>
                                    <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Awarded By</th>
                                    <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Remarks</th>
                                    <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Date</th>
                                    <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Points Type</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="point in points" :key="point.id" class="hover:bg-rose-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 text-green-700 font-semibold">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            +{{ point.points }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-rose-950/80">{{ point.awarded_by?.name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-rose-950/80">{{ point.remarks ?? '—' }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ formatDate(point.created_at) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Fixed Award
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>

                    <div v-else class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <p class="text-sm">No points awarded yet.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Confirmation Modal -->
        <Modal :show="showApproveModal" title="Approve Resident" @close="showApproveModal = false">
            <p class="text-rose-950/80 text-sm mb-6">
                Approve <span class="font-semibold">{{ resident.name }}</span>? They will receive an SMS notification.
            </p>
            <div class="flex justify-end gap-3">
                <button @click="showApproveModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitApprove" :disabled="actionForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all disabled:opacity-60">Approve</button>
            </div>
        </Modal>

        <!-- Reject Confirmation Modal -->
        <Modal :show="showRejectModal" title="Reject Resident" @close="showRejectModal = false">
            <p class="text-rose-950/80 text-sm mb-6">
                Reject <span class="font-semibold">{{ resident.name }}</span>? They will receive an SMS notification.
            </p>
            <div class="flex justify-end gap-3">
                <button @click="showRejectModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitReject" :disabled="actionForm.processing" class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600 disabled:opacity-60">Reject</button>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    resident: Object,
    points: Array,
    totalPoints: Number,
})

const showApproveModal = ref(false)
const showRejectModal = ref(false)
const actionForm = useForm({})

const initials = (name) => name
    ? name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
    : '?'

const statusClass = (status) => ({
    active: 'bg-green-100 text-green-700',
    pending: 'bg-yellow-100 text-yellow-700',
    rejected: 'bg-red-100 text-red-700',
}[status] ?? 'bg-gray-100 text-gray-700')

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})

const submitApprove = () => {
    actionForm.post(route('official.residents.approve', props.resident.id), {
        onSuccess: () => showApproveModal.value = false,
    })
}

const submitReject = () => {
    actionForm.post(route('official.residents.reject', props.resident.id), {
        onSuccess: () => showRejectModal.value = false,
    })
}
</script>
