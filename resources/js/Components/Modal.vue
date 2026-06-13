<template>
    <Teleport to="body">
        <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to-class="opacity-100 translate-y-0 sm:scale-100" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0 sm:scale-100" leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div v-if="show" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-0 sm:px-4 pb-0 sm:pb-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="closeable ? $emit('close') : null"></div>
                
                <div
                    class="bg-white rounded-t-3xl sm:rounded-3xl rounded-b-none shadow-2xl w-full border border-gray-100 overflow-hidden max-h-[90vh] sm:max-h-[85vh] flex flex-col z-10 transform transition-all"
                    :class="maxWidthClass"
                >
                    <!-- Handle for mobile -->
                    <div class="w-full flex justify-center pt-3 pb-1 sm:hidden cursor-grab active:cursor-grabbing" @click="$emit('close')">
                        <div class="w-12 h-1.5 bg-gray-200 rounded-full"></div>
                    </div>

                    <!-- Header -->
                    <div v-if="title" class="flex items-center justify-between px-6 py-4 sm:py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
                        <button
                            v-if="closeable"
                            @click="$emit('close')"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-white border shadow-sm text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-all"
                        >
                            <component :is="X" class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5 overflow-y-auto">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3 pb-safe">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<script setup>
import { X } from '@lucide/vue';
import { computed } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: null,
    },
    maxWidth: {
        type: String,
        default: 'md',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
})

defineEmits(['close'])

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth]
})
</script>