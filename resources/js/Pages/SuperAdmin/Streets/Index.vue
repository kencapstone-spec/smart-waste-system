<template>
    <AuthLayout page-title="Street Management">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-rose-950 tracking-tight">Streets</h2>
            <button
                @click="showCreateModal = true"
                class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all transition"
            >
                + Add Street
            </button>
        </div>

        <div class="bg-white/70 backdrop-blur-2xl rounded-2xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden">
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Street Name</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Zone</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="street in streets" :key="street.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-rose-950 font-semibold">{{ street.name }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ street.zone.name }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <button @click="editStreet(street)" class="text-blue-600 hover:underline text-xs">Edit</button>
                            <button @click="confirmDelete(street)" class="text-red-500 hover:underline text-xs">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="streets.length === 0">
                        <td colspan="3" class="px-6 py-8 text-center text-gray-400">No streets found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" title="Add Street" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                    <select v-model="createForm.zone_id" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option value="">Select zone</option>
                        <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                    </select>
                    <p v-if="createForm.errors.zone_id" class="text-red-500 text-xs mt-1">{{ createForm.errors.zone_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Name</label>
                    <input v-model="createForm.name" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="createForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Save</button>
                </div>
            </form>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" title="Edit Street" @close="showEditModal = false">
            <form @submit.prevent="submitEdit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                    <select v-model="editForm.zone_id" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                    </select>
                    <p v-if="editForm.errors.zone_id" class="text-red-500 text-xs mt-1">{{ editForm.errors.zone_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street Name</label>
                    <input v-model="editForm.name" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="editForm.errors.name" class="text-red-500 text-xs mt-1">{{ editForm.errors.name }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Update</button>
                </div>
            </form>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" title="Delete Street" @close="showDeleteModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Are you sure you want to delete <span class="font-semibold">{{ selectedStreet?.name }}</span>?</p>
            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
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
    streets: Array,
    zones: Array,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedStreet = ref(null)

const createForm = useForm({ zone_id: '', name: '' })
const editForm = useForm({ zone_id: '', name: '' })
const deleteForm = useForm({})

const editStreet = (street) => {
    selectedStreet.value = street
    editForm.zone_id = street.zone_id
    editForm.name = street.name
    showEditModal.value = true
}

const confirmDelete = (street) => {
    selectedStreet.value = street
    showDeleteModal.value = true
}

const submitCreate = () => {
    createForm.post(route('super-admin.streets.store'), {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.reset()
        },
    })
}

const submitEdit = () => {
    editForm.put(route('super-admin.streets.update', selectedStreet.value.id), {
        onSuccess: () => showEditModal.value = false,
    })
}

const submitDelete = () => {
    deleteForm.delete(route('super-admin.streets.destroy', selectedStreet.value.id), {
        onSuccess: () => showDeleteModal.value = false,
    })
}
</script>