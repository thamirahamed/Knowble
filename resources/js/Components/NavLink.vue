<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? "inline-flex text-lg px-5 text-white items-center font-bold leading-5 bg-accent/80 relative z-0 overflow-hidden transition-all duration-200 after:absolute after:inset-0 after:-z-10 after:scale-[2.5] after:rounded-[100%] after:bg-gradient-to-l from-accentdark"
        : "inline-flex text-lg px-5 items-center leading-5 text-gray-500 hover:text-gray-800 hover:bg-primary/10",
// const classes = computed(() =>
//     props.active
//         ? "inline-flex text-lg items-center px-1 pt-1 font-bold leading-5 text-gray-900 underline decoration-accent decoration-4 underline-offset-4 transition duration-150 ease-in-out"
//         : "inline-flex text-lg items-center px-1 pt-1 leading-5 text-gray-500 hover:text-gray-700 hover:underline hover:decoration-2 hover:decoration-accent hover:underline-offset-4 transition duration-150 ease-in-out",
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
