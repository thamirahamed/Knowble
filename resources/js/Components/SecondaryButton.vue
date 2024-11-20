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
        class="rounded-lg bg-white border border-secondary px-5 py-2.5 text-center font-semibold tracking-wide text-primary transition-all duration-200 ease-in-out hover:bg-secondary hover:text-white hover:tracking-wider hover:shadow-[5px_5px_rgba(3,_32,_49,_0.4),_10px_10px_rgba(3,_32,_49,_0.3),_15px_15px_rgba(3,_32,_49,_0.2)] focus:outline-none focus:ring-2 focus:ring-primary"
    >
        <slot />
    </button>
</template>
