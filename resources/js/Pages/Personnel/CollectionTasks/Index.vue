<template>
    <AuthLayout page-title="Collection Tasks">
        <div class="bg-white/70 backdrop-blur-2xl sm:rounded-2xl shadow-xl shadow-rose-900/5 sm:border border-white/60 -mx-4 sm:mx-0 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-base font-semibold text-gray-700">My Collection Tasks</h2>
            </div>

            <div class="overflow-x-auto  scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Calendar" class="w-4 h-4 opacity-70" /> Date</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Schedule</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Purok</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Activity" class="w-4 h-4 opacity-70" /> Status</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Remarks</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Settings" class="w-4 h-4 opacity-70" /> Actions</div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="task in tasks.data" :key="task.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-800">{{ formatDate(task.collection_date) }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ task.schedule?.title ?? '—' }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ task.schedule?.zone?.name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span :class="statusClass(task.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                {{ task.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ task.remarks ?? '—' }}</td>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <button @click="openUpdate(task)" class="text-blue-600 hover:underline text-xs font-medium">Update</button>
                            <button @click="openAwardPoints(task)" class="text-green-600 hover:underline text-xs font-medium">Award Points</button>
                        </td>
                    </tr>
                    <tr v-if="tasks.data.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No collection tasks found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Update Status Modal -->
        <Modal :show="showUpdateModal" title="Update Collection Status" max-width="lg" @close="showUpdateModal = false">
            <form @submit.prevent="submitUpdate" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select v-model="updateForm.status" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="missed">Missed</option>
                    </select>
                    <p v-if="updateForm.errors.status" class="text-red-500 text-xs mt-1">{{ updateForm.errors.status }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (optional)</label>
                    <textarea v-model="updateForm.remarks" rows="3" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" placeholder="Add notes about this collection..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo Proof (optional)</label>
                    <input type="file" accept="image/*" multiple @change="handlePhotos" class="w-full text-sm text-rose-950/80" />
                    <p v-if="updateForm.errors.photos" class="text-red-500 text-xs mt-1">{{ updateForm.errors.photos }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showUpdateModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="updateForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all disabled:opacity-60">Save</button>
                </div>
            </form>
        </Modal>

        <!-- Award Points Modal -->
        <Modal :show="showPointsModal" title="Award Points to Residents" max-width="2xl" @close="showPointsModal = false">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">Residents on {{ selectedTask?.schedule?.street?.name ?? 'this purok' }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">Award points to residents for proper waste disposal</p>
            </div>

            <div class="p-6">
                <div v-if="loadingResidents" class="text-center py-8 text-gray-500 text-sm">
                    Loading residents...
                </div>
                <div v-else-if="currentResidents.length > 0">
                    <div class="overflow-x-auto  scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent pb-4">
                        <table class="w-full text-sm whitespace-nowrap">
                        <thead class="border-b border-rose-100/50">
                            <tr>
                                <th class="text-left px-4 py-2 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="User" class="w-4 h-4 opacity-70" /> Resident</div></th>
                                <th class="text-left px-4 py-2 text-rose-950/70 font-semibold">Points This Task</th>
                                <th class="text-left px-4 py-2 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="FileText" class="w-4 h-4 opacity-70" /> Action</div></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="resident in currentResidents" :key="resident.id" class="hover:bg-rose-50/50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ resident.name }}
                                    <div class="text-xs text-gray-500 font-normal">{{ resident.phone }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="resident.task_points > 0" class="text-green-700 font-semibold">+{{ resident.task_points }}</span>
                                    <span v-else class="text-gray-400">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <input v-model="pointsForms[resident.id].points" type="number" min="1" max="100" class="w-16 border border-gray-300 rounded px-2 py-1 text-xs" />
                                        <input v-model="pointsForms[resident.id].remarks" type="text" placeholder="Remarks" class="w-24 border border-gray-300 rounded px-2 py-1 text-xs" />
                                        <button @click="submitPoints(resident.id)" :disabled="pointsForms[resident.id].processing" class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700 disabled:opacity-50">Award</button>
                                    </div>
                                    <p v-if="pointsForms[resident.id].error" class="text-red-500 text-[10px] mt-1">{{ pointsForms[resident.id].error }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div v-else class="py-12 text-center text-gray-400 text-sm border rounded-lg bg-gray-50">
                    No active residents found on this purok.
                </div>
            </div>
            
            <div class="flex justify-end gap-3 p-4 border-t bg-gray-50">
                <button type="button" @click="showPointsModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors font-medium">Close</button>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { Calendar, Activity, Settings, User, FileText } from '@lucide/vue'
import { ref, reactive } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    tasks: Object,
})

const showUpdateModal = ref(false)
const showPointsModal = ref(false)
const selectedTask = ref(null)

const loadingResidents = ref(false)
const currentResidents = ref([])
const pointsForms = reactive({})

const updateForm = useForm({
    status: 'pending',
    remarks: '',
    photos: [],
})

const statusClass = (status) => ({
    completed: 'bg-green-100 text-green-700',
    missed: 'bg-red-100 text-red-700',
    pending: 'bg-yellow-100 text-yellow-700',
}[status] ?? 'bg-gray-100 text-gray-700')

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})

const openUpdate = (task) => {
    selectedTask.value = task
    updateForm.status = task.status
    updateForm.remarks = task.remarks ?? ''
    showUpdateModal.value = true
}

const handlePhotos = (e) => {
    updateForm.photos = Array.from(e.target.files)
}

const submitUpdate = () => {
    updateForm.post(route('personnel.tasks.update-status', selectedTask.value.id), {
        forceFormData: true,
        onSuccess: () => showUpdateModal.value = false,
    })
}

const openAwardPoints = async (task) => {
    selectedTask.value = task
    showPointsModal.value = true
    loadingResidents.value = true
    
    try {
        const response = await fetch(route('personnel.tasks.residents', task.id), {
            headers: { 'Accept': 'application/json' },
            credentials: 'include'
        })
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        currentResidents.value = data
        
        data.forEach(resident => {
            pointsForms[resident.id] = {
                points: 10,
                remarks: '',
                processing: false,
                error: null
            }
        })
    } catch (e) {
        console.error('Failed to load residents', e)
    } finally {
        loadingResidents.value = false
    }
}

const submitPoints = (residentId) => {
    const form = pointsForms[residentId]
    form.processing = true
    form.error = null

    router.post(route('personnel.tasks.award-points', selectedTask.value.id), {
        resident_id: residentId,
        points: form.points,
        remarks: form.remarks
    }, {
        preserveScroll: true,
        onSuccess: () => {
            form.processing = false
            // Update local points logic
            const resident = currentResidents.value.find(r => r.id === residentId)
            if (resident) {
                resident.task_points = (resident.task_points || 0) + parseInt(form.points)
            }
            form.points = 10
            form.remarks = ''
        },
        onError: (errors) => {
            form.processing = false
            form.error = Object.values(errors)[0]
        }
    })
}
</script>
