<template>
    <AuthLayout page-title="Zone Management">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-700">Zones</h2>
            <button
                @click="showCreateModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition"
            >
                + Add Zone
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Name</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Description</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Streets</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="zone in zones" :key="zone.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ zone.name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ zone.description ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ zone.streets_count }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <button @click="editZone(zone)" class="text-blue-600 hover:underline text-xs">Edit</button>
                            <button @click="confirmDelete(zone)" class="text-red-500 hover:underline text-xs">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="zones.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">No zones found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" title="Add Zone" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="createForm.name" type="text" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="createForm.description" rows="3" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" :disabled="createForm.processing" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">Save</button>
                </div>
            </form>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" title="Edit Zone" @close="showEditModal = false">
            <form @submit.prevent="submitEdit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="editForm.name" type="text" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                    <p v-if="editForm.errors.name" class="text-red-500 text-xs mt-1">{{ editForm.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="editForm.description" rows="3" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">Update</button>
                </div>
            </form>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" title="Delete Zone" @close="showDeleteModal = false">
            <p class="text-gray-600 text-sm mb-6">Are you sure you want to delete <span class="font-semibold">{{ selectedZone?.name }}</span>? All streets in this zone will also be deleted.</p>
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
    zones: Array,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedZone = ref(null)

const createForm = useForm({ name: '', description: '' })
const editForm = useForm({ name: '', description: '' })
const deleteForm = useForm({})

const editZone = (zone) => {
    selectedZone.value = zone
    editForm.name = zone.name
    editForm.description = zone.description ?? ''
    showEditModal.value = true
}

const confirmDelete = (zone) => {
    selectedZone.value = zone
    showDeleteModal.value = true
}

const submitCreate = () => {
    createForm.post(route('super-admin.zones.store'), {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.reset()
        },
    })
}

const submitEdit = () => {
    editForm.put(route('super-admin.zones.update', selectedZone.value.id), {
        onSuccess: () => showEditModal.value = false,
    })
}

const submitDelete = () => {
    deleteForm.delete(route('super-admin.zones.destroy', selectedZone.value.id), {
        onSuccess: () => showDeleteModal.value = false,
    })
}
</script>