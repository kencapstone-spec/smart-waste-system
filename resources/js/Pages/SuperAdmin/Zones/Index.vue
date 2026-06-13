<template>
    <AuthLayout page-title="Zone Management">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-rose-950 tracking-tight">Zones</h2>
            <button
                @click="showCreateModal = true"
                class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all transition"
            >
                + Add Zone
            </button>
        </div>

        <div class="bg-white/70 backdrop-blur-2xl sm:rounded-2xl shadow-xl shadow-rose-900/5 sm:border border-white/60 -mx-4 sm:mx-0 overflow-hidden">
            <div class="overflow-x-auto  scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="User" class="w-4 h-4 opacity-70" /> Name</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="FileText" class="w-4 h-4 opacity-70" /> Description</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Settings" class="w-4 h-4 opacity-70" /> Actions</div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="zone in zones" :key="zone.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-rose-950 font-semibold">{{ zone.name }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ zone.description ?? '—' }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <button @click="editZone(zone)" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex-shrink-0">Edit</button>
                            <button @click="confirmDelete(zone)" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex-shrink-0">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="zones.length === 0">
                        <td colspan="3" class="px-6 py-8 text-center text-gray-400">No zones found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" title="Add Zone" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="createForm.name" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="createForm.description" rows="3" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="createForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Save</button>
                </div>
            </form>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" title="Edit Zone" @close="showEditModal = false">
            <form @submit.prevent="submitEdit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="editForm.name" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="editForm.errors.name" class="text-red-500 text-xs mt-1">{{ editForm.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="editForm.description" rows="3" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Update</button>
                </div>
            </form>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" title="Delete Zone" @close="showDeleteModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Are you sure you want to delete <span class="font-semibold">{{ selectedZone?.name }}</span>?</p>
            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                <button @click="submitDelete" class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600">Delete</button>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { User, FileText, Settings } from '@lucide/vue'
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