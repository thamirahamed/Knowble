<script setup>
import { defineProps } from 'vue';
import ProfilePicture from "@/Components/ProfilePicture.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { PencilIcon, CheckBadgeIcon, AcademicCapIcon, CalendarDateRangeIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    tutor: Array,
    profile: Array,
    school: Array,
    modules: Array,
    degree: Array,
    level: Array,
    availableTime: Array,
    user: Array,
    sessions: Array,
});
console.log(props.sessions);
</script>
<template>
    <AuthenticatedLayout>
    <!-- Main Container -->
    <div class="flex justify-center bg-gray-100  p-6">
        <div class="flex lg:max-w-7xl w-full gap-8">
            <!-- Profile Info Section -->
            <div class="flex flex-col max-w-xs w-full bg-white rounded-md mt-8 shadow">
                <!-- Profile Pic Section -->
                <div class="flex py-8 px-8 justify-center">
                    <ProfilePicture
                        :profile="profile"
                        class="w-full h-auto shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                    />
                </div>

                <!-- User Information Section -->
                <div class="flex flex-col flex-1 pb-6 px-6">
                    <div class="border-t border-gray-300 py-3">
                        <h1 class="text-2xl font-bold text-slate-900 flex items-center">
                            <template v-if="tutor === 'approved'">
                                {{ user.name }}
                                <CheckBadgeIcon class="ml-2 w-6 text-green-500" />
                            </template>
                            <template v-else>
                                {{ user.name }}
                            </template>
                        </h1>
                        <p class="text-lg font-medium text-gray-600">{{ profile.cb_number }}</p>
                    </div>
                    <div class="pt-2">
                        <p class="text-gray-700">{{ degree.degree_name }}</p>
                        <p class="text-gray-700">{{ level.level_name }}</p>
                    </div>
                    <div v-if="sessions">
                        <!-- Request TutorSession Button -->
                        <div v-if="sessions.status === null" class="flex justify-evenly">
                            <a :href="route('tutor.session.request', tutor.id)" class="bg-accent text-white px-4 py-1 rounded-md font-semibold">Request Session</a>
                        </div>
                        <div v-if="sessions.status === 'pending'" class="flex justify-evenly">
                            <a href="#" class="bg-red-800 text-white px-4 py-1 rounded-md font-semibold" aria-disabled="true">Session Requested</a>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Tutor Details -->
            <div v-if="tutor.status === 'approved'" class="flex flex-col flex-1 bg-white rounded-md shadow-md mt-8 p-6">
                <h2 class="text-2xl font-bold mb-4">Tutor Details</h2>
                <p class="text-gray-600 mb-6">
                    View the tutor's modules and availability to easily find when and what they teach.
                </p>

                <div class="flex gap-12">
                    <!-- Selected Modules -->
                    <div v-if="modules && modules.length > 0" class="flex-1">
                        <h3 class="text-lg font-semibold mb-2">Modules</h3>
                        <ul>
                            <li v-for="module in modules" :key="module.id" class="flex items-center px-4 py-2 even:bg-gray-100 text-gray-800">
                                <AcademicCapIcon class="mr-2 w-5 text-gray-500" />
                                {{ module.module_name }}
                            </li>
                        </ul>
                    </div>

                    <!-- Available Time -->
                    <div v-if="availableTime && availableTime.length > 0" class="mt-4 flex flex-col flex-1">
                        <h3 class="text-lg font-semibold mb-2">Tutor Availability</h3>
                        <ul>
                            <li v-for="time in availableTime" :key="time.id" class="flex justify-between even:bg-accent/5 px-4 py-2 text-gray-800">
                                <div class="flex items-center">
                                    <CalendarDateRangeIcon class=" w-5 text-gray-500" />
                                    {{ time.day }}
                                </div>
                                <div class="flex items-center">
                                    {{ time.start_time }} - {{ time.end_time }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </AuthenticatedLayout>
</template>
