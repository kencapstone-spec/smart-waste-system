<template>
    <AuthLayout page-title="Dashboard">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-sm text-gray-500 mb-1">Welcome back</p>
                <h2 class="text-xl font-semibold text-gray-800">{{ auth.user.name }}</h2>
                <p class="text-sm text-gray-400 capitalize mt-1">{{ auth.user.role.replace('_', ' ') }}</p>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const page = usePage()
const auth = computed(() => page.props.auth)
</script>