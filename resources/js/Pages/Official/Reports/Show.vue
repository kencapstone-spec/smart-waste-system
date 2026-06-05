<template>
    <AuthLayout page-title="Report Details">
        <div class="mb-6">
            <Link :href="route('official.reports.index')" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Reports
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Report Info -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span :class="typeClass(report.type)" class="px-3 py-1 rounded-full text-xs font-medium">
                            {{ report.type === 'missed_collection' ? 'Missed Garbage Collection' : 'Illegal Dumping' }}
                        </span>
                        <span :class="statusClass(report.status)" class="px-3 py-1 rounded-full text-xs font-medium capitalize">
                            {{ report.status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Description</p>
                        <p class="text-gray-700 text-sm bg-gray-50 p-4 rounded-md">{{ report.description }}</p>
                    </div>

                    <div v-if="report.latitude && report.longitude">
                        <p class="text-xs text-gray-400 mb-1">Location</p>
                        <p class="text-sm text-gray-600">{{ report.latitude }}, {{ report.longitude }}</p>
                    </div>

                    <div v-if="report.photos?.length > 0">
                        <p class="text-xs text-gray-400 mb-2">Photos</p>
                        <div class="flex gap-3 flex-wrap">
                            <img
                                v-for="photo in report.photos"
                                :key="photo.id"
                                :src="'/storage/' + photo.photo_path"
                                class="w-32 h-32 object-cover rounded-md border"
                            />
                        </div>
                    </div>

                    <div v-if="report.official_response" class="bg-green-50 border border-green-200 p-4 rounded-md">
                        <p class="text-xs text-gray-400 mb-1">Official Response</p>
                        <p class="text-gray-700 text-sm">{{ report.official_response }}</p>
                        <p class="text-xs text-gray-400 mt-2">Responded by {{ report.responded_by?.name }} on {{ formatDate(report.responded_at) }}</p>
                    </div>
                </div>

                <!-- Respond Form -->
                <div v-if="report.status === 'pending'" class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Respond to this Report</h3>
                    <form @submit.prevent="submitResponse" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Response</label>
                            <textarea
                                v-model="respondForm.official_response"
                                rows="4"
                                placeholder="Write your official response..."
                                class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            ></textarea>
                            <p v-if="respondForm.errors.official_response" class="text-red-500 text-xs mt-1">{{ respondForm.errors.official_response }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                            <select v-model="respondForm.status" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="reviewed">Mark as Reviewed</option>
                                <option value="resolved">Mark as Resolved</option>
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" :disabled="respondForm.processing" class="bg-green-600 text-white px-5 py-2 rounded-md text-sm hover:bg-green-700 disabled:opacity-60">
                                Submit Response
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar: Resident Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Resident Info</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Name</dt>
                            <dd class="font-medium text-gray-800">{{ report.resident?.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Phone</dt>
                            <dd class="text-gray-600">{{ report.resident?.phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Date Submitted</dt>
                            <dd class="text-gray-600">{{ formatDate(report.created_at) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const props = defineProps({
    report: Object,
})

const respondForm = useForm({
    official_response: '',
    status: 'reviewed',
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

const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'short', day: 'numeric',
}) : '—'

const submitResponse = () => {
    respondForm.post(route('official.reports.respond', props.report.id))
}
</script>
