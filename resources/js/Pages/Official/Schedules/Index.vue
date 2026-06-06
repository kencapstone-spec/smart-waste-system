<template>
    <AuthLayout page-title="Schedule Management">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-rose-950 tracking-tight">Collection Schedules</h2>
            <button
                @click="showCreateModal = true"
                class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all transition"
            >
                + Add Schedule
            </button>
        </div>

        <div class="bg-white/70 backdrop-blur-2xl rounded-2xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden">
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Title</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Street</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Frequency</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Start Date</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Time</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Status</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="schedule in schedules.data" :key="schedule.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-rose-950 font-semibold">{{ schedule.title }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ schedule.street.name }}</td>
                        <td class="px-6 py-4 capitalize text-rose-950/80">{{ schedule.frequency }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ schedule.start_date }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ schedule.collection_time }}</td>
                        <td class="px-6 py-4">
                            <span :class="schedule.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-rose-950/80'" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                {{ schedule.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <button @click="viewTasks(schedule)" class="text-indigo-600 hover:underline text-xs">Tasks</button>
                            <button @click="editSchedule(schedule)" class="text-blue-600 hover:underline text-xs">Edit</button>
                            <button @click="confirmDelete(schedule)" class="text-red-500 hover:underline text-xs">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="schedules.data.length === 0">
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">No schedules found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" title="Add Schedule" max-width="2xl" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input v-model="createForm.title" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="createForm.errors.title" class="text-red-500 text-xs mt-1">{{ createForm.errors.title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street</label>
                    <select v-model="createForm.street_id" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option value="">Select street</option>
                        <option v-for="street in streets" :key="street.id" :value="street.id">
                            {{ street.name }} ({{ street.zone.name }})
                        </option>
                    </select>
                    <p v-if="createForm.errors.street_id" class="text-red-500 text-xs mt-1">{{ createForm.errors.street_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="createForm.description" rows="2" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                        <select v-model="createForm.frequency" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                            <option value="">Select frequency</option>
                            <option value="once">Once</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        <p v-if="createForm.errors.frequency" class="text-red-500 text-xs mt-1">{{ createForm.errors.frequency }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Collection Time</label>
                        <input v-model="createForm.collection_time" type="time" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                        <p v-if="createForm.errors.collection_time" class="text-red-500 text-xs mt-1">{{ createForm.errors.collection_time }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input v-model="createForm.start_date" type="date" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                        <p v-if="createForm.errors.start_date" class="text-red-500 text-xs mt-1">{{ createForm.errors.start_date }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-gray-400">(optional)</span></label>
                        <input v-model="createForm.end_date" type="date" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Personnel</label>
                    <div class="space-y-2 max-h-36 overflow-y-auto border border-gray-200 rounded-md p-3">
                        <label v-for="person in personnel" :key="person.id" class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" :value="person.id" v-model="createForm.personnel_ids" class="rounded border-gray-300" />
                            {{ person.name }}
                        </label>
                        <p v-if="personnel.length === 0" class="text-gray-400 text-xs">No active personnel available.</p>
                    </div>
                    <p v-if="createForm.errors.personnel_ids" class="text-red-500 text-xs mt-1">{{ createForm.errors.personnel_ids }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="createForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Create</button>
                </div>
            </form>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" title="Edit Schedule" max-width="2xl" @close="showEditModal = false">
            <form @submit.prevent="submitEdit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input v-model="editForm.title" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="editForm.errors.title" class="text-red-500 text-xs mt-1">{{ editForm.errors.title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street</label>
                    <select v-model="editForm.street_id" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option v-for="street in streets" :key="street.id" :value="street.id">
                            {{ street.name }} ({{ street.zone.name }})
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="editForm.description" rows="2" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                        <select v-model="editForm.frequency" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                            <option value="once">Once</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Collection Time</label>
                        <input v-model="editForm.collection_time" type="time" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input v-model="editForm.start_date" type="date" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-gray-400">(optional)</span></label>
                        <input v-model="editForm.end_date" type="date" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select v-model="editForm.status" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Personnel</label>
                    <div class="space-y-2 max-h-36 overflow-y-auto border border-gray-200 rounded-md p-3">
                        <label v-for="person in personnel" :key="person.id" class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" :value="person.id" v-model="editForm.personnel_ids" class="rounded border-gray-300" />
                            {{ person.name }}
                        </label>
                    </div>
                    <p v-if="editForm.errors.personnel_ids" class="text-red-500 text-xs mt-1">{{ editForm.errors.personnel_ids }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Update</button>
                </div>
            </form>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" title="Delete Schedule" @close="showDeleteModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Are you sure you want to delete <span class="font-semibold">{{ selectedSchedule?.title }}</span>?</p>
            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitDelete" class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600">Delete</button>
            </div>
        </Modal>

        <!-- Tasks Reassignment Modal -->
        <Modal :show="showTasksModal" title="Schedule Tasks" max-width="3xl" @close="showTasksModal = false">
            <div v-if="loadingTasks" class="py-8 text-center text-sm text-gray-500">Loading tasks...</div>
            <div v-else>
                <div class="max-h-[60vh] overflow-y-auto">
                    <div class="overflow-x-auto pb-4">
                        <table class="w-full text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 border-b sticky top-0">
                            <tr>
                                <th class="text-left px-4 py-3 text-rose-950/70 font-semibold">Date</th>
                                <th class="text-left px-4 py-3 text-rose-950/70 font-semibold">Status</th>
                                <th class="text-left px-4 py-3 text-rose-950/70 font-semibold">Assigned To</th>
                                <th class="text-left px-4 py-3 text-rose-950/70 font-semibold">Reassign</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="task in currentTasks" :key="task.id" class="hover:bg-rose-50/50 transition-colors">
                                <td class="px-4 py-3 text-gray-800">{{ task.collection_date }}</td>
                                <td class="px-4 py-3">
                                    <span :class="{
                                        'bg-yellow-100 text-yellow-700': task.status === 'pending',
                                        'bg-green-100 text-green-700': task.status === 'completed',
                                        'bg-red-100 text-red-700': task.status === 'missed'
                                    }" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                        {{ task.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-rose-950/80">{{ task.personnel.name }}</td>
                                <td class="px-4 py-3">
                                    <div v-if="task.status === 'pending'" class="flex gap-2 items-center">
                                        <select 
                                            v-model="reassignForms[task.id]"
                                            class="border border-gray-300 rounded px-2 py-1 text-xs focus:ring-green-500 focus:border-green-500"
                                        >
                                            <option v-for="person in personnel" :key="person.id" :value="person.id">
                                                {{ person.name }}
                                            </option>
                                        </select>
                                        <button 
                                            v-if="reassignForms[task.id] !== task.personnel_id"
                                            @click="submitReassign(task)"
                                            class="bg-indigo-600 text-white px-3 py-1 rounded text-xs hover:bg-indigo-700"
                                        >
                                            Save
                                        </button>
                                    </div>
                                    <span v-else class="text-xs text-gray-400">Cannot reassign</span>
                                </td>
                            </tr>
                            <tr v-if="currentTasks.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400">No tasks found.</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t mt-4">
                    <button @click="showTasksModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Close</button>
                </div>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    schedules: Object,
    streets: Array,
    personnel: Array,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const showTasksModal = ref(false)
const selectedSchedule = ref(null)

const loadingTasks = ref(false)
const currentTasks = ref([])
const reassignForms = reactive({})

const createForm = useForm({
    street_id: '',
    title: '',
    description: '',
    frequency: '',
    start_date: '',
    end_date: '',
    collection_time: '',
    personnel_ids: [],
})

const editForm = useForm({
    street_id: '',
    title: '',
    description: '',
    frequency: '',
    start_date: '',
    end_date: '',
    collection_time: '',
    status: '',
    personnel_ids: [],
})

const deleteForm = useForm({})

const editSchedule = (schedule) => {
    selectedSchedule.value = schedule
    editForm.street_id = schedule.street_id
    editForm.title = schedule.title
    editForm.description = schedule.description ?? ''
    editForm.frequency = schedule.frequency
    editForm.start_date = schedule.start_date
    editForm.end_date = schedule.end_date ?? ''
    editForm.collection_time = schedule.collection_time
    editForm.status = schedule.status
    editForm.personnel_ids = schedule.assignments.map(a => a.personnel_id)
    showEditModal.value = true
}

const confirmDelete = (schedule) => {
    selectedSchedule.value = schedule
    showDeleteModal.value = true
}

const submitCreate = () => {
    createForm.post(route('official.schedules.store'), {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.reset()
        },
    })
}

const submitEdit = () => {
    editForm.put(route('official.schedules.update', selectedSchedule.value.id), {
        onSuccess: () => showEditModal.value = false,
    })
}

const submitDelete = () => {
    deleteForm.delete(route('official.schedules.destroy', selectedSchedule.value.id), {
        onSuccess: () => showDeleteModal.value = false,
    })
}

const viewTasks = async (schedule) => {
    selectedSchedule.value = schedule
    showTasksModal.value = true
    loadingTasks.value = true
    
    try {
        const response = await fetch(route('official.schedules.tasks', schedule.id), {
            headers: {
                'Accept': 'application/json'
            }
        })
        const data = await response.json()
        currentTasks.value = data
        
        // Initialize reassign dropdowns
        currentTasks.value.forEach(task => {
            if (task.status === 'pending') {
                reassignForms[task.id] = task.personnel_id
            }
        })
    } catch (error) {
        console.error('Failed to load tasks', error)
    } finally {
        loadingTasks.value = false
    }
}

const submitReassign = (task) => {
    const newPersonnelId = reassignForms[task.id]
    
    router.post(route('official.tasks.reassign', task.id), {
        personnel_id: newPersonnelId
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Update local state without closing modal
            const updatedPerson = props.personnel.find(p => p.id === newPersonnelId)
            task.personnel_id = newPersonnelId
            task.personnel = updatedPerson
        }
    })
}
</script>