<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome to Aroggo Pharmacy" />

    <div class="min-h-screen bg-slate-50 text-gray-800 font-sans">
        <!-- Header Navbar -->
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-150 py-4 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <img src="/assets/aroggo_pharmacy.png" class="h-10 w-auto" alt="Aroggo Logo" />
                    <span class="text-xl font-extrabold text-[#1f2b5b] tracking-wider">
                        Aroggo <span class="text-[#3dbdec]">Pharmacy</span>
                    </span>
                </div>

                <!-- Navigation Links (Centered) -->
                <nav class="hidden md:flex space-x-8 text-sm font-bold text-gray-600">
                    <a href="#hero" class="hover:text-[#3dbdec] transition-colors">Home</a>
                    <a href="#features" class="hover:text-[#3dbdec] transition-colors">How It Works</a>
                    <a href="#reviews" class="hover:text-[#3dbdec] transition-colors">Reviews</a>
                    <a href="#contact" class="hover:text-[#3dbdec] transition-colors">Contact</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-5 py-2.5 bg-[#1f2b5b] hover:bg-[#3dbdec] hover:text-[#1f2b5b] text-white rounded-lg text-sm font-bold shadow-md transition-all"
                    >
                        Go to Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="px-4 py-2 text-sm font-bold text-[#1f2b5b] hover:text-[#3dbdec] transition-colors"
                        >
                            Sign In
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-5 py-2.5 bg-[#1f2b5b] hover:bg-[#3dbdec] hover:text-[#1f2b5b] text-white rounded-lg text-sm font-bold shadow-md transition-all"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="hero" class="relative overflow-hidden bg-gradient-to-br from-[#1f2b5b] via-[#1f2b5b]/95 to-[#3dbdec]/40 text-white py-20 lg:py-32 px-4 sm:px-6 lg:px-8">
            <!-- Background grids -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent opacity-40"></div>
            
            <div class="max-w-7xl mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-left">
                    <span class="inline-block px-3 py-1 bg-[#3dbdec]/20 border border-[#3dbdec]/30 text-[#3dbdec] text-xs font-bold uppercase tracking-wider rounded-full">
                        💊 Next-Gen Healthcare Platform
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight">
                        Your Trusted Healthcare Partner, <br/>
                        <span class="text-[#3dbdec]">Right at Your Fingertips</span>
                    </h1>
                    <p class="text-base sm:text-lg text-slate-200 max-w-xl">
                        Aroggo Pharmacy bridges the gap between you and your healthcare needs. Upload prescriptions, manage medical logs, track store racks, and receive automated purchase discounts effortlessly.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-2">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="route('register')"
                            class="px-8 py-3.5 bg-[#3dbdec] hover:bg-white text-[#1f2b5b] font-extrabold rounded-xl shadow-lg hover:shadow-xl transition-all"
                        >
                            Create Free Account
                        </Link>
                        <Link
                            v-else
                            :href="route('dashboard')"
                            class="px-8 py-3.5 bg-[#3dbdec] hover:bg-white text-[#1f2b5b] font-extrabold rounded-xl shadow-lg hover:shadow-xl transition-all"
                        >
                            Go to Portal Dashboard
                        </Link>
                        <a
                            href="#features"
                            class="px-8 py-3.5 bg-transparent border border-white/30 hover:border-white hover:bg-white/10 font-bold rounded-xl transition-all"
                        >
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5 flex justify-center">
                    <!-- Glassmorphism Card visual representation -->
                    <div class="w-full max-w-sm p-6 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20 shadow-2xl relative">
                        <div class="absolute -top-6 -left-6 w-20 h-20 bg-[#3dbdec]/30 rounded-full blur-xl"></div>
                        <div class="absolute -bottom-6 -right-6 w-20 h-20 bg-indigo-500/20 rounded-full blur-xl"></div>
                        
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-[#3dbdec] text-[#1f2b5b] flex items-center justify-center font-extrabold">✓</div>
                            <div>
                                <h4 class="font-bold text-sm">Pharmacy Seeding Active</h4>
                                <p class="text-[10px] text-slate-300">Database & Stocks Live</p>
                            </div>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="p-3 bg-white/5 rounded-xl border border-white/5 flex justify-between">
                                <span>Total Medicines</span>
                                <span class="font-bold text-[#3dbdec]">150+ Varieties</span>
                            </div>
                            <div class="p-3 bg-white/5 rounded-xl border border-white/5 flex justify-between">
                                <span>Prescription Auditing</span>
                                <span class="font-bold text-[#3dbdec]">Instant Approval</span>
                            </div>
                            <div class="p-3 bg-white/5 rounded-xl border border-white/5 flex justify-between">
                                <span>Discount Registry</span>
                                <span class="font-bold text-[#3dbdec]">Up to 100% Off</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto text-center space-y-12">
                <div class="space-y-4 max-w-2xl mx-auto">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1f2b5b]">
                        Seamless Roles. Beautiful Architecture.
                    </h2>
                    <p class="text-sm sm:text-base text-gray-500">
                        Aroggo Pharmacy brings together Customers, Data Entry Operators, and Admins under a unified system.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Customers -->
                    <div class="p-8 rounded-2xl border border-gray-100 hover:border-[#3dbdec]/30 hover:shadow-lg transition-all text-left bg-gradient-to-b from-white to-sky-50/10">
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center font-bold text-lg mb-6 shadow-sm">
                            👤
                        </div>
                        <h3 class="text-lg font-bold text-[#1f2b5b] mb-2">1. Upload & View</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Customers register profiles, upload prescription files, review past approved records, track purchases, and download cash-invoice receipts directly.
                        </p>
                    </div>

                    <!-- Operators -->
                    <div class="p-8 rounded-2xl border border-gray-100 hover:border-[#3dbdec]/30 hover:shadow-lg transition-all text-left bg-gradient-to-b from-white to-sky-50/10">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#3dbdec] flex items-center justify-center font-bold text-lg mb-6 shadow-sm">
                            ⚙️
                        </div>
                        <h3 class="text-lg font-bold text-[#1f2b5b] mb-2">2. Record & Sell</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Data Entry Operators manage the medicine directory, search physical storage locations, adjust stocks instantly, and log sales transactions for customers.
                        </p>
                    </div>

                    <!-- Admins -->
                    <div class="p-8 rounded-2xl border border-gray-100 hover:border-[#3dbdec]/30 hover:shadow-lg transition-all text-left bg-gradient-to-b from-white to-sky-50/10">
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg mb-6 shadow-sm">
                            🛡️
                        </div>
                        <h3 class="text-lg font-bold text-[#1f2b5b] mb-2">3. Oversee & Discount</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Admins hold destructive privileges, manage personnel, view expenditure leaderboards, and issue purchase discount percentages for customer next-orders.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customer Reviews Section -->
        <section id="reviews" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50">
            <div class="max-w-7xl mx-auto space-y-12 text-center">
                <div class="space-y-4 max-w-2xl mx-auto">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1f2b5b]">
                        What Our Customers Say
                    </h2>
                    <p class="text-sm sm:text-base text-gray-500">
                        Read verified testimonials from patients and administrators who trust Aroggo.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Review 1 -->
                    <div class="p-6 bg-white rounded-2xl border border-gray-150 shadow-sm text-left flex flex-col justify-between">
                        <p class="text-sm text-gray-600 italic leading-relaxed mb-6">
                            "Aroggo Pharmacy has made submitting my prescription incredibly straightforward. I just upload a photo from my phone and pick up my medicines. The next-purchase discount applied automatically too!"
                        </p>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-[#1f2b5b] text-white flex items-center justify-center font-bold">
                                JD
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-[#1f2b5b]">John Doe</h4>
                                <span class="text-xs text-gray-400">Regular Customer</span>
                            </div>
                        </div>
                    </div>

                    <!-- Review 2 -->
                    <div class="p-6 bg-white rounded-2xl border border-gray-150 shadow-sm text-left flex flex-col justify-between">
                        <p class="text-sm text-gray-600 italic leading-relaxed mb-6">
                            "Tracking stock categories and locations in our physical pharmacy became simple. Highlighting rack numbers prevents human delays, and instant stock updates via the dashboard are brilliant."
                        </p>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-[#3dbdec] text-[#1f2b5b] flex items-center justify-center font-bold">
                                SR
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-[#1f2b5b]">Sarah Rahman</h4>
                                <span class="text-xs text-gray-400">Data Entry Operator</span>
                            </div>
                        </div>
                    </div>

                    <!-- Review 3 -->
                    <div class="p-6 bg-white rounded-2xl border border-gray-150 shadow-sm text-left flex flex-col justify-between">
                        <p class="text-sm text-gray-600 italic leading-relaxed mb-6">
                            "The customer leaderboard allows our team to easily spot top accounts and assign rewards or loyalty discounts. The text invoice export download option is lightweight and incredibly fast."
                        </p>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center font-bold">
                                AH
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-[#1f2b5b]">Dr. Ahmed H.</h4>
                                <span class="text-xs text-gray-400">Pharmacy Director</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 px-4 sm:px-6 lg:px-8 bg-white border-t border-gray-100">
            <div class="max-w-4xl mx-auto text-center space-y-8">
                <h2 class="text-3xl font-extrabold text-[#1f2b5b]">Contact Aroggo Support</h2>
                <p class="text-gray-500 max-w-xl mx-auto">
                    Have questions about prescription approvals, data entry roles, or invoice downloads? Reach out to our 24/7 technical team.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md mx-auto text-sm text-left">
                    <div class="p-4 bg-slate-50 border rounded-xl flex items-center space-x-3">
                        <span class="text-lg">📧</span>
                        <div>
                            <span class="block font-bold text-gray-700">Email Us</span>
                            <a href="mailto:support@aroggo.com" class="text-[#3dbdec] font-medium hover:underline">support@aroggo.com</a>
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 border rounded-xl flex items-center space-x-3">
                        <span class="text-lg">📞</span>
                        <div>
                            <span class="block font-bold text-gray-700">Call Us</span>
                            <span class="text-[#1f2b5b] font-medium">+1 (555) 019-2834</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#1f2b5b] text-white py-12 px-4 sm:px-6 lg:px-8 border-t border-white/5">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 items-center text-center md:text-left">
                <!-- Branding -->
                <div class="flex flex-col items-center md:items-start space-y-3">
                    <div class="flex items-center space-x-2">
                        <img src="/assets/aroggo_pharmacy.png" class="h-10 w-auto bg-white/90 p-1.5 rounded-lg" alt="Aroggo Logo" />
                        <span class="text-xl font-extrabold tracking-wider">
                            Aroggo <span class="text-[#3dbdec]">Pharmacy</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">
                        Bridging health and technology since 2026.
                    </p>
                </div>

                <!-- Center Quick Links -->
                <div class="flex justify-center space-x-6 text-sm text-slate-300">
                    <a href="#hero" class="hover:text-white transition-colors">Home</a>
                    <a href="#features" class="hover:text-white transition-colors">How It Works</a>
                    <a href="#reviews" class="hover:text-white transition-colors">Reviews</a>
                    <a href="#contact" class="hover:text-white transition-colors">Support</a>
                </div>

                <!-- Copyright -->
                <div class="text-xs text-slate-400 md:text-right">
                    &copy; 2026 Aroggo Pharmacy. All rights reserved. <br/>
                    Built for superior health management systems.
                </div>
            </div>
        </footer>
    </div>
</template>
