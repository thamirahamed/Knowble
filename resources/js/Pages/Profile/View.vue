<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
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
            <div class="flex container w-full bg-white rounded-md mt-4 shadow-[5px_5px_0px_0px_rgba(27,143,103,.6)]" >
                <!-- Profile Section -->
                <div class="flex w-1/4 p-10">
                    <ProfilePicture :profile="profile" class="w-full h-auto shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]" />
                </div>
                <!-- User Information Section -->
                <div class="relative flex flex-col w-3/4 py-10 px-4">
                    <p class="text-8xl font-extrabold tracking-wide mb-2">{{ user.name }}</p>
                    <p class="text-4xl font-subh font-semibold text-gray-700 mb-1">{{ profile.cb_number.toUpperCase() }}</p>
                    <p class="text-3xl font-subh text-gray-700 mb-1">{{ course.CourseName}}</p>
                    <p class="text-2xl font-subh text-gray-700 mb-1">{{ level.level}}</p>

                    <PrimaryButton class="absolute w-fit bottom-10 right-10 text-lg">
                        <a :href="route('profile.edit')">
                            Edit Profile
                        </a>
                    </PrimaryButton>

                    <!-- Tutor Status Button -->
                    <template v-if="tutor === null">
                        <PrimaryButton class="absolute w-fit bottom-10 right-40 text-lg mr-10">
                            <a :href="route('admin.tutor.request')">
                                Request to be a Tutor
                            </a>
                        </PrimaryButton>
                    </template>

                    <template v-else-if="tutor === 'pending'">
                        <PrimaryButton class="absolute w-fit bottom-10 right-40 text-lg mr-10" disabled>
                            Request Sent
                        </PrimaryButton>
                    </template>

                    <template v-else-if="tutor === 'accepted'">
                        <PrimaryButton class="absolute w-fit bottom-10 right-40 text-lg mr-10">
                            <a :href="route('admin.dashboard')">
                                Go to Dashboard
                            </a>
                        </PrimaryButton>
                    </template>

                    <template v-else-if="tutor === 'rejected'">
                        <PrimaryButton class="absolute w-fit bottom-10 right-40 text-lg mr-10" disabled>
                            Request Rejected
                        </PrimaryButton>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
