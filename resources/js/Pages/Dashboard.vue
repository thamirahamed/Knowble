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
console.log(JSON.stringify(props.sessions, null, 2));

function getDegreeName(schoolId) {
    const school = props.allDegree.find((deg) => deg.id === schoolId);
    return school ? school.degree_name : 'Not Found';
}

// Define the state for the selected value
const selectedModule = ref("");

// Error message for validation
const moduleError = ref("");

// Redirect to Join Meeting with the meeting_url from a specific booking
const joinMeeting = (booking) => {
    // Get the base meeting URL from the booking
    let meetingUrl = booking.meeting_url;
    
    // Create the tutor's name (convert to lowercase and replace spaces with hyphens)
    const studentName = booking.student_name.replace(" ", "-").toLowerCase();  // Example: Emily Davis -> emily-davis
    
    // append the 'name' parameter
    meetingUrl += `&name=${studentName}`;

    router.visit(route("meetings.index", { meetingUrl, id: booking.id }));
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
                <div class="flex flex-col flex-1 bg-white rounded-md shadow-sm py-4 px-6 gap-5 " >
                    <div class="flex flex-col gap-2">
                        <h1 class="text-2xl font-bold text-slate-900">Tutors</h1>
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
                                :tutor_id="tutor.tutor.id"
                                :school="getDegreeName(tutor.profile.degree_id)"
                            />
                        </div>

                    </div>
                </div>

                <!-- Upcoming sessions -->
                <div class="flex flex-1 bg-white rounded-md shadow-sm py-4 px-6 max-w-[30rem] min-h-60 h-fit overflow-y-auto max-h-full">
                    <div class="flex flex-col w-full">
                        <!-- Section Header -->
                        <h1 class="text-xl font-bold mb-4 text-slate-900">Upcoming Sessions</h1>

                        <!-- Session List -->
                        <div v-if="sessions.length > 0" class="flex flex-col gap-3 overflow-y-auto h-full">
                            <div
                                v-for="(session, index) in sessions"
                                :key="index"
                                class="flex flex-col bg-secondary/5 p-3 rounded-md shadow-md"
                            >
                                <!-- Tutor Name -->
                                <div>
                                    <div class="flex items-center">
                                        <img
                                            :src="session.profile_pic"
                                            alt="Profile Picture"
                                            class="w-8 h-8 mr-3 rounded-full object-cover"
                                        />
                                        <p class="text-lg font-semibold text-gray-800">{{ session.tutor_name }}</p>
                                        <CheckBadgeIcon class="ml-1 w-5 h-5 text-accent" />
                                    </div>
                                    <p class="text-lg text-gray-600">{{ session.module_name }}</p>
                                </div>
                                
                                <!-- Join Meeting Button -->
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-lg text-gray-600">{{ formatDateToWords(session.session_date) }}</p>
                                        <p class="text-lg text-gray-600">{{ session.start_time }} - {{ session.end_time }}</p>
                                    </div>
                                    <PrimaryButton :id="'joinMeeting-' + session.id" class="!text-sm" @click="joinMeeting(session)">Join Now</PrimaryButton>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col w-full">
                            <p class="text-lg text-gray-600">No upcoming sessions.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
