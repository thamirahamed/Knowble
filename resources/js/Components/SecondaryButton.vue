<script setup>
defineProps({
    type: {
        type: String,
        default: "button",
    },
});
</script>

<template>
    <button
        :type="type"
        class="rounded-lg bg-transparent px-5 py-2.5 text-center font-semibold tracking-wide text-slate-900 hover:bg-primary hover:text-white relative z-0 overflow-hidden transition-all duration-200"
    >
        <slot />
    </button>
</template>
