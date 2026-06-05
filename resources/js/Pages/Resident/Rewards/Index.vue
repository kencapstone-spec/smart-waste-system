<template>
    <AuthLayout page-title="Rewards">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl p-6 text-white mb-8 shadow-md flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold opacity-90">My Available Points</h2>
                <div class="text-4xl font-extrabold mt-1 flex items-center gap-2">
                    <span class="text-yellow-300">⭐</span> {{ availablePoints }}
                </div>
            </div>
            <div class="bg-white/20 rounded-lg px-4 py-2 text-sm font-medium backdrop-blur-sm">
                Redeem rewards using your points!
            </div>
        </div>

        <!-- Rewards Grid -->
        <h3 class="text-lg font-bold text-gray-800 mb-4">Available Rewards</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div v-for="reward in rewards" :key="reward.id" class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-md transition">
                <div class="h-32 bg-gray-100 flex items-center justify-center text-4xl">
                    🎁
                </div>
                <div class="p-5">
                    <h4 class="font-bold text-gray-800 text-lg">{{ reward.name }}</h4>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2 h-10">{{ reward.description || 'No description available.' }}</p>
                    
                    <div class="mt-4 flex items-center justify-between">
                        <span class="font-bold text-yellow-600 text-lg">{{ reward.points_required }} ⭐</span>
                        <span class="text-xs text-gray-500">{{ reward.stock }} in stock</span>
                    </div>

                    <button 
                        @click="redeem(reward)"
                        :disabled="availablePoints < reward.points_required || reward.stock <= 0"
                        class="mt-4 w-full py-2 rounded-lg text-sm font-medium transition"
                        :class="availablePoints >= reward.points_required && reward.stock > 0 
                            ? 'bg-green-600 text-white hover:bg-green-700' 
                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                    >
                        {{ availablePoints < reward.points_required ? 'Not Enough Points' : 'Redeem Reward' }}
                    </button>
                </div>
            </div>

            <div v-if="rewards.length === 0" class="col-span-3 py-12 text-center text-gray-500 bg-white rounded-xl border border-dashed">
                No rewards available at the moment.
            </div>
        </div>

        <!-- Redemption History -->
        <h3 class="text-lg font-bold text-gray-800 mb-4">Redemption History</h3>
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Date</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Reward</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Points Spent</th>
                        <th class="text-left px-6 py-3 text-gray-600 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="redemption in redemptions" :key="redemption.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-600">{{ new Date(redemption.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ redemption.reward.name }}</td>
                        <td class="px-6 py-4 font-bold text-yellow-600">-{{ redemption.points_spent }} ⭐</td>
                        <td class="px-6 py-4">
                            <span :class="{
                                'bg-yellow-100 text-yellow-700': redemption.status === 'pending',
                                'bg-green-100 text-green-700': redemption.status === 'approved',
                                'bg-red-100 text-red-700': redemption.status === 'rejected'
                            }" class="px-2 py-1 rounded-full text-xs font-medium capitalize">
                                {{ redemption.status }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="redemptions.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">You haven't redeemed any rewards yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Confirm Redemption Modal -->
        <Modal :show="showConfirmModal" title="Confirm Redemption" max-width="md" @close="closeModal">
            <div class="p-6">
                <div class="mb-4 text-gray-700">
                    Are you sure you want to redeem <strong>"{{ selectedReward?.name }}"</strong>?
                </div>
                <div class="bg-gray-50 border rounded-lg p-4 flex justify-between items-center mb-6">
                    <span class="text-sm text-gray-500">Points Required:</span>
                    <span class="font-bold text-yellow-600">{{ selectedReward?.points_required }} ⭐</span>
                </div>
                <p class="text-sm text-gray-500 mb-6">
                    This action will deduct {{ selectedReward?.points_required }} points from your balance. The reward will be marked as pending until approved.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="closeModal" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition font-medium">Cancel</button>
                    <button @click="confirmRedeem" class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-sm">Confirm</button>
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
