<template>
    <AuthLayout page-title="Reports">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-rose-950 tracking-tight">Submitted Reports</h2>
        </div>

        <div class="bg-white/70 backdrop-blur-2xl sm:rounded-2xl shadow-xl shadow-rose-900/5 sm:border border-white/60 -mx-4 sm:mx-0 overflow-hidden">
            <div class="overflow-x-auto  scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="border-b border-rose-100/50">
                    <tr>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="User" class="w-4 h-4 opacity-70" /> Resident</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Box" class="w-4 h-4 opacity-70" /> Type</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="FileText" class="w-4 h-4 opacity-70" /> Description</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Activity" class="w-4 h-4 opacity-70" /> Status</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Calendar" class="w-4 h-4 opacity-70" /> Date</div></th>
                        <th class="text-left px-6 py-3 text-rose-950/70 font-semibold"><div class="flex items-center gap-1.5"><component :is="Settings" class="w-4 h-4 opacity-70" /> Actions</div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="report in reports.data" :key="report.id" class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-800">{{ report.resident.name }}</td>
                        <td class="px-6 py-4">
                            <span :class="report.type === 'missed_collection' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ report.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-rose-950/80 max-w-xs truncate">{{ report.description }}</td>
                        <td class="px-6 py-4">
                            <span :class="statusClass(report.status)" class="px-3 py-1 rounded-full text-xs font-bold capitalize">
                                {{ report.status === 'pending' ? 'Pending' : 'Resolved' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            <div>{{ formatDate(report.responded_at ?? report.created_at) }}</div>
                            <div v-if="report.responded_at" class="text-[11px] text-gray-400">Submitted: {{ formatDate(report.created_at) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button @click="viewReport(report)" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex-shrink-0">View</button>
                                <button @click="deleteReport(report)" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex-shrink-0">Delete</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="reports.data.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No reports found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- View/Respond Modal -->
        <Modal :show="showViewModal" title="Report Details" max-width="lg" @close="showViewModal = false">
            <div v-if="selectedReport" class="space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Resident</p>
                        <p class="font-medium text-gray-800">{{ selectedReport.resident.name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Type</p>
                        <p class="font-medium text-gray-800">{{ selectedReport.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Status</p>
                        <span :class="statusClass(selectedReport.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">{{ selectedReport.status }}</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-1">Date Submitted</p>
                        <p class="text-gray-800">{{ formatDate(selectedReport.created_at) }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-gray-500 text-xs mb-1">Description</p>
                    <p class="text-gray-800 text-sm bg-gray-50 p-3 rounded-md">{{ selectedReport.description }}</p>
                </div>

                <div v-if="selectedReport.photos?.length > 0">
                    <p class="text-gray-500 text-xs mb-2">Photos</p>
                    <div class="flex gap-2 flex-wrap">
                        <img v-for="photo in selectedReport.photos" :key="photo.id" @click="viewingPhoto = '/storage/' + photo.photo_path" :src="'/storage/' + photo.photo_path" class="w-24 h-24 object-cover rounded-md border cursor-pointer hover:opacity-80 transition-opacity" />
                    </div>
                </div>

                <div v-if="selectedReport.official_response">
                    <p class="text-gray-500 text-xs mb-1">Official Response</p>
                    <p class="text-gray-800 text-sm bg-green-50 p-3 rounded-md">{{ selectedReport.official_response }}</p>
                    <p v-if="selectedReport.responded_at" class="text-xs text-gray-400 mt-2">Responded on {{ formatDate(selectedReport.responded_at) }}</p>
                </div>

                <div v-if="selectedReport.status === 'pending'" class="border-t pt-4">
                    <p class="text-sm font-bold text-gray-700 mb-2">Respond & Resolve Report</p>
                    <form @submit.prevent="submitResponse" class="space-y-3">
                        <div>
                            <textarea v-model="respondForm.official_response" rows="3" placeholder="Write resolution details or response to resident..." class="w-full bg-white/50 border border-rose-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                            <p v-if="respondForm.errors.official_response" class="text-red-500 text-xs mt-1">{{ respondForm.errors.official_response }}</p>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-lg font-bold">
                                Status: Marked as Resolved
                            </span>
                            <div class="flex gap-2">
                                <button type="button" @click="showViewModal = false" class="text-sm text-rose-900/60 hover:text-rose-900 transition-colors px-3 py-2">Cancel</button>
                                <button type="submit" :disabled="respondForm.processing" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-md transition-all flex items-center gap-1.5">
                                    <span v-if="respondForm.processing">Saving...</span>
                                    <span v-else>Resolve Report</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>

        <!-- Fullscreen Image Modal -->
        <Modal :show="!!viewingPhoto" max-width="3xl" @close="viewingPhoto = null">
            <div class="relative bg-slate-950 rounded-2xl overflow-hidden p-2 flex items-center justify-center min-h-[300px]">
                <button @click="viewingPhoto = null" class="absolute top-4 right-4 text-white bg-black/60 hover:bg-black/90 rounded-full w-8 h-8 flex items-center justify-center transition-colors z-10"><component :is="X" class="w-5 h-5" /></button>
                <img :src="viewingPhoto" class="w-full h-auto max-h-[80vh] object-contain rounded-xl" />
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { X, User, Box, FileText, Activity, Calendar, Settings } from '@lucide/vue';
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    reports: Object,
})

const showViewModal = ref(false)
const selectedReport = ref(null)
const viewingPhoto = ref(null)

const respondForm = useForm({
    official_response: '',
    status: 'resolved',
})

const statusClass = (status) => ({
    pending: 'bg-amber-100 text-amber-800 border border-amber-200',
    resolved: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
    reviewed: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
}[status] ?? 'bg-gray-100 text-gray-700')

const formatDate = (date) => date ? new Date(date).toLocaleString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
}) : '—'

const viewReport = (report) => {
    selectedReport.value = report
    respondForm.reset()
    showViewModal.value = true
}

const submitResponse = () => {
    respondForm.post(route('official.reports.respond', selectedReport.value.id), {
        onSuccess: () => showViewModal.value = false,
    })
}

const deleteReport = (report) => {
    if (confirm(`Are you sure you want to delete this report from ${report.resident?.name || 'resident'}?`)) {
        router.delete(route('official.reports.destroy', report.id))
    }
}
</script>