<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import ProfilePicture from "@/Components/ProfilePicture.vue";

// Props
const props = defineProps({
    profile: Object,
    user: Object,
    course: Object,
    level: Object,
    tutor: [String, null],
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="flex justify-center">
            <div class="flex flex-col md:flex-row max-w-7xl w-full bg-white rounded-md mt-4 shadow" >
                <!-- Profile Section -->
                <div class="flex py-8 px-8 justify-center w-full md:w-fit">
                    <ProfilePicture 
                        :profile="profile" 
                        class="w-80 shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]" 
                    />
                </div>
                <!-- User Information Section -->
                <div class="flex flex-col flex-1 py-10 px-8 justify-between ">
                    <div>
                        <p class="text-6xl font-subh font-black tracking-wide mb-2">{{ user.name }}</p>
                        <p class="text-4xl font-subh font-semibold text-gray-700 mb-1">{{ profile.cb_number.toUpperCase() }}</p>
                        <p class="text-3xl font-subh text-gray-700 mb-1">{{ course.CourseName}}</p>
                        <p class="text-3xl font-subh text-gray-700 mb-1">{{ level.level}}</p>
                    </div>
                    <div class="flex justify-end">
                        <a 
                            :href="route('profile.edit')" 
                            class="rounded-lg bg-accent px-5 py-2.5 text-center font-semibold tracking-wide text-white transition-all duration-200 ease-in-out hover:bg-accentdark hover:tracking-wider hover:shadow-[5px_5px_rgba(0,_98,_90,_0.4),_10px_10px_rgba(0,_98,_90,_0.3),_15px_15px_rgba(0,_98,_90,_0.2)] focus:outline-none focus:ring-2 focus:ring-accent"
                        >
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>