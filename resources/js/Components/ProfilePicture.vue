<script setup>
import { computed } from "vue";
// Props
const props = defineProps({
    profile: Object
});

// Computed property for the profile picture URL
const profilePictureUrl = computed(() => {
    return `${props.profile.profile_pic}`;
});
</script>

<template>
    <img :src="profilePictureUrl" alt="Profile Picture" class="aspect-square rounded-full object-cover">
</template>


