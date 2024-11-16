<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import ProfileCreate from "@/Pages/Profile/Partials/CreateProfile.vue"; // Assuming your ProfileCreate component is in the Pages folder
</script>

<template>
    <GuestLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Your Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <ProfileCreate />
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
