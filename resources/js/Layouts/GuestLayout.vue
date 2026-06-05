<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-rose-950 via-rose-900 to-red-900 relative overflow-hidden">
        <!-- Animated mesh pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-full h-full"
                style="background-image: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.15) 1px, transparent 1px), radial-gradient(circle at 75% 75%, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;">
            </div>
        </div>

        <!-- Floating orbs -->
        <div class="absolute top-20 right-20 w-96 h-96 bg-rose-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-72 h-72 bg-red-400/15 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 w-full flex justify-center px-4 lg:px-8">
            <slot />
        </div>
    </div>
</template>