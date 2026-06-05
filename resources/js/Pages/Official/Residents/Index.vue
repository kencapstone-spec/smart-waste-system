<template>
    <AuthLayout page-title="Resident Management">
        <div class="mb-6 flex gap-3">
            <button
                @click="filter = 'all'"
                :class="filter === 'all' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border'"
                class="px-4 py-2 rounded-md text-sm font-medium transition"
            >All</button>
            <button
                @click="filter = 'pending'"
                :class="filter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-600 border'"
                class="px-4 py-2 rounded-md text-sm font-medium transition"
            >Pending</button>
            <button
                @click="filter = 'active'"
                :class="filter === 'active' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border'"
                class="px-4 py-2 rounded-md text-sm font-medium transition"
            >Active</button>
            <button
                @click="filter = 'rejected'"
                :class="filter === 'rejected' ? 'bg-red-500 text-white' : 'bg-white text-gray-600 border'"
                class="px-4 py-2 rounded-md text-sm font-medium transition"
            >Rejected</button>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Name</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Phone</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Street</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Registered</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="resident in filteredResidents" :key="resident.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ resident.name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ resident.phone }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ resident.street?.name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span :class="statusClass(resident.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                {{ resident.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ formatDate(resident.created_at) }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <button @click="viewResident(resident)" class="text-blue-600 hover:underline text-xs font-medium">View</button>
                            <button
                                v-if="resident.status === 'pending'"
                                @click="approveResident(resident)"
                                class="text-green-600 hover:underline text-xs font-medium"
                            >Approve</button>
                            <button
                                v-if="resident.status === 'pending'"
                                @click="rejectResident(resident)"
                                class="text-red-500 hover:underline text-xs font-medium"
                            >Reject</button>
                            <button
                                v-if="resident.status === 'active'"
                                @click="deactivateResident(resident)"
                                class="text-orange-500 hover:underline text-xs font-medium"
                            >Deactivate</button>
                            <button
                                @click="deleteResident(resident)"
                                class="text-red-600 hover:underline text-xs font-medium"
                            >Delete</button>
                        </td>
                    </tr>
                    <tr v-if="filteredResidents.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No residents found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

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
                        <p class="text-gray-500 text-xs mb-1">Street</p>
                        <p class="text-gray-800">{{ selectedResident.street?.name ?? '—' }}</p>
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
                        <button @click="approveResident(selectedResident)" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition">Approve</button>
                    </template>
                    <template v-if="selectedResident.status === 'active'">
                        <button @click="deactivateResident(selectedResident)" class="bg-orange-500 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-600 transition">Deactivate</button>
                    </template>
                    <button @click="deleteResident(selectedResident)" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 transition">Delete</button>
                </div>
            </div>
        </Modal>

        <!-- Approve Confirmation Modal -->
        <Modal :show="showApproveModal" title="Approve Resident" @close="showApproveModal = false">
            <p class="text-gray-600 text-sm mb-6">Approve <span class="font-semibold">{{ selectedResident?.name }}</span>? They will receive an SMS notification.</p>
            <div class="flex justify-end gap-3">
                <button @click="showApproveModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                <button @click="submitApprove" :disabled="actionForm.processing" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">Approve</button>
            </div>
        </Modal>

        <!-- Reject Confirmation Modal -->
        <Modal :show="showRejectModal" title="Reject Resident" @close="showRejectModal = false">
            <p class="text-gray-600 text-sm mb-6">Reject <span class="font-semibold">{{ selectedResident?.name }}</span>? They will receive an SMS notification.</p>
            <div class="flex justify-end gap-3">
                <button @click="showRejectModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                <button @click="submitReject" :disabled="actionForm.processing" class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600">Reject</button>
            </div>
        </Modal>

        <!-- Deactivate Confirmation Modal -->
        <Modal :show="showDeactivateModal" title="Deactivate Resident" @close="showDeactivateModal = false">
            <p class="text-gray-600 text-sm mb-6">Deactivate <span class="font-semibold">{{ selectedResident?.name }}</span>? This will change their status to rejected and they will be notified via SMS.</p>
            <div class="flex justify-end gap-3">
                <button @click="showDeactivateModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                <button @click="submitDeactivate" :disabled="actionForm.processing" class="bg-orange-500 text-white px-4 py-2 rounded-md text-sm hover:bg-orange-600">Deactivate</button>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" title="Delete Resident Account" @close="showDeleteModal = false">
            <div class="mb-6">
                <p class="text-gray-800 text-sm font-semibold mb-2">Are you sure you want to permanently delete <span class="font-bold text-red-600">{{ selectedResident?.name }}</span>?</p>
                <p class="text-red-500 text-xs">Warning: This will permanently delete their account and any associated points/records. This action cannot be undone.</p>
            </div>
            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                <button @click="submitDelete" :disabled="actionForm.processing" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">Yes, Delete</button>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    residents: Object,
})

const filter = ref('all')
const showViewModal = ref(false)
const showApproveModal = ref(false)
const showRejectModal = ref(false)
const showDeactivateModal = ref(false)
const showDeleteModal = ref(false)
const selectedResident = ref(null)
const actionForm = useForm({})

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

const submitDelete = () => {
    actionForm.delete(route('official.residents.destroy', selectedResident.value.id), {
        onSuccess: () => showDeleteModal.value = false,
    })
}
</script>