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
        class="rounded-lg bg-secondary opacity-75 border border-secondary px-5 py-2.5 text-center font-semibold tracking-wide text-white transition-all duration-200 ease-in-out hover:bg-white hover:text-black hover:tracking-wider hover:shadow-[5px_5px_rgba(3,_32,_49,_0.4),_10px_10px_rgba(3,_32,_49,_0.3)] focus:outline-none focus:ring-2 focus:ring-primary"
    >
        <slot />
    </button>
</template>
