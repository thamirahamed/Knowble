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
        ? "flex font-medium w-full align-left py-3 px-4 text-white bg-accent/80 relative z-0 overflow-hidden transition-all duration-200 after:absolute after:inset-0 after:-z-10 after:scale-[2.5] after:rounded-[100%] after:bg-gradient-to-l from-accentdark hover:bg-accent"
        : "flex w-full align-left py-3 px-4 hover:bg-primary/15 text-gray-600 hover:text-gray-900",
);
</script>

<template>
    <Link :href="href" :class="classes" preserve-state>
        <slot />
    </Link>
</template>