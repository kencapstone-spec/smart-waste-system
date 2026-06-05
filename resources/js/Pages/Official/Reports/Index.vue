<template>
    <AuthLayout page-title="Reports">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-700">Submitted Reports</h2>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Resident</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Type</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Description</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Status</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Date</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="report in reports.data" :key="report.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-800">{{ report.resident.name }}</td>
                        <td class="px-6 py-4">
                            <span :class="report.type === 'missed_collection' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ report.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ report.description }}</td>
                        <td class="px-6 py-4">
                            <span :class="statusClass(report.status)" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                {{ report.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ formatDate(report.created_at) }}</td>
                        <td class="px-6 py-4">
                            <button @click="viewReport(report)" class="text-blue-600 hover:underline text-xs">View</button>
                        </td>
                    </tr>
                    <tr v-if="reports.data.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No reports found.</td>
                    </tr>
                </tbody>
            </table>
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
                        <img v-for="photo in selectedReport.photos" :key="photo.id" :src="'/storage/' + photo.photo_path" class="w-24 h-24 object-cover rounded-md border" />
                    </div>
                </div>

                <div v-if="selectedReport.official_response">
                    <p class="text-gray-500 text-xs mb-1">Official Response</p>
                    <p class="text-gray-800 text-sm bg-green-50 p-3 rounded-md">{{ selectedReport.official_response }}</p>
                </div>

                <div v-if="selectedReport.status === 'pending'" class="border-t pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Respond to Report</p>
                    <form @submit.prevent="submitResponse" class="space-y-3">
                        <div>
                            <textarea v-model="respondForm.official_response" rows="3" placeholder="Write your response..." class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                            <p v-if="respondForm.errors.official_response" class="text-red-500 text-xs mt-1">{{ respondForm.errors.official_response }}</p>
                        </div>
                        <div>
                            <select v-model="respondForm.status" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="reviewed">Mark as Reviewed</option>
                                <option value="resolved">Mark as Resolved</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showViewModal = false" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                            <button type="submit" :disabled="respondForm.processing" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">Submit Response</button>
                        </div>
                    </form>
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

const showViewModal = ref(false)
const selectedReport = ref(null)

const respondForm = useForm({
    official_response: '',
    status: 'reviewed',
})

const statusClass = (status) => ({
    pending: 'bg-yellow-100 text-yellow-700',
    reviewed: 'bg-blue-100 text-blue-700',
    resolved: 'bg-green-100 text-green-700',
}[status] ?? 'bg-gray-100 text-gray-700')

const formatDate = (date) => new Date(date).toLocaleString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
})

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
</script>