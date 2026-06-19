<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div class="min-h-screen relative overflow-hidden flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-[#1f2b5b]/5 via-white to-[#3dbdec]/15">
        
        <!-- Floating Background Icons -->
        <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0">
            <!-- Tablet Icon 1 (Top Left) -->
            <div class="absolute left-10 top-20 text-[#3dbdec]/15 animate-float-slow">
                <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect x="5" y="9" width="14" height="6" rx="3" transform="rotate(-45 12 12)" />
                    <line x1="9" y1="15" x2="15" y2="9" />
                </svg>
            </div>
            
            <!-- Bottle Icon 1 (Top Right) -->
            <div class="absolute right-12 top-24 text-[#1f2b5b]/10 animate-float-medium">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6M7 8h10v11a2 2 0 01-2 2H9a2 2 0 01-2-2V8zm2-3v3m4-3v3M9 13h6" />
                </svg>
            </div>
            
            <!-- Injection Icon 1 (Bottom Left) -->
            <div class="absolute left-16 bottom-24 text-[#3dbdec]/15 animate-float-medium">
                <svg class="w-28 h-28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M10 6l8 8m-5.5-5.5l2.5 2.5m-5.5-2.5l5.5 5.5M6 18l-3 3m15-15l3-3M6.5 14.5l3 3" />
                </svg>
            </div>
            
            <!-- Circular Pill Icon (Bottom Right) -->
            <div class="absolute right-20 bottom-16 text-[#1f2b5b]/10 animate-float-slow">
                <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="8" />
                    <line x1="12" y1="4" x2="12" y2="20" />
                </svg>
            </div>

            <!-- Scattered small pills -->
            <div class="absolute left-1/3 top-10 text-[#3dbdec]/20 animate-pulse">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="8" cy="8" r="4" />
                    <circle cx="16" cy="16" r="4" />
                </svg>
            </div>
            <div class="absolute right-1/3 bottom-12 text-[#1f2b5b]/10">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="mb-6 flex flex-col items-center z-10">
            <Link href="/" class="flex flex-col items-center">
                <ApplicationLogo class="w-24 h-24 mb-2 hover:scale-105 transition-transform duration-250" />
                <span class="text-2xl font-extrabold text-[#1f2b5b] tracking-wider mt-1">
                    Aroggo <span class="text-[#3dbdec]">Pharmacy</span>
                </span>
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md px-8 py-8 bg-white border border-gray-100 shadow-xl overflow-hidden sm:rounded-2xl z-10"
        >
            <slot />
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-12px) rotate(3deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}
@keyframes float-reverse {
    0% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(12px) rotate(-5deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}
.animate-float-slow {
    animation: float 10s ease-in-out infinite;
}
.animate-float-medium {
    animation: float-reverse 8s ease-in-out infinite;
}
</style>
