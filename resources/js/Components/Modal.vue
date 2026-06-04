<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
            <div
                class="bg-white rounded-lg shadow-xl w-full"
                :class="maxWidthClass"
            >
                <!-- Header -->
                <div v-if="title" class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
                    <button
                        v-if="closeable"
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        ✕
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-4">
                    <slot />
                </div>

                <!-- Footer -->
                <div v-if="$slots.footer" class="px-6 py-4 border-t flex justify-end gap-3">
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