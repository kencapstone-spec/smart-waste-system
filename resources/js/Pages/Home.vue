<template>
    <div class="min-h-screen bg-white overflow-x-hidden text-base">

        <!-- ============================================================ -->
        <!-- NAVBAR -->
        <!-- ============================================================ -->
        <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
            :class="scrolled ? 'bg-white/90 backdrop-blur-xl shadow-lg shadow-rose-900/5' : 'bg-transparent'">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-18">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                        :class="scrolled ? 'bg-rose-900 text-white' : 'bg-white/15 text-white backdrop-blur-sm'">
                        <component :is="Leaf" class="w-5 h-5" />
                    </div>
                    <span class="font-bold text-lg tracking-tight"
                        :class="scrolled ? 'text-rose-950' : 'text-white'">
                        SmartWaste
                    </span>
                </div>
                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a v-for="link in navLinks" :key="link.id" :href="'#' + link.id"
                        class="transition-colors duration-300 hover:opacity-80"
                        :class="scrolled ? 'text-rose-950/80 hover:text-rose-900' : 'text-white/80 hover:text-white'"
                        @click.prevent="scrollToSection(link.id)">
                        {{ link.label }}
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <a :href="route('login')"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300"
                        :class="scrolled
                            ? 'bg-rose-900 text-white hover:bg-rose-800 shadow-lg shadow-rose-900/25'
                            : 'bg-white text-rose-900 hover:bg-white/90 shadow-lg shadow-black/10'">
                        Sign In
                    </a>
                </div>
            </div>
        </nav>

        <!-- ============================================================ -->
        <!-- HERO SECTION -->
        <!-- ============================================================ -->
        <section class="relative min-h-screen flex flex-col">
            <!-- Background Elements Wrapper -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <!-- Gradient Background -->
                <div class="absolute inset-0 bg-gradient-to-br from-rose-950 via-rose-900 to-red-900"></div>

                <!-- Animated mesh pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 left-0 w-full h-full"
                        style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.15) 1px, transparent 1px), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;">
                    </div>
                </div>

                <!-- Floating orbs -->
                <div class="absolute top-20 right-20 w-96 h-96 bg-rose-400/20 rounded-full blur-3xl animate-float"></div>
                <div class="absolute bottom-20 left-10 w-72 h-72 bg-red-400/15 rounded-full blur-3xl animate-float-delayed"></div>
                <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-rose-300/10 rounded-full blur-3xl animate-float-slow"></div>
                
            </div>

            <div class="relative flex-1 flex flex-col max-w-7xl mx-auto px-6 lg:px-8 pt-32 pb-20 w-full">
                <div class="my-auto grid lg:grid-cols-2 gap-16 items-center">
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 text-xs font-medium text-white border border-white/10">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            Barangay San Isidro • Talibon, Bohol
                        </div>

                        <h1 class="text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white leading-[1.05] tracking-tight">
                            Smart Waste
                            <span class="block text-white">
                                Collection
                            </span>
                            <span class="block text-3xl lg:text-4xl font-semibold text-white mt-2">
                                Monitoring & Scheduling
                            </span>
                        </h1>

                        <p class="text-lg text-white max-w-lg leading-relaxed">
                            A modern platform that connects residents, officials, and collection personnel
                            for cleaner, smarter, and more efficient waste management in our barangay.
                        </p>

                        <div class="flex flex-wrap gap-4 pt-2">
                            <a :href="route('register')"
                                class="group relative px-8 py-3.5 bg-white text-rose-900 rounded-2xl font-semibold text-sm overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-white/20 hover:-translate-y-0.5">
                                <span class="relative z-10">Register as Resident</span>
                                <div class="absolute inset-0 bg-gradient-to-r from-rose-50 to-white opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </a>
                            <a href="#features"
                                @click.prevent="scrollToSection('features')"
                                class="px-8 py-3.5 border border-white/20 text-white rounded-2xl font-semibold text-sm backdrop-blur-sm hover:bg-white/10 transition-all duration-300">
                                Learn More ↓
                            </a>
                        </div>

                        <!-- Trust badges -->
                        <div class="flex items-center gap-6 pt-4">
                            <div class="flex items-center gap-2 text-white text-xs">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                <span>OTP Secured</span>
                            </div>
                            <div class="flex items-center gap-2 text-white text-xs">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                <span>Real-Time Updates</span>
                            </div>
                            <div class="flex items-center gap-2 text-white text-xs">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                <span>PDF Reports</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Card Illustration -->
                    <div class="hidden lg:block relative">
                        <div class="relative">
                            <!-- Main card -->
                            <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-8 border border-white/15 shadow-2xl">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between bg-white/10 rounded-xl p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-emerald-500/30 flex items-center justify-center text-emerald-300"><component :is="CalendarDays" class="w-7 h-7" /></div>
                                            <div>
                                                <p class="text-sm font-medium text-white">Monday Collection</p>
                                                <p class="text-xs text-rose-200/60">Purok Oten • Zone 1</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded-full text-xs font-medium">Active</span>
                                    </div>
                                    <div class="flex items-center justify-between bg-white/10 rounded-xl p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-amber-500/30 flex items-center justify-center text-amber-300"><component :is="FileText" class="w-7 h-7" /></div>
                                            <div>
                                                <p class="text-sm font-medium text-white">Missed Collection Report</p>
                                                <p class="text-xs text-rose-200/60">Submitted 2h ago</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 bg-amber-500/20 text-amber-300 rounded-full text-xs font-medium">Pending</span>
                                    </div>
                                    <div class="flex items-center justify-between bg-white/10 rounded-xl p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-rose-500/30 flex items-center justify-center text-rose-300">⭐</div>
                                            <div>
                                                <p class="text-sm font-medium text-white">Points Awarded</p>
                                                <p class="text-xs text-rose-200/60">+15 pts — Proper Segregation</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 bg-rose-500/20 text-rose-300 rounded-full text-xs font-medium">+15</span>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- LEADERBOARD SECTION -->
        <!-- ============================================================ -->
        <section id="leaderboard" class="py-24 bg-rose-50/30">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-sm font-semibold text-amber-600 tracking-widest uppercase mb-3">Top Performers</p>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">
                        Resident Leaderboard
                    </h2>
                    <p class="text-gray-500 mt-4 text-lg">
                        Recognizing our top 10 residents who are leading the way in clean waste management.
                    </p>
                </div>

                <div v-if="leaderboard && leaderboard.length > 0" class="bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-xl shadow-rose-900/5">
                    <div class="divide-y divide-gray-50">
                        <div v-for="(resident, index) in leaderboard" :key="resident.name" 
                            class="flex items-center justify-between p-6 hover:bg-gray-50 transition-colors"
                            :class="index < 3 ? 'bg-amber-50/30' : ''">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl font-bold shadow-sm"
                                    :class="index === 0 ? 'bg-gradient-to-br from-amber-200 to-amber-400 text-amber-900 border border-amber-300' : (index === 1 ? 'bg-gradient-to-br from-gray-200 to-gray-300 text-gray-700 border border-gray-300' : (index === 2 ? 'bg-gradient-to-br from-orange-200 to-orange-300 text-orange-800 border border-orange-300' : 'bg-rose-50 text-rose-900 border border-rose-100'))">
                                    <component v-if="index === 0" :is="Trophy" class="w-6 h-6" />
                                    <component v-else-if="index === 1" :is="Medal" class="w-6 h-6" />
                                    <component v-else-if="index === 2" :is="Award" class="w-6 h-6" />
                                    <span v-else>{{ index + 1 }}</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-lg">{{ resident.name }}</h4>
                                    <p class="text-sm text-gray-500">{{ resident.zone }} • {{ resident.address }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-black text-rose-600">{{ resident.points }}</div>
                                <div class="text-xs font-semibold text-rose-300 uppercase tracking-wider">Points</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="text-center py-12 bg-white rounded-3xl border border-gray-100 border-dashed shadow-sm">
                    <div class="mb-4 opacity-50 flex justify-center text-gray-400">
                        <component :is="Trophy" class="w-12 h-12" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">No points awarded yet</h3>
                    <p class="text-gray-500">The leaderboard will update automatically once residents start earning points!</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- STATS BAR -->
        <!-- ============================================================ -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div v-for="stat in stats" :key="stat.label"
                        class="text-center group">
                        <p class="text-3xl lg:text-4xl font-extrabold text-rose-900 group-hover:scale-110 transition-transform duration-300">
                            {{ stat.value }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- FEATURES -->
        <!-- ============================================================ -->
        <section id="features" class="py-24 bg-gradient-to-b from-white to-rose-50/50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <p class="text-sm font-semibold text-rose-900/70 tracking-widest uppercase mb-3">Features</p>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">
                        Everything your barangay needs
                    </h2>
                    <p class="text-gray-500 mt-4 text-lg">
                        From scheduling to reporting, our platform handles it all.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="feature in features" :key="feature.title"
                        class="group relative bg-white rounded-2xl p-8 border border-gray-100 hover:border-rose-200 hover:shadow-xl hover:shadow-rose-900/5 transition-all duration-500 hover:-translate-y-1">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3"
                            :class="feature.bgClass">
                            <component :is="feature.icon" class="w-7 h-7" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ feature.title }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ feature.description }}</p>
                        <div class="absolute bottom-0 left-8 right-8 h-0.5 bg-gradient-to-r from-rose-400 to-red-400 rounded-full scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- HOW IT WORKS -->
        <!-- ============================================================ -->
        <section id="how-it-works" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <p class="text-sm font-semibold text-rose-900/70 tracking-widest uppercase mb-3">How It Works</p>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">
                        Simple. Fast. Effective.
                    </h2>
                </div>

                <div class="grid md:grid-cols-4 gap-8">
                    <div v-for="(step, i) in steps" :key="step.title"
                        class="relative text-center group">
                        <!-- Connector line -->
                        <div v-if="i < steps.length - 1" class="hidden md:block absolute top-8 left-1/2 w-full h-px bg-gradient-to-r from-rose-300 to-rose-200"></div>
                        <div class="relative z-10 w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-rose-900 to-red-800 text-white flex items-center justify-center text-xl font-bold shadow-lg shadow-rose-900/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            {{ step.number }}
                        </div>
                        <h3 class="font-bold text-gray-900 mt-5 mb-2">{{ step.title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- ROLES SECTION -->
        <!-- ============================================================ -->
        <section id="roles" class="py-24 bg-gradient-to-b from-rose-50/50 to-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <p class="text-sm font-semibold text-rose-900/70 tracking-widest uppercase mb-3">User Roles</p>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">
                        Built for everyone
                    </h2>
                    <p class="text-gray-500 mt-4 text-lg">
                        Each role has a tailored experience and set of tools.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="role in roles" :key="role.title"
                        class="bg-white rounded-2xl p-7 border border-gray-100 hover:shadow-xl hover:shadow-rose-900/5 transition-all duration-500 hover:-translate-y-1">
                    <div class="mb-4">
                            <component :is="role.icon" class="w-10 h-10" :class="role.iconClass" />
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2 text-lg">{{ role.title }}</h3>
                        <ul class="space-y-2">
                            <li v-for="ability in role.abilities" :key="ability"
                                class="flex items-start gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-rose-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ ability }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- CTA SECTION -->
        <!-- ============================================================ -->
        <section class="py-24">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="relative bg-gradient-to-br from-rose-950 via-rose-900 to-red-900 rounded-3xl p-12 lg:p-16 overflow-hidden">
                    <!-- Decorative -->
                    <div class="absolute top-0 right-0 w-80 h-80 bg-rose-400/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-red-400/10 rounded-full blur-3xl"></div>

                    <div class="relative text-center max-w-2xl mx-auto">
                        <h2 class="text-3xl lg:text-5xl font-extrabold text-white tracking-tight mb-4">
                            Ready to get started?
                        </h2>
                        <p class="text-white text-lg mb-8">
                            Join the smart waste management system of Barangay San Isidro today. Register as a resident and start making a difference.
                        </p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a :href="route('register')"
                                class="px-8 py-3.5 bg-white text-rose-900 rounded-2xl font-semibold text-sm hover:bg-rose-50 transition-all duration-300 shadow-lg shadow-black/10 hover:-translate-y-0.5">
                                Register Now
                            </a>
                            <a :href="route('login')"
                                class="px-8 py-3.5 border border-white/20 text-white rounded-2xl font-semibold text-sm hover:bg-white/10 transition-all duration-300">
                                Sign In →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- ANNOUNCEMENTS SECTION -->
        <!-- ============================================================ -->
        <section v-if="announcements && announcements.length > 0" id="announcements" class="py-24 bg-white relative">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl md:text-4xl font-black text-rose-950 tracking-tight mb-4">Latest Announcements</h2>
                    <p class="text-lg text-rose-900/60 font-medium">Stay updated with the latest news and schedules from your Barangay.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="announcement in announcements" :key="announcement.id" class="bg-rose-50/50 rounded-3xl p-8 border border-rose-100/50 hover:bg-white hover:shadow-xl hover:shadow-rose-900/5 hover:-translate-y-1 transition-all duration-300 relative group overflow-hidden">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-rose-100 rounded-full blur-2xl opacity-50 group-hover:bg-rose-200 transition-colors"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-bold rounded-xl uppercase tracking-wider">{{ announcement.type }}</span>
                                <span class="text-sm text-gray-500 font-medium">{{ new Date(announcement.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-rose-950 mb-3">{{ announcement.title }}</h3>
                            <p class="text-gray-600 line-clamp-4 leading-relaxed">{{ announcement.content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- FOOTER -->
        <!-- ============================================================ -->
        <footer class="bg-gray-950 text-gray-400 py-16">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-12 mb-12">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-9 h-9 rounded-xl bg-rose-900 flex items-center justify-center text-lg"><component :is="Trash2" class="w-8 h-8" /></div>
                            <span class="font-bold text-lg text-white tracking-tight">SmartWaste</span>
                        </div>
                        <p class="text-sm leading-relaxed">
                            Smart Waste Collection Monitoring and Scheduling System for
                            Barangay San Isidro, Talibon, Bohol.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white text-sm mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#features" @click.prevent="scrollToSection('features')" class="hover:text-rose-400 transition-colors">Features</a></li>
                            <li><a href="#how-it-works" @click.prevent="scrollToSection('how-it-works')" class="hover:text-rose-400 transition-colors">How It Works</a></li>
                            <li><a href="#roles" @click.prevent="scrollToSection('roles')" class="hover:text-rose-400 transition-colors">User Roles</a></li>
                            <li><a :href="route('login')" class="hover:text-rose-400 transition-colors">Sign In</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white text-sm mb-4">About</h4>
                        <p class="text-sm leading-relaxed">
                            A capstone project for BS Information Systems at Talibon Polytechnic College.
                        </p>
                        <p class="text-sm mt-3 text-gray-500">
                            Barangay San Isidro, Talibon, Bohol, Philippines
                        </p>
                    </div>
                </div>
                <div class="border-t border-gray-800 pt-8 text-center text-xs text-gray-400">
                    © {{ new Date().getFullYear() }} SmartWaste System. All rights reserved.
                </div>
            </div>
        </footer>

    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
    CalendarDays,
    CheckCircle,
    FileText,
    Star,
    FileDown,
    ShieldCheck,
    Shield,
    Building,
    Truck,
    Home as HomeIcon,
    Trash2,
    Trophy,
    Medal,
    Award,
    Leaf
} from '@lucide/vue'

const props = defineProps({
    leaderboard: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Array,
        default: () => []
    },
    announcements: {
        type: Array,
        default: () => []
    }
})

// Navbar scroll state
const scrolled = ref(false)
const handleScroll = () => { scrolled.value = window.scrollY > 50 }
onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))

const scrollToSection = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' })
}

const navLinks = [
    { id: 'features', label: 'Features' },
    { id: 'how-it-works', label: 'How It Works' },
    { id: 'leaderboard', label: 'Leaderboard' },
    { id: 'announcements', label: 'Announcements' },
    { id: 'roles', label: 'Roles' },
]



const features = [
    { icon: CalendarDays, title: 'Scheduled Collections', description: 'Officials create and manage daily, weekly, or monthly waste collection schedules per purok.', bgClass: 'bg-rose-100 text-rose-600' },
    { icon: CheckCircle, title: 'Task Management', description: 'Personnel mark tasks as completed or missed with photo proof for full accountability.', bgClass: 'bg-amber-100 text-amber-600' },
    { icon: FileText, title: 'Incident Reporting', description: 'Residents report missed collections or illegal dumping with photos and GPS location.', bgClass: 'bg-blue-100 text-blue-600' },
    { icon: Star, title: 'Points & Incentives', description: 'Personnel manually award points to residents for proper segregation and participation.', bgClass: 'bg-emerald-100 text-emerald-600' },
    { icon: FileDown, title: 'PDF Reports', description: 'Officials generate exportable collection summaries, complaint reports, and participation rankings.', bgClass: 'bg-purple-100 text-purple-600' },
    { icon: ShieldCheck, title: 'OTP Authentication', description: 'Secure phone-based login using SMS one-time passwords via Semaphore PH API.', bgClass: 'bg-rose-100 text-rose-600' },
]

const steps = [
    { number: '01', title: 'Register', description: 'Sign up with your phone number and select your purok.' },
    { number: '02', title: 'Get Approved', description: 'A barangay official reviews and approves your account.' },
    { number: '03', title: 'Stay Updated', description: 'View your purok\'s collection schedule and submit reports.' },
    { number: '04', title: 'Earn Points', description: 'Get rewarded for proper waste segregation and timely disposal.' },
]

const roles = [
    {
        icon: Shield, title: 'Super Admin',
        abilities: ['Manage user accounts', 'Configure zones', 'View system activity logs'],
        iconClass: 'text-rose-600'
    },
    {
        icon: Building, title: 'Barangay Official',
        abilities: ['Create collection schedules', 'Respond to reports', 'Approve residents', 'Export PDF reports'],
        iconClass: 'text-amber-600'
    },
    {
        icon: Truck, title: 'Personnel',
        abilities: ['View assigned schedules', 'Update task status with photos', 'Award points to residents'],
        iconClass: 'text-blue-600'
    },
    {
        icon: HomeIcon, title: 'Resident',
        abilities: ['View collection schedules', 'Submit reports with photos', 'Track earned points'],
        iconClass: 'text-emerald-600'
    },
]
</script>

<style scoped>
/* Scoped styles intentionally left empty as animations moved to app.css */
</style>