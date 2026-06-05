<template>
    <AuthLayout page-title="Rewards Management">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-700">Rewards Catalog</h2>
            <button
                @click="showCreateModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition"
            >
                + Add Reward
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Name</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Points Req.</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Stock</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="reward in rewards.data" :key="reward.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ reward.name }}</td>
                        <td class="px-6 py-4 text-gray-600 font-bold text-yellow-600">{{ reward.points_required }} ⭐</td>
                        <td class="px-6 py-4 text-gray-600">{{ reward.stock }}</td>
                        <td class="px-6 py-4">
                            <span :class="reward.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ reward.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <button @click="editReward(reward)" class="text-blue-600 hover:underline text-xs">Edit</button>
                            <button @click="confirmDelete(reward)" class="text-red-500 hover:underline text-xs">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="rewards.data.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No rewards found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" title="Add Reward" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="createForm.name" type="text" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="createForm.description" rows="2" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Points Required</label>
                        <input v-model="createForm.points_required" type="number" min="1" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Initial Stock</label>
                        <input v-model="createForm.stock" type="number" min="0" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" v-model="createForm.is_active" id="isActiveCreate" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <label for="isActiveCreate" class="text-sm text-gray-700">Active</label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showCreateModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" :disabled="createForm.processing" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">Create</button>
                </div>
            </form>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" title="Edit Reward" @close="showEditModal = false">
            <form @submit.prevent="submitEdit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="editForm.name" type="text" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="editForm.description" rows="2" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Points Required</label>
                        <input v-model="editForm.points_required" type="number" min="1" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                        <input v-model="editForm.stock" type="number" min="0" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500" required />
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" v-model="editForm.is_active" id="isActiveEdit" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <label for="isActiveEdit" class="text-sm text-gray-700">Active</label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showEditModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">Update</button>
                </div>
            </form>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" title="Delete Reward" @close="showDeleteModal = false">
            <p class="text-gray-600 text-sm mb-6">Are you sure you want to delete <span class="font-semibold">{{ selectedReward?.name }}</span>?</p>
            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                <button @click="submitDelete" class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600">Delete</button>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    rewards: Object,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedReward = ref(null)

const createForm = useForm({
    name: '',
    description: '',
    points_required: 100,
    stock: 0,
    is_active: true
})

const editForm = useForm({
    name: '',
    description: '',
    points_required: 0,
    stock: 0,
    is_active: true
})

const deleteForm = useForm({})

const editReward = (reward) => {
    selectedReward.value = reward
    editForm.name = reward.name
    editForm.description = reward.description ?? ''
    editForm.points_required = reward.points_required
    editForm.stock = reward.stock
    editForm.is_active = !!reward.is_active
    showEditModal.value = true
}

const confirmDelete = (reward) => {
    selectedReward.value = reward
    showDeleteModal.value = true
}

const submitCreate = () => {
    createForm.post(route('super-admin.rewards.store'), {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.reset()
        },
    })
}

const submitEdit = () => {
    editForm.put(route('super-admin.rewards.update', selectedReward.value.id), {
        onSuccess: () => showEditModal.value = false,
    })
}

const submitDelete = () => {
    deleteForm.delete(route('super-admin.rewards.destroy', selectedReward.value.id), {
        onSuccess: () => showDeleteModal.value = false,
    })
}
</script>
