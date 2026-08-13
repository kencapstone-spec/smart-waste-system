<template>
    <AuthLayout page-title="Announcements">
        <div class="flex justify-end md:justify-between items-center mb-6 mt-2 md:mt-0">
            <h2 class="hidden md:block text-xl font-bold text-rose-950 tracking-tight">Announcements</h2>
            <button
                @click="showCreateModal = true"
                class="bg-rose-900 text-white px-4 md:px-5 py-2 md:py-2.5 rounded-xl text-xs md:text-sm font-semibold hover:bg-rose-800 shadow-md transition-all w-full md:w-auto"
            >
                + Post Announcement
            </button>
        </div>

        <!-- Empty state -->
        <div v-if="announcements.data.length === 0" class="bg-white/70 backdrop-blur-2xl sm:rounded-2xl shadow-xl shadow-rose-900/5 sm:border border-white/60 p-12 text-center">
            <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <component :is="Megaphone" class="w-8 h-8 text-rose-400" />
            </div>
            <h3 class="text-lg font-bold text-rose-950 mb-1">No announcements yet</h3>
            <p class="text-rose-900/50 text-sm">Post an announcement to notify residents.</p>
        </div>

        <!-- Announcements List -->
        <div v-else class="space-y-4">
            <div
                v-for="announcement in announcements.data"
                :key="announcement.id"
                class="bg-white/70 backdrop-blur-2xl rounded-2xl shadow-sm shadow-rose-900/5 border border-white/60 p-6 transition-all hover:shadow-md hover:shadow-rose-900/10"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center shrink-0 mt-0.5">
                            <component :is="Megaphone" class="w-5 h-5 text-rose-700" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-rose-950 text-base leading-snug mb-1">{{ announcement.title }}</h3>
                            <p class="text-rose-900/70 text-sm leading-relaxed whitespace-pre-line">{{ announcement.content }}</p>
                            <div class="flex items-center gap-3 mt-3">
                                <span class="text-xs text-rose-900/40 font-medium">
                                    Posted by {{ announcement.creator?.name ?? 'Official' }}
                                </span>
                                <span class="w-1 h-1 rounded-full bg-rose-200"></span>
                                <span class="text-xs text-rose-900/40 font-medium">
                                    {{ new Date(announcement.created_at).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' }) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <button
                        @click="confirmDelete(announcement)"
                        class="shrink-0 text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors"
                        title="Delete"
                    >
                        <component :is="Trash2" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="announcements.last_page > 1" class="flex justify-center gap-2 mt-6">
            <a
                v-for="link in announcements.links" :key="link.label"
                :href="link.url ?? '#'"
                v-html="link.label"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                :class="link.active ? 'bg-rose-900 text-white' : 'bg-white text-rose-900 border border-rose-100 hover:bg-rose-50'"
            />
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" title="Post Announcement" @close="showCreateModal = false">
            <form @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-rose-950 mb-1">Title</label>
                    <input
                        v-model="createForm.title"
                        type="text"
                        placeholder="e.g. Schedule Change Next Week"
                        class="w-full border border-rose-100 bg-rose-50/50 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none transition-all"
                        required
                    />
                    <p v-if="createForm.errors.title" class="text-red-500 text-xs mt-1">{{ createForm.errors.title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-rose-950 mb-1">Message</label>
                    <textarea
                        v-model="createForm.content"
                        rows="4"
                        placeholder="Write your announcement message here..."
                        class="w-full border border-rose-100 bg-rose-50/50 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-400 focus:border-rose-400 focus:outline-none transition-all resize-none"
                        required
                    ></textarea>
                    <p v-if="createForm.errors.content" class="text-red-500 text-xs mt-1">{{ createForm.errors.content }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors px-4 py-2">Cancel</button>
                    <button
                        type="submit"
                        :disabled="createForm.processing"
                        class="bg-rose-900 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-800 shadow-md transition-all disabled:opacity-70"
                    >
                        Post
                    </button>
                </div>
            </form>
        </Modal>

        <!-- Delete Confirm Modal -->
        <Modal :show="showDeleteModal" title="Delete Announcement" @close="showDeleteModal = false">
            <p class="text-rose-950/80 text-sm mb-6">Are you sure you want to delete <span class="font-semibold">"{{ selectedAnnouncement?.title }}"</span>?</p>
            <div class="flex justify-end gap-3">
                <button @click="showDeleteModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors px-4 py-2">Cancel</button>
                <button @click="submitDelete" class="bg-red-500 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-red-600 transition-all">Delete</button>
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { Megaphone, Trash2 } from '@lucide/vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    announcements: Object,
})

const showCreateModal = ref(false)
const showDeleteModal = ref(false)
const selectedAnnouncement = ref(null)

const createForm = useForm({
    title: '',
    content: '',
})

const deleteForm = useForm({})

const confirmDelete = (announcement) => {
    selectedAnnouncement.value = announcement
    showDeleteModal.value = true
}

const submitCreate = () => {
    createForm.post(route('official.announcements.store'), {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.reset()
        },
    })
}

const submitDelete = () => {
    deleteForm.delete(route('official.announcements.destroy', selectedAnnouncement.value.id), {
        onSuccess: () => showDeleteModal.value = false,
    })
}
</script>
