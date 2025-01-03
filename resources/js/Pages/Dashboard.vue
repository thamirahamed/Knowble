<script setup>
import DynamicDropdown from "@/Components/DynamicDropdown.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import TutorCard from "@/Components/TutorCard.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { CheckBadgeIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    semstertutors: Array,
    allDegree: Array,
    tutors: Array,
    sessions: Array,
});
console.log(props.sessions);

function getDegreeName(schoolId) {
    const school = props.allDegree.find((deg) => deg.id === schoolId);
    return school ? school.degree_name : 'Not Found';
}

// Define the state for the selected value
const selectedModule = ref("");

// Error message for validation
const moduleError = ref("");
const joinMeeting = (meetingUrl) => {
    router.visit(route("meetings.index", { meetingUrl }));
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
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <div class="pt-8 flex justify-center">
            <div class="container flex flex-col lg:flex-row gap-10 h-[85vh]">

                <!-- Tutor Listings -->
                <div class="flex flex-col flex-1 bg-white rounded-md shadow-sm py-6 px-8 gap-5 " >
                    <div class="flex flex-col gap-2">
                        <h1 class="text-xl font-bold">Tutors</h1>
                        <DynamicDropdown
                            label="Module"
                            id="module-dropdown"
                            :options="modules"
                            v-model="selectedModule"
                            :error="moduleError"
                        />
                        <TextInput
                            id="searchTutor"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Search Tutor"
                        />
                    </div>

                    <div class="flex flex-col overflow-y-auto gap-2">
                        <div v-for="tutor in semstertutors" :key="tutor.id">
                            <TutorCard
                                v-if="tutor.tutor"
                                :tutorname="tutor.user.name"
                                :cbnumber="tutor.profile.cb_number"
                                :profile_pic="tutor.profile.profile_pic"
                                :tutor_id="tutor.tutor"
                                :school="getDegreeName(tutor.profile.degree_id)"
                            />
                        </div>
                        <div v-for="tutor in tutors" :key="tutor.id">
                            <TutorCard
                                v-if="tutor.tutor"
                                :tutorname="tutor.user.name"
                                :cbnumber="tutor.profile.cb_number"
                                :profile_pic="tutor.profile.profile_pic"
                                :tutor_id="tutor.tutor"
                                :school="getDegreeName(tutor.profile.degree_id)"
                            />
                        </div>

                    </div>
                </div>

                <!-- Upcoming sessions -->
                <div class="flex flex-1 bg-white rounded-md shadow-sm py-6 px-8 max-w-[30rem]">
                    <div class="flex flex-col w-full">
                        <!-- Section Header -->
                        <h1 class="text-xl font-bold mb-4 text-gray-700">Upcoming Sessions</h1>

                        <!-- Session List -->
                        <div class="flex flex-col gap-3 overflow-y-auto h-full">
                            <div
                                v-for="(session, index) in sessions"
                                :key="index"
                                class="flex flex-col bg-accentdark/5 p-3 rounded-md shadow-md"
                            >
                                <!-- Tutor Name -->
                                <div>
                                    <div class="flex items-center">
                                        <p class="text-lg font-semibold text-gray-800">{{ session.tutor_name }}</p>
                                        <CheckBadgeIcon class="ml-1 w-5" />
                                    </div>
                                    <p class="text-lg text-gray-600">{{ session.module_name }}</p>
                                </div>
                                
                                <!-- Join Meeting Button -->
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-lg text-gray-600">{{ formatDateToWords(session.session_date) }}</p>
                                        <p class="text-lg text-gray-600">{{ session.start_time }} - {{ session.end_time }}</p>
                                    </div>
                                    <PrimaryButton class="!text-sm">Join Now</PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
