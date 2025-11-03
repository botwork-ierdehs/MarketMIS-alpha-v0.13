<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary focus:bg-primary active:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
