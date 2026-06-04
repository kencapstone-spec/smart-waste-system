<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <h1 class="text-2xl font-semibold text-gray-800 mb-2">Welcome, {{ auth.user.name }}</h1>
            <p class="text-gray-500 text-sm mb-6">Role: {{ auth.user.role }}</p>
            <form @submit.prevent="logout">
                <button
                    type="submit"
                    class="bg-red-500 text-white px-6 py-2 rounded-md text-sm hover:bg-red-600 transition"
                >
                    Logout
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { usePage, useForm } from '@inertiajs/vue3'

const { auth } = usePage().props

const form = useForm({})

const logout = () => {
    form.post(route('logout'))
}
</script>