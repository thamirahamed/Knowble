<script setup>

import AdminLayout from "@/Layouts/AdminLayout.vue";
import { AcademicCapIcon, CalendarDateRangeIcon, CheckBadgeIcon, } from "@heroicons/vue/24/solid/index.js";
import ProfilePicture from "@/Components/ProfilePicture.vue";

const props = defineProps({
    user: Object ,
    profile: Object,
    tutor: Object,
    sessions: Object,
    peerGroup: Object,
    chat: Object,
    degree: Object,
    level: Object,
    semester: Object,
    isTutor: Boolean,
});

const formatDateToWords = (date) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const d = new Date(date);
    const day = d.getDate();
    const suffix = getDaySuffix(day);

    const formatter = new Intl.DateTimeFormat('en-US', options);
    return `${formatter.format(d).replace(day, day + suffix)}`;
};

// Function to get the day suffix (st, nd, rd, th)
const getDaySuffix = (day) => {
    const j = day % 10;
    const k = day % 100;
    if (j === 1 && k !== 11) {
        return 'st';
    }
    if (j === 2 && k !== 12) {
        return 'nd';
    }
    if (j === 3 && k !== 13) {
        return 'rd';
    }
    return 'th';
};

</script>

<template>
    <AdminLayout>
        <div class="ml-12 flex justify-evenly">
            <!-- User Information Card -->
            <div class="flex flex-col max-w-xs w-full bg-white rounded-md mt-8 shadow h-fit">
                <!-- Profile Pic Section -->
                <div class="flex py-8 px-8 justify-center">
                    <ProfilePicture
                        :profile="profile"
                        class="w-full h-auto shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                    />
                </div>

                <!-- User Information Section -->
                <div class="flex flex-col flex-1 pb-5 px-6">
                    <div class="border-y border-gray-300 py-3 px-1">
                        <h1 class="flex text-2xl font-extrabold tracking-wide text-slate-900">
                            {{ user.name }}
                            <CheckBadgeIcon class="ml-1 w-6 text-accent" />
                        </h1>
                        <p class="text-xl font-medium text-gray-700">{{ profile.cb_number }}</p>
                    </div>
                    <div class="p-1">
                        <p class="text-lg text-gray-700">{{ degree.degree_name }}</p>
                        <p class="text-lg text-gray-700">{{ level.level_name }} | {{ semester.semester_name }}</p>
                    </div>
                </div>
            </div>
            <!-- Details Side -->
            <div class="flex flex-col bg-white rounded-md mt-8 shadow h-fit w-3/4 mx-auto">
                <!-- Tutor Information Section -->
                    <div v-if="props.isTutor === true">
                        <!-- Tutor Information Section -->
                        <div class="flex flex-col flex-1 bg-white rounded-md mt-8 shadow h-fit py-4 px-6">
                            <h2 class="text-2xl font-bold">Tutor Details</h2>
                            <p class="text-gray-700">
                                View the tutor's modules and availability to easily find when and what they teach.
                            </p>

                            <div class="flex w-full gap-12">
                                <!-- Selected Modules -->
                                <div v-if="tutor.tuorselectedmodules && tutor.tuorselectedmodules.length > 0" class="mt-4 flex flex-col flex-1">
                                    <h3 class="text-lg font-semibold mb-2">Modules</h3>
                                    <ul>
                                        <li v-for="module in tutor.tuorselectedmodules" :key="module.id" class="flex items-center even:bg-accent/5 px-4 py-2 text-gray-800">
                                            <AcademicCapIcon class="mr-2 w-4 h-4 text-gray-700" />
                                            {{ module.module_name }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="mt-4 flex flex-col flex-1">
                                    <h3 class="text-lg font-medium mb-2">Modules</h3>
                                    <p class="mt-2 text-gray-600 h-full">No modules available.</p>
                                </div>

<!--                                <div v-else class="mt-4 flex flex-col flex-1">-->
<!--                                    <h3 class="text-lg font-medium mb-2">Tutor Availability</h3>-->
<!--                                    <p class="mt-2 text-gray-600 h-full">No sessions available.</p>-->
<!--                                </div>-->
                            </div>
                        </div>

                    </div>

                <!-- Session Information Section -->
                <div class="flex flex-col flex-1 py-5 px-6">
                    <div class="border-b border-gray-300 py-3 px-1">
                        <h1 class="text-2xl font-extrabold tracking-wide text-slate-900">Session Information</h1>
                    </div>
                    <div class="p-1">
                        <div v-if="props.isTutor === true" class="mt-4 flex flex-col flex-1">
                            <h3 class="text-lg font-medium mb-2">Session As Tutor</h3>
                            <ul>
                                <li
                                    v-for="session in tutor.tutorsession"
                                    :key="session.id"
                                    class="flex justify-between even:bg-accent/5 px-4 py-2 text-gray-800 "
                                >
                                    <div class="flex items-center">
                                        <CalendarDateRangeIcon class="mr-2 w-4 h-4 text-gray-700" />  <p>{{ formatDateToWords(session.session_date)}}</p>

                                    </div>
                                    <div class="flex items-center">
                                        {{ session.start_time }} - {{ session.end_time }}
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="mt-4 flex flex-col flex-1">
                            <h3 class="text-lg font-medium mb-2">Session As Student</h3>
                            <ul>
                                <li
                                    v-for="session in sessions"
                                    :key="session.id"
                                    class="flex justify-between even:bg-accent/5 px-4 py-2 text-gray-800 "
                                >
                                    <div class="flex items
                                    -center">
                                        <CalendarDateRangeIcon class="mr-2 w-4 h-4 text-gray-700" />  <p>{{ formatDateToWords(session.session_date)}}</p>
                                    </div>
                                    <div class="flex items center">
                                        {{ session.start_time }} - {{ session.end_time }}
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Peer Group Information Section -->
                <div class="flex flex-col flex-1 py-5 px-6">
                    <div class="border-b border-gray-300 py-3 px-1">
                        <h1 class="text-2xl font-extrabold tracking-wide text-slate-900">Peer Group Information</h1>
                    </div>
                    <div class="p-1">
                        <div v-if="peerGroup.peerGroup && peerGroup.peerGroup.length > 0" class="mt-4 flex flex-col flex-1">
                            <h3 class="text-lg font-medium mb-2">Peer Group</h3>
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left px-4 py-2">Name</th>
                                        <th class="text-left px-4 py-2">Created At</th>
                                        <th class="text-left px-4 py-2">Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="peer in peerGroup.peerGroup" :key="peer.id">
                                        <td class="px-4 py-2">{{ peer.name }}</td>
                                        <td class="px-4 py-2">{{ formatDateToWords(peer.created_at) }}</td>
                                        <td class="px-4 py-2">Leader</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left px-4 py-2">Name</th>
                                        <th class="text-left px-4 py-2">Created At</th>
                                        <th class="text-left px-4 py-2">Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="peer in peerGroup.peergroupmember" :key="peer.id">
                                        <td class="px-4 py-2">{{ peer.group.name }}</td>
                                        <td class="px-4 py-2">{{ formatDateToWords(peer.created_at) }}</td>
                                        <td class="px-4 py-2">Member</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Chat Information Section -->
                <div class="flex flex-col flex-1 py-5 px-6">
                    <div class="border-b border-gray-300 py-3 px-1">
                        <h1 class="text-2xl font-extrabold tracking-wide text-slate-900">Chat Information</h1>
                    </div>
                    <div v-for="chats in chat" class="p-1 flex justify-between text-left">
                        <p class="text-lg text-gray-700">{{ chats.user.name }}</p>
                        <p class="text-lg text-gray-700">{{ chats.user.email }}</p>
                    </div>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>
