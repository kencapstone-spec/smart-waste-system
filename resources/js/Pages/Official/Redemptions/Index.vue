<template>
    <AuthLayout page-title="Redemptions Management">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1 scrollbar-none">
            <button
                v-for="status in ['all', 'pending', 'approved', 'rejected']"
                :key="status"
                @click="activeFilter = status"
                :class="activeFilter === status
                    ? 'bg-rose-900 text-white shadow-md shadow-rose-900/20'
                    : 'bg-white text-rose-950/70 hover:bg-rose-50/70 border border-rose-100'"
                class="px-4 py-2 rounded-xl text-xs font-bold capitalize transition-all flex items-center gap-2 whitespace-nowrap"
            >
                <span>{{ status }}</span>
                <span
                    :class="activeFilter === status ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-900'"
                    class="text-[11px] px-2 py-0.5 rounded-full font-bold"
                >
                    {{ countByStatus(status) }}
                </span>
            </button>
        </div>

        <!-- Table Card -->
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-rose-900/5 border border-rose-100/60 overflow-hidden">
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-rose-100/70 bg-rose-50/40 text-xs uppercase tracking-wider text-rose-900/70 font-bold">
                            <th class="px-6 py-4">Date & Time</th>
                            <th class="px-6 py-4">Resident</th>
                            <th class="px-6 py-4">Reward Item</th>
                            <th class="px-6 py-4">Points</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        <tr
                            v-for="redemption in filteredRedemptions"
                            :key="redemption.id"
                            class="hover:bg-rose-50/40 transition-colors"
                        >
                            <!-- Date -->
                            <td class="px-6 py-4">
                                <div class="font-semibold text-rose-950">
                                    {{ formatDate(redemption.created_at) }}
                                </div>
                                <div class="text-[11px] text-gray-400 font-medium">
                                    {{ formatTime(redemption.created_at) }}
                                </div>
                            </td>

                            <!-- Resident -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-100 to-rose-200 text-rose-900 font-bold flex items-center justify-center text-sm shadow-inner shrink-0">
                                        {{ getInitials(redemption.resident?.name) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-rose-950">{{ redemption.resident?.name || 'Unknown Resident' }}</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                            <span class="font-mono">{{ redemption.resident?.phone }}</span>
                                            <span v-if="redemption.resident?.zone" class="px-2 py-0.5 rounded-md bg-rose-50 text-[10px] font-semibold text-rose-800 border border-rose-100">
                                                {{ redemption.resident.zone.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Reward Item -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200/50 shrink-0">
                                        <component :is="Gift" class="w-4 h-4" />
                                    </div>
                                    <span class="font-bold text-gray-800">{{ redemption.reward?.name || 'Reward Item' }}</span>
                                </div>
                            </td>

                            <!-- Points Spent -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-amber-50 border border-amber-200/60 text-amber-700 font-black text-xs">
                                    <component :is="Star" class="w-3.5 h-3.5 fill-amber-400 text-amber-500" />
                                    {{ redemption.points_spent }} pts
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <span
                                    :class="statusBadgeClass(redemption.status)"
                                    class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 capitalize"
                                >
                                    <span
                                        v-if="redemption.status === 'pending'"
                                        class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"
                                    ></span>
                                    <component
                                        v-else-if="redemption.status === 'approved'"
                                        :is="CheckCircle2"
                                        class="w-3.5 h-3.5"
                                    />
                                    <component
                                        v-else
                                        :is="XCircle"
                                        class="w-3.5 h-3.5"
                                    />
                                    {{ redemption.status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div v-if="redemption.status === 'pending'" class="inline-flex items-center gap-2">
                                    <button
                                        @click="openApproveModal(redemption)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow transition-all"
                                    >
                                        <component :is="Check" class="w-3.5 h-3.5" />
                                        Approve
                                    </button>
                                    <button
                                        @click="openRejectModal(redemption)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-xl transition-all"
                                    >
                                        <component :is="X" class="w-3.5 h-3.5" />
                                        Reject
                                    </button>
                                </div>
                                <div v-else-if="redemption.status === 'approved'">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50/80 px-3 py-1 rounded-lg border border-emerald-100">
                                        <component :is="CheckCircle2" class="w-3.5 h-3.5 text-emerald-600" />
                                        Claim Ready
                                    </span>
                                </div>
                                <div v-else>
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-50 px-3 py-1 rounded-lg border border-gray-200">
                                        <component :is="XCircle" class="w-3.5 h-3.5 text-gray-400" />
                                        Refunded
                                    </span>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="filteredRedemptions.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center mx-auto mb-3 text-rose-300">
                                    <component :is="Gift" class="w-6 h-6" />
                                </div>
                                <p class="font-bold text-rose-950/70">No redemptions found</p>
                                <p class="text-xs text-gray-400 mt-1">There are no redemptions matching this filter.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Approve Modal -->
        <Modal :show="showApprove" title="Approve Reward Redemption" max-width="md" @close="showApprove = false">
            <div v-if="selectedRedemption" class="space-y-4">
                <div class="flex items-center gap-3 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-700 flex items-center justify-center shrink-0">
                        <component :is="CheckCircle2" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Ready for Approval</p>
                        <p class="text-sm font-semibold text-emerald-950">
                            Approve claim for <span class="font-bold">{{ selectedRedemption.resident?.name }}</span>?
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-100 space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Reward Item</span>
                        <span class="font-bold text-gray-800">{{ selectedRedemption.reward?.name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Points Deducted</span>
                        <span class="font-bold text-amber-600">{{ selectedRedemption.points_spent }} ⭐</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Phone Number</span>
                        <span class="font-mono text-gray-800">{{ selectedRedemption.resident?.phone }}</span>
                    </div>
                    <div v-if="selectedRedemption.resident?.zone" class="flex justify-between py-1">
                        <span class="text-gray-500">Purok / Zone</span>
                        <span class="font-medium text-gray-800">{{ selectedRedemption.resident.zone.name }}</span>
                    </div>
                </div>

                <p class="text-xs text-gray-500 leading-relaxed">
                    An SMS notification will be automatically sent to the resident informing them that their reward can be claimed at the Barangay Hall.
                </p>

                <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showApprove = false"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="actionForm.processing"
                        @click="submitApprove"
                        class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/20 transition-all disabled:opacity-50 flex items-center gap-1.5"
                    >
                        <span v-if="actionForm.processing">Processing...</span>
                        <span v-else>Confirm & Send SMS</span>
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Reject Modal -->
        <Modal :show="showReject" title="Reject Reward Redemption" max-width="md" @close="showReject = false">
            <div v-if="selectedRedemption" class="space-y-4">
                <div class="flex items-center gap-3 p-4 bg-rose-50 rounded-2xl border border-rose-100">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-700 flex items-center justify-center shrink-0">
                        <component :is="XCircle" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Reject Redemption</p>
                        <p class="text-sm font-semibold text-rose-950">
                            Reject claim for <span class="font-bold">{{ selectedRedemption.resident?.name }}</span>?
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-100 space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Reward Item</span>
                        <span class="font-bold text-gray-800">{{ selectedRedemption.reward?.name }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-gray-500">Refund Amount</span>
                        <span class="font-bold text-amber-600">+{{ selectedRedemption.points_spent }} ⭐ back to resident</span>
                    </div>
                </div>

                <p class="text-xs text-rose-800/80 bg-rose-50/60 p-3 rounded-xl border border-rose-100 leading-relaxed">
                    Rejecting will return 1 item to stock, refund the <span class="font-bold">{{ selectedRedemption.points_spent }} points</span> back to the resident's account, and send an SMS notification.
                </p>

                <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                    <button
                        type="button"
                        @click="showReject = false"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="actionForm.processing"
                        @click="submitReject"
                        class="px-5 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/20 transition-all disabled:opacity-50 flex items-center gap-1.5"
                    >
                        <span v-if="actionForm.processing">Processing...</span>
                        <span v-else>Confirm Rejection</span>
                    </button>
                </div>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { Gift, Star, CheckCircle2, XCircle, Check, X } from '@lucide/vue'
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    redemptions: Object,
})

const activeFilter = ref('all')
const showApprove = ref(false)
const showReject = ref(false)
const selectedRedemption = ref(null)

const actionForm = useForm({})

const filteredRedemptions = computed(() => {
    const list = props.redemptions?.data || []
    if (activeFilter.value === 'all') return list
    return list.filter((r) => r.status === activeFilter.value)
})

const countByStatus = (status) => {
    const list = props.redemptions?.data || []
    if (status === 'all') return list.length
    return list.filter((r) => r.status === status).length
}

const statusBadgeClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-amber-100 text-amber-800 border border-amber-200'
        case 'approved':
            return 'bg-emerald-100 text-emerald-800 border border-emerald-200'
        case 'rejected':
            return 'bg-rose-100 text-rose-800 border border-rose-200'
        default:
            return 'bg-gray-100 text-gray-700'
    }
}

const formatDate = (date) => {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const formatTime = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleTimeString('en-PH', {
        hour: '2-digit',
        minute: '2-digit',
    })
}

const getInitials = (name) => {
    if (!name) return 'R'
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase()
}

const openApproveModal = (redemption) => {
    selectedRedemption.value = redemption
    showApprove.value = true
}

const openRejectModal = (redemption) => {
    selectedRedemption.value = redemption
    showReject.value = true
}

const submitApprove = () => {
    if (!selectedRedemption.value) return
    actionForm.post(route('official.redemptions.approve', selectedRedemption.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showApprove.value = false
            selectedRedemption.value = null
        },
    })
}

const submitReject = () => {
    if (!selectedRedemption.value) return
    actionForm.post(route('official.redemptions.reject', selectedRedemption.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showReject.value = false
            selectedRedemption.value = null
        },
    })
}
</script>
