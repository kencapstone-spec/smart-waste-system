<template>
    <AuthLayout page-title="Printed Reports">
        <!-- Header Banner -->
        <div class="relative overflow-hidden bg-gradient-to-r from-rose-950 via-rose-900 to-rose-800 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-rose-950/20 mb-8">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute right-20 -bottom-10 w-36 h-36 bg-amber-400/20 rounded-full blur-xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-rose-200 text-xs font-semibold mb-3 border border-white/10">
                        <component :is="Printer" class="w-3.5 h-3.5" />
                        <span>Official Barangay Documentation Hub</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">Printed Reports &amp; PDF Exports</h1>
                    <p class="text-rose-100/80 text-sm mt-1 max-w-xl">
                        Generate, preview, and print official government-formatted PDF summaries for waste collections, citizen grievances, schedules, and participation records.
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 text-right">
                        <p class="text-[10px] uppercase font-bold text-rose-200/80">Barangay</p>
                        <p class="text-sm font-black text-white">San Isidro, Talibon</p>
                    </div>
                </div>
            </div>
        </div>



        <!-- 4 Main Report Generator Cards -->
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-1.5 h-5 bg-rose-600 rounded-full"></span>
            Report Generators
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <!-- 1. Collection Activity Summary Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 border border-white/70 shadow-xl shadow-rose-900/5 hover:shadow-2xl hover:shadow-emerald-950/10 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
                                <component :is="CheckCircle" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Collection Activity Summary</h3>
                                <p class="text-xs text-gray-500">Comprehensive log of scheduled collections, completed tasks, missed routes &amp; remarks.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100/70 text-emerald-800 text-[10px] font-bold rounded-lg uppercase tracking-wider">Landscape</span>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">From Date</label>
                                <input v-model="collectionFilters.from" type="date" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">To Date</label>
                                <input v-model="collectionFilters.to" type="date" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Purok / Zone</label>
                                <select v-model="collectionFilters.zone_id" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                    <option value="">All Puroks</option>
                                    <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Collection Status</label>
                                <select v-model="collectionFilters.status" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                    <option value="">All Statuses</option>
                                    <option value="completed">Completed Only</option>
                                    <option value="missed">Missed Only</option>
                                    <option value="pending">Pending Only</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-5 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button @click="openReport('official.pdf.collection-summary', collectionFilters, 'stream')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-xl text-xs font-bold transition-all">
                        <component :is="Printer" class="w-3.5 h-3.5" />
                        <span>Print / Preview</span>
                    </button>
                    <button @click="openReport('official.pdf.collection-summary', collectionFilters, 'download')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition-all">
                        <component :is="FileDown" class="w-3.5 h-3.5" />
                        <span>Download PDF</span>
                    </button>
                </div>
            </div>

            <!-- 2. Complaints & Incidents Summary Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 border border-white/70 shadow-xl shadow-rose-900/5 hover:shadow-2xl hover:shadow-rose-950/10 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-xl shadow-sm">
                                <component :is="FileText" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Complaints &amp; Incidents Summary</h3>
                                <p class="text-xs text-gray-500">Resident reports regarding missed collections, illegal dumping, and resolutions taken.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-rose-100/70 text-rose-800 text-[10px] font-bold rounded-lg uppercase tracking-wider">Landscape</span>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">From Date</label>
                                <input v-model="complaintFilters.from" type="date" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">To Date</label>
                                <input v-model="complaintFilters.to" type="date" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Purok</label>
                                <select v-model="complaintFilters.zone_id" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                    <option value="">All Puroks</option>
                                    <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Report Type</label>
                                <select v-model="complaintFilters.type" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                    <option value="">All Types</option>
                                    <option value="missed_collection">Missed Collection</option>
                                    <option value="illegal_dumping">Illegal Dumping</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Status</label>
                                <select v-model="complaintFilters.status" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="resolved">Resolved</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-5 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button @click="openReport('official.pdf.complaints-summary', complaintFilters, 'stream')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-800 rounded-xl text-xs font-bold transition-all">
                        <component :is="Printer" class="w-3.5 h-3.5" />
                        <span>Print / Preview</span>
                    </button>
                    <button @click="openReport('official.pdf.complaints-summary', complaintFilters, 'download')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 transition-all">
                        <component :is="FileDown" class="w-3.5 h-3.5" />
                        <span>Download PDF</span>
                    </button>
                </div>
            </div>

            <!-- 3. Resident Participation & Points Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 border border-white/70 shadow-xl shadow-rose-900/5 hover:shadow-2xl hover:shadow-sky-950/10 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center text-xl shadow-sm">
                                <component :is="Users" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Resident Participation &amp; Rankings</h3>
                                <p class="text-xs text-gray-500">Citizen incentive point balances, proper disposal compliance leaderboard, and purok records.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-sky-100/70 text-sky-800 text-[10px] font-bold rounded-lg uppercase tracking-wider">Portrait</span>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Purok / Zone</label>
                                <select v-model="participationFilters.zone_id" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                                    <option value="">All Puroks</option>
                                    <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Min. Points</label>
                                <input v-model="participationFilters.min_points" type="number" min="0" placeholder="e.g. 10" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Sorting Order</label>
                                <select v-model="participationFilters.sort" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                                    <option value="points">Top Points First</option>
                                    <option value="name">Alphabetical (A-Z)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-5 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button @click="openReport('official.pdf.resident-participation', participationFilters, 'stream')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-50 hover:bg-sky-100 text-sky-800 rounded-xl text-xs font-bold transition-all">
                        <component :is="Printer" class="w-3.5 h-3.5" />
                        <span>Print / Preview</span>
                    </button>
                    <button @click="openReport('official.pdf.resident-participation', participationFilters, 'download')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold shadow-md shadow-sky-600/20 transition-all">
                        <component :is="FileDown" class="w-3.5 h-3.5" />
                        <span>Download PDF</span>
                    </button>
                </div>
            </div>

            <!-- 4. Collection Schedules Master List Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 border border-white/70 shadow-xl shadow-rose-900/5 hover:shadow-2xl hover:shadow-indigo-950/10 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-sm">
                                <component :is="Calendar" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Collection Schedules Master Roster</h3>
                                <p class="text-xs text-gray-500">Official timetable of collection days, times, frequency, and assigned collectors per purok.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-indigo-100/70 text-indigo-800 text-[10px] font-bold rounded-lg uppercase tracking-wider">Portrait</span>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Purok</label>
                                <select v-model="scheduleFilters.zone_id" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="">All Puroks</option>
                                    <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Frequency</label>
                                <select v-model="scheduleFilters.frequency" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="">All Frequencies</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="biweekly">Bi-weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="once">Once</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Status</label>
                                <select v-model="scheduleFilters.status" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-5 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button @click="openReport('official.pdf.schedules', scheduleFilters, 'stream')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-800 rounded-xl text-xs font-bold transition-all">
                        <component :is="Printer" class="w-3.5 h-3.5" />
                        <span>Print / Preview</span>
                    </button>
                    <button @click="openReport('official.pdf.schedules', scheduleFilters, 'download')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition-all">
                        <component :is="FileDown" class="w-3.5 h-3.5" />
                        <span>Download PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Incident Reports (1-Click Incident Sheet Print) -->
        <div class="bg-white/70 backdrop-blur-2xl rounded-3xl p-6 border border-white/60 shadow-xl shadow-rose-900/5 mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-rose-600 rounded-full"></span>
                        Quick Single-Incident Print
                    </h2>
                    <p class="text-xs text-gray-500">Print formal individual investigation sheets for recent resident complaint reports.</p>
                </div>
                <Link :href="route('official.reports.index')" class="text-xs font-bold text-rose-700 hover:text-rose-900 flex items-center gap-1">
                    <span>View all reports</span>
                    <component :is="ExternalLink" class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-left text-gray-500 font-semibold">
                            <th class="py-3 px-4">Ref No.</th>
                            <th class="py-3 px-4">Resident</th>
                            <th class="py-3 px-4">Purok</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Date Logged</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="rep in recentReports" :key="rep.id" class="hover:bg-rose-50/40 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-rose-950">IR-{{ String(rep.id).padStart(5, '0') }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-900">{{ rep.resident?.name ?? 'Resident' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ rep.resident?.zone?.name ?? '—' }}</td>
                            <td class="py-3 px-4">
                                <span :class="rep.type === 'missed_collection' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'" class="px-2 py-0.5 rounded-md font-semibold text-[10px]">
                                    {{ rep.type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span :class="rep.status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" class="px-2 py-0.5 rounded-md font-bold text-[10px] uppercase">
                                    {{ rep.status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-500">{{ new Date(rep.created_at).toLocaleDateString() }}</td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button @click="printSingleReport(rep.id, 'stream')" title="Print Sheet" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 transition-colors">
                                        <component :is="Printer" class="w-3.5 h-3.5" />
                                    </button>
                                    <button @click="printSingleReport(rep.id, 'download')" title="Download PDF" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                                        <component :is="FileDown" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!recentReports || recentReports.length === 0">
                            <td colspan="7" class="py-6 text-center text-gray-400">No complaint reports logged yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Link } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import {
    Printer,
    FileDown,
    Calendar,
    FileText,
    Users,
    CheckCircle,
    ExternalLink,
} from 'lucide-vue-next'

const props = defineProps({
    zones: Array,
    stats: Object,
    recentReports: Array,
})

const collectionFilters = reactive({
    from: '',
    to: '',
    zone_id: '',
    status: '',
})

const complaintFilters = reactive({
    from: '',
    to: '',
    zone_id: '',
    type: '',
    status: '',
})

const participationFilters = reactive({
    zone_id: '',
    min_points: '',
    sort: 'points',
})

const scheduleFilters = reactive({
    zone_id: '',
    frequency: '',
    status: '',
})

const openReport = (routeName, filters, action = 'stream') => {
    const params = new URLSearchParams()
    Object.entries(filters).forEach(([k, v]) => {
        if (v !== '' && v !== null && v !== undefined) {
            params.append(k, v)
        }
    })
    params.append('action', action)
    const url = `${route(routeName)}?${params.toString()}`

    if (action === 'stream') {
        window.open(url, '_blank')
    } else {
        window.location.href = url
    }
}

const printSingleReport = (reportId, action = 'stream') => {
    const url = `${route('official.pdf.single-complaint', reportId)}?action=${action}`
    if (action === 'stream') {
        window.open(url, '_blank')
    } else {
        window.location.href = url
    }
}
</script>
