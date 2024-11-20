<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";
import ProfileCreate from "@/Pages/Profile/Partials/CreateProfile.vue"; // Assuming your ProfileCreate component is in the Pages folder
</script>

<template>
    <GuestLayout>
        <Head title="Create Profile" />       
        <div>
            <ProfileCreate />
        </div>
        
        
    </GuestLayout>
</template>
