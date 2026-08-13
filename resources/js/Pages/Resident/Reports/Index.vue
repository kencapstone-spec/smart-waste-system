<template>
    <AuthLayout page-title="My Reports">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">My Reports</h2>
                <p class="text-sm text-gray-500 mt-1">Track your submitted issues and concerns</p>
            </div>
            <button
                @click="showCreateModal = true"
                class="bg-rose-900 text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-rose-800 transition-all shadow-lg shadow-rose-900/20 flex items-center justify-center gap-2 group"
            >
                <span class="text-lg group-hover:rotate-90 transition-transform"><component :is="Plus" class="w-5 h-5" /></span>
                Submit Report
            </button>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white/70 backdrop-blur-2xl rounded-3xl shadow-xl shadow-rose-900/5 border border-white/60 overflow-hidden mb-8">
            <div class="overflow-x-auto  scrollbar-thin scrollbar-thumb-rose-200 scrollbar-track-transparent pb-4">
                <table class="w-full text-sm whitespace-nowrap">
                <thead class="bg-white/40 border-b border-white/50 backdrop-blur-sm">
                    <tr>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs"><div class="flex items-center gap-1.5"><component :is="Box" class="w-4 h-4 opacity-70" /> Type</div></th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs"><div class="flex items-center gap-1.5"><component :is="FileText" class="w-4 h-4 opacity-70" /> Description</div></th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs"><div class="flex items-center gap-1.5"><component :is="Activity" class="w-4 h-4 opacity-70" /> Status</div></th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs"><div class="flex items-center gap-1.5"><component :is="Calendar" class="w-4 h-4 opacity-70" /> Date</div></th>
                        <th class="text-right px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs"><div class="flex items-center gap-1.5"><component :is="Settings" class="w-4 h-4 opacity-70" /> Actions</div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="report in reports.data" :key="report.id" class="hover:bg-rose-50/30 transition-colors">
                        <td class="px-8 py-5">
                            <span :class="typeClass(report.type)" class="px-3 py-1.5 rounded-lg text-xs font-bold">
                                {{ report.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-rose-950/70 font-semibold max-w-xs truncate">{{ report.description }}</td>
                        <td class="px-8 py-5">
                            <span :class="statusClass(report.status)" class="px-3 py-1.5 rounded-lg text-xs font-bold capitalize">{{ report.status }}</span>
                        </td>
                        <td class="px-8 py-5 text-gray-500 font-medium">{{ formatDate(report.created_at) }}</td>
                        <td class="px-8 py-5 text-right">
                            <button @click="viewReport(report)" class="text-rose-600 hover:text-rose-800 font-bold text-sm bg-rose-50 hover:bg-rose-100 px-4 py-2 rounded-lg transition-colors">View Details</button>
                        </td>
                    </tr>
                    <tr v-if="reports.data.length === 0">
                        <td colspan="5" class="px-8 py-16 text-center text-gray-400">
                            <div class="flex justify-center mb-4 opacity-50"><component :is="FileText" class="w-8 h-8" /></div>
                            <p class="font-medium text-base">No reports submitted yet.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Mobile Stacked Cards -->
        <div class="md:hidden space-y-4 mb-8">
            <div v-for="report in reports.data" :key="report.id" class="bg-white/70 backdrop-blur-2xl rounded-2xl p-5 border border-white/60 shadow-md shadow-rose-900/5 flex flex-col gap-4">
                <div class="flex justify-between items-start">
                    <span :class="typeClass(report.type)" class="px-3 py-1 rounded-lg text-[10px] font-bold">
                        {{ report.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                    </span>
                    <span :class="statusClass(report.status)" class="px-3 py-1 rounded-lg text-[10px] font-bold capitalize">
                        {{ report.status }}
                    </span>
                </div>
                
                <div>
                    <p class="text-gray-900 font-medium text-sm line-clamp-2 mb-2">{{ report.description }}</p>
                    <p class="text-xs text-gray-400 font-medium">{{ formatDate(report.created_at) }}</p>
                </div>
                
                <div class="pt-3 border-t border-gray-50 flex justify-end">
                    <button @click="viewReport(report)" class="text-rose-600 font-bold text-xs bg-rose-50 px-4 py-2 rounded-lg">View Details</button>
                </div>
            </div>
            
            <div v-if="reports.data.length === 0" class="py-12 text-center text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                <div class="flex justify-center mb-3 opacity-50"><component :is="FileText" class="w-10 h-10" /></div>
                <p class="font-medium text-sm">No reports submitted yet.</p>
            </div>
        </div>

        <!-- Submit Report Modal -->
        <Modal :show="showCreateModal" title="Submit a Report" max-width="lg" @close="showCreateModal = false">
            <form @submit.prevent="submitReport" class="space-y-5 p-2">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Report Type</label>
                    <select v-model="reportForm.type" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-rose-900/10 focus:border-rose-900 transition-all font-medium appearance-none">
                        <option value="" disabled>Select the type of issue</option>
                        <option value="missed_collection">Missed Garbage Collection</option>
                        <option value="illegal_dumping">Illegal Dumping</option>
                    </select>
                    <p v-if="reportForm.errors.type" class="text-red-500 text-xs mt-2 font-medium">{{ reportForm.errors.type }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                    <textarea v-model="reportForm.description" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-rose-900/10 focus:border-rose-900 transition-all font-medium resize-none" placeholder="Describe what happened or what you saw in detail..."></textarea>
                    <p v-if="reportForm.errors.description" class="text-red-500 text-xs mt-2 font-medium">{{ reportForm.errors.description }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Photo Evidence (optional)</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 bg-gray-50 hover:bg-gray-100 transition-colors relative cursor-pointer group">
                        <input type="file" accept="image/*" multiple @change="handlePhotos" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                        <div class="text-center">
                            <span class="text-2xl mb-2 block opacity-50 group-hover:scale-110 transition-transform"><component :is="Camera" class="w-5 h-5" /></span>
                            <span class="text-sm font-medium text-rose-950/80">Tap to attach photos</span>
                            <p v-if="reportForm.photos.length" class="text-xs text-rose-600 font-bold mt-2">{{ reportForm.photos.length }} file(s) selected</p>
                        </div>
                    </div>
                </div>
                

                
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" @click="showCreateModal = false" class="w-full sm:w-auto px-6 py-3 text-sm text-rose-950/80 font-bold hover:bg-rose-50/50 transition-colors rounded-xl transition-all">Cancel</button>
                    <button type="submit" :disabled="reportForm.processing" class="w-full sm:w-auto bg-rose-900 text-white px-8 py-3 rounded-xl text-sm font-bold hover:bg-rose-800 transition-all shadow-lg shadow-rose-900/20 disabled:opacity-60 flex justify-center items-center">
                        Submit Report
                    </button>
                </div>
            </form>
        </Modal>

        <!-- View Report Modal -->
        <Modal :show="showViewModal" title="Report Details" max-width="lg" @close="showViewModal = false">
            <div v-if="selectedReport" class="p-2">
                <div class="flex justify-between items-start mb-6 pb-6 border-b border-gray-100">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Status</p>
                        <span :class="statusClass(selectedReport.status)" class="px-3 py-1.5 rounded-lg text-xs font-bold capitalize">{{ selectedReport.status }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Date Submitted</p>
                        <p class="text-gray-900 font-bold text-sm">{{ formatDate(selectedReport.created_at) }}</p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Report Type</p>
                    <span :class="typeClass(selectedReport.type)" class="px-3 py-1.5 rounded-lg text-xs font-bold inline-block mb-4">
                        {{ selectedReport.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                    </span>
                    
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-2">Description</p>
                    <p class="text-gray-800 bg-gray-50 p-4 rounded-2xl text-sm font-medium leading-relaxed border border-gray-100">{{ selectedReport.description }}</p>
                </div>
                
                <div v-if="selectedReport.photos?.length > 0" class="mb-6">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Photo Evidence</p>
                    <div class="grid grid-cols-3 gap-2">
                        <img v-for="photo in selectedReport.photos" :key="photo.id" @click="viewingPhoto = '/storage/' + photo.photo_path" :src="'/storage/' + photo.photo_path" class="w-full h-24 object-cover rounded-xl border border-gray-200 shadow-sm cursor-pointer hover:opacity-80 transition-opacity" />
                    </div>
                </div>
                
                <div v-if="selectedReport.official_response" class="bg-emerald-50/50 p-5 rounded-2xl border border-emerald-100 mt-6">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-2 flex items-center gap-1"><span><component :is="MessageCircle" class="w-5 h-5" /></span> Official Response</p>
                    <p class="text-emerald-900 font-medium text-sm leading-relaxed">{{ selectedReport.official_response }}</p>
                </div>
                
                <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end">
                    <button @click="showViewModal = false" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm rounded-xl transition-colors">Close</button>
                </div>
            </div>
        </Modal>

        <!-- Fullscreen Image Modal -->
        <Modal :show="!!viewingPhoto" max-width="4xl" @close="viewingPhoto = null">
            <div class="relative bg-black rounded-xl overflow-hidden p-1">
                <button @click="viewingPhoto = null" class="absolute top-4 right-4 text-white bg-black/50 hover:bg-black/80 rounded-full w-8 h-8 flex items-center justify-center transition-colors"><component :is="X" class="w-5 h-5" /></button>
                <img :src="viewingPhoto" class="w-full h-auto max-h-[85vh] object-contain rounded-lg" />
            </div>
        </Modal>
    </AuthLayout>
</template>

<script setup>
import { Plus, FileText, Camera, MessageCircle, X, Box, Activity, Calendar, Settings } from '@lucide/vue';
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    reports: Object,
})

const showCreateModal = ref(false)
const showViewModal = ref(false)
const selectedReport = ref(null)
const viewingPhoto = ref(null)

const reportForm = useForm({
    type: '',
    description: '',
    photos: [],
})

const typeClass = (type) => ({
    missed_collection: 'bg-amber-100 text-amber-700',
    illegal_dumping: 'bg-rose-100 text-rose-700',
}[type] ?? 'bg-gray-100 text-gray-700')

const statusClass = (status) => ({
    pending: 'bg-amber-100 text-amber-700',
    reviewed: 'bg-blue-100 text-blue-700',
    resolved: 'bg-emerald-100 text-emerald-700',
}[status] ?? 'bg-gray-100 text-gray-700')

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})



const handlePhotos = (e) => {
    reportForm.photos = Array.from(e.target.files)
}

const viewReport = (report) => {
    selectedReport.value = report
    showViewModal.value = true
}

const submitReport = () => {
    reportForm.post(route('resident.reports.store'), {
        forceFormData: true,
        onSuccess: () => {
            showCreateModal.value = false
            reportForm.reset()
        },
    })
}
</script>
