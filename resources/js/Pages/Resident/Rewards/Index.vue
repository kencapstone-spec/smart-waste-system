<template>
    <AuthLayout page-title="Rewards Catalog">
        
        <!-- Header -->
        <div class="relative bg-gradient-to-br from-rose-950 via-rose-900 to-red-900 rounded-3xl p-8 text-white mb-8 shadow-2xl shadow-rose-900/20 overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-rose-200/80 font-medium tracking-wide text-sm mb-2 uppercase">My Available Points</h2>
                    <div class="text-5xl font-extrabold flex items-center gap-3">
                        <span class="animate-bounce">⭐</span> {{ availablePoints }}
                    </div>
                </div>
                <div class="bg-white/10 rounded-2xl px-5 py-3 text-sm font-medium backdrop-blur-md border border-white/10">
                    Redeem rewards using your points!
                </div>
            </div>
        </div>

        <!-- Rewards Grid -->
        <div class="mb-12">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Available Rewards</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="reward in rewards" :key="reward.id" class="bg-white rounded-3xl shadow-[0_4px_24px_rgba(225,29,72,0.04)] border border-gray-100 overflow-hidden hover:shadow-[0_8px_32px_rgba(225,29,72,0.08)] transition-all duration-300 group hover:-translate-y-1 flex flex-col">
                    <div class="h-40 bg-gradient-to-br from-rose-50 to-gray-50 flex items-center justify-center text-5xl relative overflow-hidden">
                        <div class="absolute inset-0 bg-rose-900/5 group-hover:bg-rose-900/0 transition-colors"></div>
                        <span class="transform group-hover:scale-110 transition-transform duration-500">🎁</span>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h4 class="font-extrabold text-gray-900 text-lg leading-tight">{{ reward.name }}</h4>
                        <p class="text-sm text-gray-500 mt-2 line-clamp-2 flex-1">{{ reward.description || 'No description available.' }}</p>
                        
                        <div class="mt-6 p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">⭐</span>
                                <span class="font-extrabold text-gray-900 text-lg">{{ reward.points_required }}</span>
                            </div>
                            <span class="text-xs font-semibold px-3 py-1 rounded-full" :class="reward.stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'">
                                {{ reward.stock > 0 ? `${reward.stock} in stock` : 'Out of stock' }}
                            </span>
                        </div>

                        <button 
                            @click="redeem(reward)"
                            :disabled="availablePoints < reward.points_required || reward.stock <= 0"
                            class="w-full py-3.5 rounded-xl text-sm font-bold transition-all duration-300 flex justify-center items-center gap-2"
                            :class="availablePoints >= reward.points_required && reward.stock > 0 
                                ? 'bg-rose-900 text-white hover:bg-rose-800 shadow-md shadow-rose-900/20' 
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        >
                            {{ availablePoints < reward.points_required ? 'Not Enough Points' : 'Redeem Reward' }}
                        </button>
                    </div>
                </div>

                <div v-if="rewards.length === 0" class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-gray-200">
                    <div class="text-4xl mb-4 opacity-50">🏪</div>
                    <p class="text-gray-500 font-medium">No rewards available at the moment.</p>
                </div>
            </div>
        </div>

        <!-- Redemption History -->
        <h3 class="text-xl font-bold text-gray-900 mb-6">Redemption History</h3>
        
        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-3xl shadow-[0_4px_24px_rgba(225,29,72,0.04)] border border-gray-100 overflow-hidden mb-8">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Date</th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Reward</th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Points Spent</th>
                        <th class="text-left px-8 py-4 text-gray-500 font-semibold uppercase tracking-wider text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="redemption in redemptions" :key="redemption.id" class="hover:bg-rose-50/30 transition-colors">
                        <td class="px-8 py-5 text-gray-600 font-medium">{{ new Date(redemption.created_at).toLocaleDateString() }}</td>
                        <td class="px-8 py-5 text-gray-900 font-bold">{{ redemption.reward.name }}</td>
                        <td class="px-8 py-5 font-extrabold text-amber-500">-{{ redemption.points_spent }} ⭐</td>
                        <td class="px-8 py-5">
                            <span :class="{
                                'bg-amber-100 text-amber-700': redemption.status === 'pending',
                                'bg-emerald-100 text-emerald-700': redemption.status === 'approved',
                                'bg-rose-100 text-rose-700': redemption.status === 'rejected'
                            }" class="px-3 py-1.5 rounded-lg text-xs font-bold capitalize">
                                {{ redemption.status }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="redemptions.length === 0">
                        <td colspan="4" class="px-8 py-12 text-center text-gray-400">You haven't redeemed any rewards yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards for History -->
        <div class="md:hidden space-y-4 mb-8">
            <div v-for="redemption in redemptions" :key="redemption.id" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col gap-3">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs text-gray-500 font-medium mb-1">{{ new Date(redemption.created_at).toLocaleDateString() }}</p>
                        <p class="font-bold text-gray-900 text-base">{{ redemption.reward.name }}</p>
                    </div>
                    <span :class="{
                        'bg-amber-100 text-amber-700': redemption.status === 'pending',
                        'bg-emerald-100 text-emerald-700': redemption.status === 'approved',
                        'bg-rose-100 text-rose-700': redemption.status === 'rejected'
                    }" class="px-3 py-1 rounded-lg text-[10px] font-bold capitalize">
                        {{ redemption.status }}
                    </span>
                </div>
                <div class="pt-3 border-t border-gray-50 flex justify-end">
                    <span class="font-extrabold text-amber-500 text-sm">-{{ redemption.points_spent }} ⭐</span>
                </div>
            </div>
            <div v-if="redemptions.length === 0" class="py-8 text-center text-gray-400 bg-white rounded-2xl border border-dashed border-gray-200">
                You haven't redeemed any rewards yet.
            </div>
        </div>

        <!-- Confirm Redemption Modal -->
        <Modal :show="showConfirmModal" title="Confirm Redemption" max-width="md" @close="closeModal">
            <div class="p-2">
                <div class="mb-6 text-gray-600 text-center text-lg">
                    Redeem <strong>"{{ selectedReward?.name }}"</strong>?
                </div>
                
                <div class="bg-gradient-to-br from-rose-50 to-white border border-rose-100 rounded-2xl p-6 flex flex-col items-center justify-center mb-8 shadow-inner">
                    <span class="text-sm text-gray-500 font-semibold mb-2 uppercase tracking-wider">Points Required</span>
                    <span class="font-extrabold text-4xl text-gray-900 flex items-center gap-2">
                        ⭐ {{ selectedReward?.points_required }}
                    </span>
                </div>

                <p class="text-sm text-gray-500 mb-8 text-center px-4 leading-relaxed">
                    This will deduct {{ selectedReward?.points_required }} points from your balance. The reward will be marked as pending until approved by an official.
                </p>

                <div class="flex flex-col gap-3">
                    <button @click="confirmRedeem" class="w-full bg-rose-900 text-white px-6 py-4 rounded-xl text-sm font-bold hover:bg-rose-800 transition-all duration-300 shadow-lg shadow-rose-900/20">
                        Confirm Redemption
                    </button>
                    <button @click="closeModal" class="w-full px-6 py-4 text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all font-bold">
                        Cancel
                    </button>
                </div>
            </div>
        </Modal>

    </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    rewards: Array,
    redemptions: Array,
    availablePoints: Number,
})

const showConfirmModal = ref(false)
const selectedReward = ref(null)

const redeem = (reward) => {
    selectedReward.value = reward
    showConfirmModal.value = true
}

const closeModal = () => {
    showConfirmModal.value = false
    selectedReward.value = null
}

const confirmRedeem = () => {
    if (selectedReward.value) {
        router.post(route('resident.rewards.redeem', selectedReward.value.id), {}, { 
            preserveScroll: true,
            onSuccess: () => closeModal()
        })
    }
}
</script>
