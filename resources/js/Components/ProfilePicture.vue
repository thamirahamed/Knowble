<script setup>
import { computed } from "vue";
// Props
const props = defineProps({
    profile: Object
});

// Computed property for the profile picture URL
const profilePictureUrl = computed(() => {
    return `/private-profile-picture/${props.profile.profile_pic}`;
});
</script>

<template>
    <img :src="profilePictureUrl" alt="Profile Picture" class="rounded-full object-cover">
</template>


