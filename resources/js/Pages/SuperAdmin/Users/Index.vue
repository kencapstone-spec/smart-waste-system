<template>
    <AuthLayout page-title="User Management">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-rose-950 tracking-tight">System Users</h2>
            <button
                @click="showCreateModal = true"
                class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all transition"
            >
                + Add User
            </button>
        </div>

        <!-- Users Table -->
        <div class="bg-white/70 backdrop-blur-2xl rounded-2xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden">
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Name</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Phone</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Role</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Status</th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-800">{{ user.name }}</td>
                        <td class="px-6 py-4 text-rose-950/80">{{ user.phone }}</td>
                        <td class="px-6 py-4">
                            <span class="capitalize px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ user.role.replace('_', ' ') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="statusClass(user.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ user.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <button
                                @click="editUser(user)"
                                class="text-blue-600 hover:underline text-xs"
                            >Edit</button>
                            <button
                                @click="confirmDelete(user)"
                                class="text-red-500 hover:underline text-xs"
                            >Delete</button>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No users found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" title="Add User" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="createForm.name" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input v-model="createForm.phone" type="tel" placeholder="09XXXXXXXXX" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="createForm.errors.phone" class="text-red-500 text-xs mt-1">{{ createForm.errors.phone }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select v-model="createForm.role" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option value="">Select role</option>
                        <option value="barangay_official">Barangay Official</option>
                        <option value="personnel">Personnel</option>
                    </select>
                    <p v-if="createForm.errors.role" class="text-red-500 text-xs mt-1">{{ createForm.errors.role }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="createForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Save</button>
                </div>
            </form>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" title="Edit User" @close="showEditModal = false">
            <form @submit.prevent="submitEdit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="editForm.name" type="text" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="editForm.errors.name" class="text-red-500 text-xs mt-1">{{ editForm.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input v-model="editForm.phone" type="tel" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all" />
                    <p v-if="editForm.errors.phone" class="text-red-500 text-xs mt-1">{{ editForm.errors.phone }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select v-model="editForm.role" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option value="barangay_official">Barangay Official</option>
                        <option value="personnel">Personnel</option>
                    </select>
                    <p v-if="editForm.errors.role" class="text-red-500 text-xs mt-1">{{ editForm.errors.role }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select v-model="editForm.status" class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors">Cancel</button>
                    <button type="submit" :disabled="editForm.processing" class="bg-rose-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all">Update</button>
                </div>
            </form>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" title="Delete User" @close="showDeleteModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Are you sure you want to delete <span class="font-semibold">{{ selectedUser?.name }}</span>? This action cannot be undone.</p>
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
    users: Object,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref(null)

const createForm = useForm({ name: '', phone: '', role: '' })
const editForm = useForm({ name: '', phone: '', role: '', status: '' })
const deleteForm = useForm({})

const statusClass = (status) => {
    return {
        active: 'bg-green-100 text-green-700',
        pending: 'bg-yellow-100 text-yellow-700',
        rejected: 'bg-red-100 text-red-700',
    }[status] ?? 'bg-gray-100 text-gray-700'
}

const editUser = (user) => {
    selectedUser.value = user
    editForm.name = user.name
    editForm.phone = user.phone
    editForm.role = user.role
    editForm.status = user.status
    showEditModal.value = true
}

const confirmDelete = (user) => {
    selectedUser.value = user
    showDeleteModal.value = true
}

const submitCreate = () => {
    createForm.post(route('super-admin.users.store'), {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.reset()
        },
    })
}

const submitEdit = () => {
    editForm.put(route('super-admin.users.update', selectedUser.value.id), {
        onSuccess: () => {
            showEditModal.value = false
        },
    })
}

const submitDelete = () => {
    deleteForm.delete(route('super-admin.users.destroy', selectedUser.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
        },
    })
}
</script>