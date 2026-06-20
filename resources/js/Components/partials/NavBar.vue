<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});
const emit = defineEmits(["update:modelValue"]);

const showNavigation = () => {
    if (props.modelValue == false) {
        emit("update:modelValue", true);
    } else {
        emit("update:modelValue", false);
    }
};
</script>
<template>
    <nav class="bg-white border-b border-gray-200 fixed z-30 w-full">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button
                        @click="showNavigation"
                        id="toggleSidebarMobile"
                        aria-expanded="true"
                        aria-controls="sidebar"
                        class="lg:hidden mr-2 text-gray-600 hover:text-gray-900 cursor-pointer p-2 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 rounded"
                    >
                        <svg
                            id="toggleSidebarMobileHamburger"
                            class="w-6 h-6"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                        <svg
                            id="toggleSidebarMobileClose"
                            class="w-6 h-6 hidden"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </button>
                    <Link
                        :href="route('dashboard')"
                        class="text-xl font-bold flex items-center lg:ml-2.5"
                    >
                        <img
                            src="/assets/aroggo_pharmacy.png"
                            class="h-8 mr-2 w-auto"
                            alt="Aroggo Pharmacy Logo"
                        />
                        <span class="self-center whitespace-nowrap text-xl font-extrabold text-[#1f2b5b]">
                            Aroggo <span class="text-[#3dbdec]">Pharmacy</span>
                        </span>
                    </Link>
                </div>
                <div class="flex items-center">
                    <div
                        class="flex items-center md:order-2 cursor-pointer"
                        @click="showNavigation"
                    >
                        <img
                            class="w-8 h-8 rounded-full"
                            src="../../../assets/user.png"
                            alt="user photo"
                        />
                        <p class="text-gray-800 font-semibold px-2">
                            {{ $page.props.auth.user.name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
