<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center z-50 px-4">
            <div
                class="bg-white rounded-3xl shadow-2xl w-full border border-gray-100 overflow-hidden"
                :class="maxWidthClass"
            >
                <!-- Header -->
                <div v-if="title" class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
                    <button
                        v-if="closeable"
                        @click="$emit('close')"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white border shadow-sm text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-all"
                    >
                        ✕
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-5">
                    <slot />
                </div>

                <!-- Footer -->
                <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
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
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg',
        xl: 'max-w-xl',
        '2xl': 'max-w-2xl',
    }[props.maxWidth]
})
</script>