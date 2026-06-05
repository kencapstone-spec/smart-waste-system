<template>
    <AuthLayout page-title="Collection Task Detail">
        <div class="mb-6">
            <Link :href="route('personnel.tasks.index')" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Tasks
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Task Info + Update Form -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Task Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Schedule</dt>
                            <dd class="font-medium text-gray-800">{{ task.schedule?.title }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Street</dt>
                            <dd class="text-gray-600">{{ task.schedule?.street?.name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Zone</dt>
                            <dd class="text-gray-600">{{ task.schedule?.street?.zone?.name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Collection Date</dt>
                            <dd class="text-gray-600">{{ formatDate(task.collection_date) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Status</dt>
                            <dd>
                                <span :class="statusClass(task.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                    {{ task.status }}
                                </span>
                            </dd>
                        </div>
                        <div v-if="task.remarks">
                            <dt class="text-xs text-gray-400 mb-0.5">Remarks</dt>
                            <dd class="text-gray-600">{{ task.remarks }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Update Status Form -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Update Status</h3>
                    <form @submit.prevent="submitUpdate" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select v-model="updateForm.status" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="missed">Missed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                            <textarea v-model="updateForm.remarks" rows="3" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Add notes..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Photo Proof</label>
                            <input type="file" accept="image/*" multiple @change="handlePhotos" class="w-full text-sm text-gray-600" />
                        </div>
                        <button type="submit" :disabled="updateForm.processing" class="w-full bg-green-600 text-white py-2 rounded-md text-sm hover:bg-green-700 disabled:opacity-60">
                            Save Update
                        </button>
                    </form>
                </div>

                <!-- Photos -->
                <div v-if="task.photos?.length > 0" class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Photo Proof</h3>
                    <div class="flex gap-2 flex-wrap">
                        <img v-for="photo in task.photos" :key="photo.id" :src="'/storage/' + photo.photo_path" class="w-24 h-24 object-cover rounded-md border" />
                    </div>
                </div>
            </div>

            <!-- Residents & Award Points -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold text-gray-700">Residents on This Street</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Award points to residents for proper waste disposal</p>
                    </div>

                    <div v-if="residents.length > 0">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="text-left px-6 py-3 text-gray-600 font-medium">Resident</th>
                                    <th class="text-left px-6 py-3 text-gray-600 font-medium">Phone</th>
                                    <th class="text-left px-6 py-3 text-gray-600 font-medium">Points This Task</th>
                                    <th class="text-left px-6 py-3 text-gray-600 font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="resident in residents" :key="resident.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ resident.name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ resident.phone }}</td>
                                    <td class="px-6 py-4">
                                        <span v-if="resident.task_points > 0" class="text-green-700 font-semibold">+{{ resident.task_points }}</span>
                                        <span v-else class="text-gray-400">—</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button @click="openAwardPoints(resident)" class="text-blue-600 hover:underline text-xs">Award Points</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="px-6 py-12 text-center text-gray-400 text-sm">
                        No active residents found on this street.
                    </div>
                </div>
            </div>
        </div>

        <!-- Award Points Modal -->
        <Modal :show="showPointsModal" title="Award Points" @close="showPointsModal = false">
            <form @submit.prevent="submitPoints" class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 mb-4">Awarding points to <span class="font-semibold">{{ selectedResident?.name }}</span></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                    <input v-model="pointsForm.points" type="number" min="1" max="100" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    <p v-if="pointsForm.errors.points" class="text-red-500 text-xs mt-1">{{ pointsForm.errors.points }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (optional)</label>
                    <input v-model="pointsForm.remarks" type="text" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Proper segregation, on time, etc." />
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showPointsModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" :disabled="pointsForm.processing" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 disabled:opacity-60">Award</button>
                </div>
            </form>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    task: Object,
    residents: Array,
})

const showPointsModal = ref(false)
const selectedResident = ref(null)

const updateForm = useForm({
    status: props.task.status,
    remarks: props.task.remarks ?? '',
    photos: [],
})

const pointsForm = useForm({
    resident_id: '',
    points: 10,
    remarks: '',
})

const statusClass = (status) => ({
    completed: 'bg-green-100 text-green-700',
    missed: 'bg-red-100 text-red-700',
    pending: 'bg-yellow-100 text-yellow-700',
}[status] ?? 'bg-gray-100 text-gray-700')

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})

const handlePhotos = (e) => {
    updateForm.photos = Array.from(e.target.files)
}

const submitUpdate = () => {
    updateForm.post(route('personnel.tasks.update-status', props.task.id), {
        forceFormData: true,
    })
}

const openAwardPoints = (resident) => {
    selectedResident.value = resident
    pointsForm.resident_id = resident.id
    pointsForm.points = 10
    pointsForm.remarks = ''
    showPointsModal.value = true
}

const submitPoints = () => {
    pointsForm.post(route('personnel.tasks.award-points', props.task.id), {
        onSuccess: () => showPointsModal.value = false,
    })
}
</script>
