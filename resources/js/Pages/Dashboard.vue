<template>
    <AuthLayout page-title="Dashboard">
        
        <!-- Welcome Header (All roles) -->
        <div class="mb-10 relative">
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-rose-400/20 rounded-full blur-3xl -z-10"></div>
            <div class="absolute top-0 right-20 w-32 h-32 bg-amber-400/20 rounded-full blur-3xl -z-10"></div>
            
            <h2 class="text-3xl md:text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-600 to-rose-400">{{ auth.user.name.split(' ')[0] }}</span>! <component :is="Smile" class="inline-block w-8 h-8 text-rose-500 mb-1" />
            </h2>
            <p class="text-gray-500 mt-2 text-lg">Here is what's happening today.</p>
        </div>

        <div v-if="auth.user.role === 'super_admin'" class="space-y-10">
            <div class="grid grid-cols-2 md:grid-cols-2 gap-3 md:gap-6">
                <!-- Total Staff (clickable) -->
                <a :href="route('super-admin.users.index')" class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(225,29,72,0.12)] transition-all duration-300 group hover:-translate-y-1 cursor-pointer block">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700 ease-out"></div>
                    <div class="relative flex items-center justify-between z-10">
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-2">Total Staff</p>
                            <p class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ stats.totalStaff }}</p>
                            <p class="text-xs text-indigo-500 font-semibold mt-1 flex items-center gap-1">
                                View all
                                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </p>
                        </div>
                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-gradient-to-br from-indigo-50 to-white flex items-center justify-center text-3xl shadow-sm border border-indigo-100/50 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                            <component :is="Users" class="w-6 h-6 md:w-8 md:h-8 text-indigo-500" />
                        </div>
                    </div>
                </a>
                <!-- Zones (clickable) -->
                <a :href="route('super-admin.zones.index')" class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(245,158,11,0.12)] transition-all duration-300 group hover:-translate-y-1 cursor-pointer block">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-gradient-to-br from-amber-100 to-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700 ease-out"></div>
                    <div class="relative flex items-center justify-between z-10">
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-2">System Zones</p>
                            <p class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ stats.totalZones }}</p>
                            <p class="text-xs text-amber-500 font-semibold mt-1 flex items-center gap-1">
                                View all
                                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </p>
                        </div>
                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-gradient-to-br from-amber-50 to-white flex items-center justify-center shadow-sm border border-amber-100/50 group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300 text-amber-500">
                            <component :is="MapIcon" class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                    </div>
                </a>
            </div>
        </div>


        <!-- Barangay Official Dashboard -->
        <div v-else-if="auth.user.role === 'barangay_official'" class="space-y-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-3 md:gap-6">
                <!-- Active Schedules -->
                <div class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-indigo-900/5 transition-all duration-300 group hover:-translate-y-1">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform"><component :is="Calendar" class="w-6 h-6 md:w-8 md:h-8" /></div>
                        <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-1">Active Schedules</p>
                        <p class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900">{{ stats.activeSchedules }}</p>
                    </div>
                </div>

                <!-- Pending Reports -->
                <div class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-amber-900/5 transition-all duration-300 group hover:-translate-y-1">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">⚠️</div>
                        <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-1">Pending Reports</p>
                        <p class="text-2xl sm:text-3xl md:text-4xl font-black text-amber-500">{{ stats.pendingReports }}</p>
                    </div>
                </div>

                <!-- Pending Residents -->
                <div class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-rose-900/5 transition-all duration-300 group hover:-translate-y-1">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">⏳</div>
                        <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-1">Pending Residents</p>
                        <p class="text-2xl sm:text-3xl md:text-4xl font-black text-rose-500">{{ stats.pendingResidents }}</p>
                    </div>
                </div>

                <!-- Active Residents (Premium Green) -->
                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-3 sm:p-5 md:p-6 border border-emerald-400 shadow-lg shadow-emerald-500/30 transition-all duration-300 group hover:-translate-y-1 hover:shadow-emerald-500/40">
                    <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30"></div>
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors duration-700"></div>
                    <div class="relative z-10 text-white">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl border border-white/30 mb-4 group-hover:scale-110 transition-transform"><component :is="Users" class="w-6 h-6 md:w-8 md:h-8" /></div>
                        <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider mb-1">Active Residents</p>
                        <p class="text-2xl sm:text-3xl md:text-4xl font-black">{{ stats.activeResidents }}</p>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-rose-500 rounded-full"></span>
                    Performance KPIs
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-3 md:gap-6">
                    <!-- Best Performing Zone -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-3 sm:p-5 md:p-6 md:p-8 border border-amber-400 shadow-lg shadow-amber-500/30 transition-all duration-300 group hover:-translate-y-1 hover:shadow-amber-500/40">
                        <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30"></div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors duration-700"></div>
                        <div class="relative z-10 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-3xl border border-white/30 group-hover:scale-110 group-hover:rotate-6 transition-transform"><component :is="Trophy" class="w-6 h-6 md:w-8 md:h-8" /></div>
                                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold uppercase tracking-wider border border-white/30">Top Zone</span>
                            </div>
                            <p class="text-3xl md:text-2xl sm:text-3xl md:text-4xl font-black mb-2 truncate">{{ stats.kpis?.bestZone }}</p>
                            <p class="text-sm font-semibold text-amber-100 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                {{ stats.kpis?.bestZoneTasks }} Completed Tasks
                            </p>
                        </div>
                    </div>

                    <!-- Best Performing Resident -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-rose-500 to-rose-600 rounded-3xl p-3 sm:p-5 md:p-6 md:p-8 border border-rose-400 shadow-lg shadow-rose-500/30 transition-all duration-300 group hover:-translate-y-1 hover:shadow-rose-500/40">
                        <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30"></div>
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors duration-700"></div>
                        <div class="relative z-10 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-3xl border border-white/30 group-hover:scale-110 group-hover:-rotate-6 transition-transform">⭐</div>
                                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-bold uppercase tracking-wider border border-white/30">Top Resident</span>
                            </div>
                            <p class="text-3xl md:text-2xl sm:text-3xl md:text-4xl font-black mb-2 truncate">{{ stats.kpis?.bestResident }}</p>
                            <p class="text-sm font-semibold text-rose-100 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                {{ stats.kpis?.bestResidentPoints }} Points Earned
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Chart Section -->
            <div class="mt-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-rose-500 rounded-full"></span>
                    System Analytics
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-6">
                    <div class="lg:col-span-2 bg-white rounded-3xl p-3 sm:p-5 md:p-6 border border-gray-100 shadow-sm">
                        <h4 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-4">Collection Performance (Last 7 Days)</h4>
                        <div class="h-72 relative">
                            <Line v-if="stats.chartData" :data="lineData" :options="lineOptions" />
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl p-3 sm:p-5 md:p-6 border border-gray-100 shadow-sm flex flex-col">
                        <h4 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-4">Task Status Breakdown</h4>
                        <div class="flex-1 min-h-[250px] relative flex items-center justify-center">
                            <Doughnut v-if="stats.chartData" :data="doughnutData" :options="doughnutOptions" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personnel Dashboard -->
        <div v-else-if="auth.user.role === 'personnel'" class="space-y-10">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-6">
                <!-- Card 1 -->
                <div class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-xl hover:shadow-rose-900/10 transition-all duration-300 group hover:-translate-y-1">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-2">My Schedules</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-2xl sm:text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ stats.assignedSchedules }}</p>
                                <span class="text-sm font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">Active</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-white flex items-center justify-center text-3xl shadow-sm border border-gray-100 group-hover:rotate-6 transition-transform">
                            <component :is="Calendar" class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-xl hover:shadow-blue-900/10 transition-all duration-300 group hover:-translate-y-1">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-2">Tasks Today</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-2xl sm:text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ stats.tasksToday }}</p>
                                <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">To do</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-white flex items-center justify-center text-3xl shadow-sm border border-gray-100 group-hover:-rotate-6 transition-transform">
                            <component :is="ClipboardList" class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-3 sm:p-5 md:p-8 border border-emerald-400 shadow-lg shadow-emerald-500/30 transition-all duration-300 group hover:-translate-y-1 hover:shadow-emerald-500/40">
                    <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-30"></div>
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors duration-700"></div>
                    
                    <div class="relative z-10 flex items-center justify-between text-white">
                        <div>
                            <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider mb-2">Completed</p>
                            <p class="text-2xl sm:text-2xl sm:text-3xl md:text-4xl font-black tracking-tight">{{ stats.completedTasks }}</p>
                        </div>
                        <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-3xl border border-white/30 group-hover:scale-110 transition-transform">
                            <component :is="CheckCircle" class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-rose-500 rounded-full"></span>
                    Quick Actions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a :href="route('personnel.schedules.index')" class="relative overflow-hidden bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-rose-900/5 hover:-translate-y-1 transition-all duration-300 group flex items-center justify-between">
                        <div class="flex items-center gap-5 relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-600 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                <component :is="Calendar" class="w-6 h-6 md:w-8 md:h-8" />
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">View Schedules</p>
                                <p class="text-sm text-gray-500 mt-1">Check your upcoming routes</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300 relative z-10">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-indigo-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a :href="route('personnel.tasks.index')" class="relative overflow-hidden bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-emerald-900/5 hover:-translate-y-1 transition-all duration-300 group flex items-center justify-between">
                        <div class="flex items-center gap-5 relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-600 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                                <component :is="CheckCircle" class="w-6 h-6 md:w-8 md:h-8" />
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">Update Tasks</p>
                                <p class="text-sm text-gray-500 mt-1">Mark completed & award points</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300 relative z-10">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-emerald-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Resident Dashboard -->
        <div v-else class="space-y-10">
            <!-- Points Card -->
            <div class="relative bg-gradient-to-br from-rose-950 via-rose-900 to-rose-800 rounded-3xl p-3 sm:p-5 md:p-8 md:p-10 overflow-hidden shadow-2xl shadow-rose-900/30 text-white group">
                <!-- Premium glassmorphism effects -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-rose-500/30 rounded-full blur-3xl mix-blend-screen group-hover:bg-rose-400/40 transition-colors duration-700"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-red-500/30 rounded-full blur-3xl mix-blend-screen"></div>
                
                <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==')] opacity-20"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-md rounded-full border border-white/20 mb-4">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs font-bold tracking-wider uppercase text-rose-100">Reward Balance</span>
                        </div>
                        <div class="flex items-baseline gap-3">
                            <span class="text-6xl md:text-7xl font-black tracking-tight drop-shadow-lg">{{ stats.totalPoints }}</span>
                            <span class="text-2xl font-bold text-rose-200/80 mb-2">pts</span>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <a :href="route('resident.rewards.index')" class="group/btn relative inline-flex items-center justify-center bg-white text-rose-900 px-8 py-4 rounded-2xl font-extrabold text-sm shadow-[0_0_40px_rgba(255,255,255,0.3)] hover:shadow-[0_0_60px_rgba(255,255,255,0.5)] transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                            <span class="relative z-10 flex items-center gap-2">
                                Redeem Rewards
                                <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-rose-50 to-white opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-6">
                <!-- Total Reports -->
                <div class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-xl hover:shadow-rose-900/5 transition-all duration-300 group hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-3xl group-hover:scale-110 group-hover:-rotate-6 transition-transform">
                            <component :is="FileText" class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-amber-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-2">Total Reports</p>
                    <p class="text-2xl sm:text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 tracking-tight">{{ stats.totalReports }}</p>
                </div>
                
                <!-- Account Status -->
                <div class="relative overflow-hidden bg-white rounded-3xl p-3 sm:p-5 md:p-8 border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-xl hover:shadow-emerald-900/5 transition-all duration-300 group hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-3xl group-hover:scale-110 group-hover:rotate-6 transition-transform">
                            <component :is="ShieldCheck" class="w-6 h-6 md:w-8 md:h-8" />
                        </div>
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border"
                            :class="auth.user.status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                            {{ auth.user.status }}
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider break-words w-full mb-2">Account Status</p>
                    <p class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 capitalize tracking-tight">{{ auth.user.status }}</p>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-rose-500 rounded-full"></span>
                    Quick Navigation
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a :href="route('resident.schedules.index')" class="relative overflow-hidden bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-rose-900/5 hover:-translate-y-1 transition-all duration-300 group flex items-center justify-between">
                        <div class="flex items-center gap-5 relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-50 to-rose-100 text-rose-600 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300"><component :is="Calendar" class="w-6 h-6 md:w-8 md:h-8" /></div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">Collection Schedules</p>
                                <p class="text-sm text-gray-500 mt-0.5">When is the garbage truck coming?</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300 relative z-10">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-rose-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    <a :href="route('resident.reports.index')" class="relative overflow-hidden bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-amber-900/5 hover:-translate-y-1 transition-all duration-300 group flex items-center justify-between">
                        <div class="flex items-center gap-5 relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100 text-amber-600 flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300"><component :is="FileText" class="w-6 h-6 md:w-8 md:h-8" /></div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg">Submit Report</p>
                                <p class="text-sm text-gray-500 mt-0.5">Report missed collections or issues</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300 relative z-10">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-amber-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Smile, Users, Home as HomeIcon, Map as MapIcon, Calendar, AlertTriangle, Clock, Trophy, Star, ClipboardList, CheckCircle, FileText, ShieldCheck } from '@lucide/vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  Filler
} from 'chart.js'
import { Line, Doughnut } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  Filler
)

const page = usePage()
const auth = computed(() => page.props.auth)

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
})

const lineData = computed(() => ({
    labels: props.stats.chartData?.performance.labels || [],
    datasets: [
        {
            label: 'Completed Tasks',
            backgroundColor: 'rgba(225, 29, 72, 0.1)', // rose-600
            borderColor: 'rgb(225, 29, 72)',
            borderWidth: 2,
            pointBackgroundColor: 'rgb(225, 29, 72)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(225, 29, 72)',
            fill: true,
            tension: 0.4,
            data: props.stats.chartData?.performance.data || []
        }
    ]
}))

const lineOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            ticks: { stepSize: 1, precision: 0 }
        }
    },
    plugins: {
        legend: { display: false }
    }
}

const doughnutData = computed(() => ({
    labels: ['Completed', 'Pending', 'Missed'],
    datasets: [
        {
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'], // emerald, amber, red
            borderWidth: 0,
            hoverOffset: 4,
            data: [
                props.stats.chartData?.statuses.completed || 0,
                props.stats.chartData?.statuses.pending || 0,
                props.stats.chartData?.statuses.missed || 0,
            ]
        }
    ]
}))

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '75%',
    plugins: {
        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
    }
}
</script>