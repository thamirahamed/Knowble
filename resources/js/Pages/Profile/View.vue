<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    components: { AuthenticatedLayout },
    props: {
        profile: Object,
        user: Object,
        course: Object,
        level : Object
    },
    computed: {
        profilePictureUrl() {
            return `/private-profile-picture/${this.profile.profile_pic}`;
        },
    },
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="profile-container">
            <div class="profile-content">
                <!-- Profile Section -->
                <div class="profile-section">
                    <img :src="profilePictureUrl" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover">
                    <a :href="route('profile.edit')" class="inline-block px-6 py-3 text-white bg-blue-500 hover:bg-blue-600 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                        Edit Profile
                    </a>
                    <h1 class="profile-name">{{ profile.cb_number }}</h1>
                    <p class="profile-email">{{ course.CourseName}}</p>
                    <p>{{ level.level}}</p>

                </div>
                <!-- User Information Section -->
                <div class="user-info-section">
                    <h1 class="info-title">User Information</h1>
                    <p class="info-detail">Username: {{ user.name }}</p>
                    <p class="info-detail">Email: {{ user.email }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
