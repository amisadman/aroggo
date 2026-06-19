<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-[#1f2b5b] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#3dbdec] hover:text-[#1f2b5b] focus:bg-[#1f2b5b] active:bg-[#1f2b5b] focus:outline-none focus:ring-2 focus:ring-[#3dbdec] focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
