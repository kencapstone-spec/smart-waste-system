<template>
    <AuthLayout page-title="My Reports">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-700">My Submitted Reports</h2>
            <button
                @click="showCreateModal = true"
                class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition"
            >+ Submit Report</button>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Type</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Description</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Date</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="report in reports.data" :key="report.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span :class="typeClass(report.type)" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ report.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ report.description }}</td>
                        <td class="px-6 py-4">
                            <span :class="statusClass(report.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">{{ report.status }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ formatDate(report.created_at) }}</td>
                        <td class="px-6 py-4">
                            <button @click="viewReport(report)" class="text-blue-600 hover:underline text-xs">View</button>
                        </td>
                    </tr>
                    <tr v-if="reports.data.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No reports submitted yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Submit Report Modal -->
        <Modal :show="showCreateModal" title="Submit a Report" max-width="lg" @close="showCreateModal = false">
            <form @submit.prevent="submitReport" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                    <select v-model="reportForm.type" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Select type</option>
                        <option value="missed_collection">Missed Garbage Collection</option>
                        <option value="illegal_dumping">Illegal Dumping</option>
                    </select>
                    <p v-if="reportForm.errors.type" class="text-red-500 text-xs mt-1">{{ reportForm.errors.type }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea v-model="reportForm.description" rows="4" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Describe the issue in detail..."></textarea>
                    <p v-if="reportForm.errors.description" class="text-red-500 text-xs mt-1">{{ reportForm.errors.description }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo (optional)</label>
                    <input type="file" accept="image/*" multiple @change="handlePhotos" class="w-full text-sm text-gray-600" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <div class="flex gap-2">
                        <button type="button" @click="getLocation" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200 transition">
                            📍 Use My Location
                        </button>
                        <span v-if="reportForm.latitude" class="text-xs text-gray-500 self-center">
                            {{ reportForm.latitude }}, {{ reportForm.longitude }}
                        </span>
                        <span v-else class="text-xs text-gray-400 self-center">No location set</span>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showCreateModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" :disabled="reportForm.processing" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 disabled:opacity-60">Submit</button>
                </div>
            </form>
        </Modal>

        <!-- View Report Modal -->
        <Modal :show="showViewModal" title="Report Details" max-width="lg" @close="showViewModal = false">
            <div v-if="selectedReport" class="space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Type</p>
                        <span :class="typeClass(selectedReport.type)" class="px-2 py-1 rounded-full text-xs font-medium">
                            {{ selectedReport.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Status</p>
                        <span :class="statusClass(selectedReport.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">{{ selectedReport.status }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Date Submitted</p>
                        <p class="text-gray-700">{{ formatDate(selectedReport.created_at) }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Description</p>
                    <p class="text-gray-700 bg-gray-50 p-3 rounded-md">{{ selectedReport.description }}</p>
                </div>
                <div v-if="selectedReport.photos?.length > 0">
                    <p class="text-xs text-gray-400 mb-2">Photos</p>
                    <div class="flex gap-2 flex-wrap">
                        <img v-for="photo in selectedReport.photos" :key="photo.id" :src="'/storage/' + photo.photo_path" class="w-24 h-24 object-cover rounded-md border" />
                    </div>
                </div>
                <div v-if="selectedReport.official_response" class="bg-green-50 p-3 rounded-md">
                    <p class="text-xs text-gray-400 mb-1">Official Response</p>
                    <p class="text-gray-700">{{ selectedReport.official_response }}</p>
                </div>
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
    reports: Object,
})

const showCreateModal = ref(false)
const showViewModal = ref(false)
const selectedReport = ref(null)

const reportForm = useForm({
    type: '',
    description: '',
    latitude: null,
    longitude: null,
    photos: [],
})

const typeClass = (type) => ({
    missed_collection: 'bg-orange-100 text-orange-700',
    illegal_dumping: 'bg-red-100 text-red-700',
}[type] ?? 'bg-gray-100 text-gray-700')

const statusClass = (status) => ({
    pending: 'bg-yellow-100 text-yellow-700',
    reviewed: 'bg-blue-100 text-blue-700',
    resolved: 'bg-green-100 text-green-700',
}[status] ?? 'bg-gray-100 text-gray-700')

const formatDate = (date) => new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
})

const getLocation = () => {
    if (!navigator.geolocation) return alert('Geolocation is not supported by your browser.')
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            reportForm.latitude = pos.coords.latitude
            reportForm.longitude = pos.coords.longitude
        },
        () => alert('Unable to get your location.')
    )
}

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
