<script setup>
import { defineProps, ref, onMounted, computed } from "vue";
import ProfilePicture from "@/Components/ProfilePicture.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { CheckBadgeIcon, AcademicCapIcon, CalendarDateRangeIcon, UserGroupIcon, UserIcon } from "@heroicons/vue/24/solid";
import BookSession from "@/Components/BookSession.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head } from "@inertiajs/vue3";
import BookGroupSession from "@/Components/BookGroupSession.vue";

const props = defineProps({
    tutor: Array,
    profile: Array,
    school: Array,
    tutormodules: Array,
    degree: Array,
    level: Array,
    semester: Array,
    user: Array,
    sessions: [Array, null],
    commonModules: [Array, null],
    studentModules: Array,
    isLeader: Array,
    peerGroups: Array,
});

console.log(JSON.stringify(props.peerGroups, null, 2));

const openModal = ref(null);

const closeModal = () => {
    openModal.value = null;
};

const openModalWithData = () => {
    openModal.value = true;
};

const openModal1 = ref(null);

const closeModal1 = () => {
    openModal1.value = null;
};

const openModalWithData1 = () => {
    openModal1.value = true;
};

// Function to format date to words with suffix
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
    <Head :title='user.name' />

    <AuthenticatedLayout>
        <!-- Main Container -->
        <div class="flex justify-center">
            <div class="flex lg:max-w-7xl w-full gap-8">
                <!-- Profile Info Section -->
                <div class="flex flex-col max-w-xs w-full bg-white rounded-md mt-8 shadow h-fit">
                    <!-- Profile Pic Section -->
                    <div class="flex py-8 px-8 justify-center">
                        <ProfilePicture
                            :profile="profile"
                            class="w-full h-auto shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        />
                    </div>
                    <BookSession
                        :openModal="openModal"
                        :tutorid="tutor.id"
                        :closeModal="closeModal"
                        :commonModules="commonModules"
                        :sessionSlots="sessions"
                    />
                    <BookGroupSession
                        :openModal="openModal1"
                        :tutorid="tutor.id"
                        :closeModal="closeModal1"
                        :peerGroups="peerGroups"
                        :sessionSlots="sessions"
                    />
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

                        <PrimaryButton 
                            v-if="sessions.length > 0 && tutormodules.length > 0"
                            id="bookSessionBtn" 
                            class="mt-4 w-full" 
                            @click="openModalWithData(tutor.id)"
                        >
                            <div class="flex items-center">
                                <UserIcon class="text-white w-5 h-5 mr-2" />
                                <span>
                                    Book Session 
                                </span>
                            </div>
                        </PrimaryButton>
                        <PrimaryButton 
                            v-if="sessions.length > 0 && tutormodules.length > 0 && isLeader"
                            id="bookSessionBtn" 
                            class="mt-4 w-full" 
                            @click="openModalWithData1(tutor.id)"
                        >   
                            <div class="flex items-center">
                                <UserGroupIcon class="text-white w-5 h-5 mr-2" />
                                <span>
                                    Book Session for Peer Group
                                </span>
                            </div>
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Tutor Details -->
                <div class="flex flex-col flex-1 bg-white rounded-md mt-8 shadow h-fit py-4 px-6">
                    <h2 class="text-2xl font-bold">Tutor Details</h2>
                    <p class="text-gray-700">
                        View the tutor's modules and availability to easily find when and what they teach.
                    </p>

                    <div class="flex w-full gap-12">
                        <!-- Selected Modules -->
                        <div v-if="tutormodules && tutormodules.length > 0" class="mt-4 flex flex-col flex-1">
                            <h3 class="text-lg font-semibold mb-2">Modules</h3>
                            <ul>
                                <li v-for="module in tutormodules" :key="module.id" class="flex items-center even:bg-accent/5 px-4 py-2 text-gray-800">
                                    <AcademicCapIcon class="mr-2 w-4 h-4 text-gray-700" />
                                    {{ module.module_name }}
                                </li>
                            </ul>
                        </div>
                        <div v-else class="mt-4 flex flex-col flex-1">
                            <h3 class="text-lg font-medium mb-2">Modules</h3>
                            <p class="mt-2 text-gray-600 h-full">No modules available.</p>
                        </div>

                        <!-- Available Time -->
                        <div v-if="sessions.length > 0" class="mt-4 flex flex-col flex-1">
                            <h3 class="text-lg font-medium mb-2">Tutor Availability</h3>
                            <ul>
                                <li 
                                    v-for="session in sessions" 
                                    :key="session.id" 
                                    class="flex justify-between even:bg-accent/5 px-4 py-2 text-gray-800 "
                                >
                                    <div class="flex items-center">
                                        <CalendarDateRangeIcon class="mr-2 w-4 h-4 text-gray-700" /> {{ formatDateToWords(session.session_date) }} 
                                    </div>
                                    <div class="flex items-center">
                                        {{ session.start_time }} - {{ session.end_time }}
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="mt-4 flex flex-col flex-1">
                            <h3 class="text-lg font-medium mb-2">Tutor Availability</h3>
                            <p class="mt-2 text-gray-600 h-full">No sessions available.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

