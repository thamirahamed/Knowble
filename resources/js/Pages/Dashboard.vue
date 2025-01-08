<script setup>
import DynamicDropdown from "@/Components/DynamicDropdown.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import TutorCard from "@/Components/TutorCard.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { UserGroupIcon, UserIcon, PlusIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import CreatePeerGroup from "@/Components/CreatePeerGroup.vue";
import PeerGroupCard from "@/Components/PeerGroupCard.vue";

// Props
const props = defineProps({
    semstertutors: Array,
    allDegree: Array,
    tutors: Array,
    sessions: Array,
    sModules: Array,
    peerGroups: Array,
});
const openModal = ref(null);
const closeModal = () => {
    openModal.value = null;
};
const openModalWithData = () => {
    openModal.value = true;
};
// Reactive states
const activeContent = ref("solo"); // Tracks active tab
const searchQuery = ref(""); // Tracks search input
const selectedModule = ref(""); // Tracks selected module
const moduleError = ref("");    // Tracks dropdown error

// Filter tutors based on module selection
const filteredTutors = ref([]); // Tracks filtered tutors dynamically

// Utility function to get degree name
function getDegreeName(schoolId) {
    const school = props.allDegree.find((deg) => deg.id === schoolId);
    return school ? school.degree_name : "Not Found";
}
const error1 = ref("");
// Define the state for the selected value
const selectedModules = ref("");
// Error message for validation
const moduleErrors = ref("");
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

// Watch selected module for filtering
watch(selectedModule, (newValue) => {
    const selectedModuleDetails = props.sModules.find((mod) => mod.id === newValue);

    if (!selectedModuleDetails) {
        filteredTutors.value = []; // Clear filters if no module selected
        return;
    }

    // Filter tutors based on selected module
    filteredTutors.value = props.tutors.filter((tutor) =>
        tutor.modules.some((module) => module.module_name === selectedModuleDetails.module_name)
    );
});

// Computed property to dynamically update displayed tutors
const displayedTutors = computed(() => {
    let tutorsToDisplay = filteredTutors.value.length > 0 ? filteredTutors.value : props.tutors;

    // Apply search filter if searchQuery exists
    if (searchQuery.value.trim() !== "") {
        tutorsToDisplay = tutorsToDisplay.filter((tutor) =>
            tutor.user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    return tutorsToDisplay;
});

// Initialize tutors with all available tutors
filteredTutors.value = props.tutors;



// Peer Group
const searchQueryPeer = ref("");
const selectedModulePeer = ref("");

// Filter peer groups based on module selection
const filteredPeerGroups = ref([]);

// Watch selected module for filtering
watch(selectedModulePeer, (newValue) => {
    const selectedModuleDetails = props.sModules.find((mod) => mod.id === newValue);

    if (!selectedModuleDetails) {
        filteredPeerGroups.value = []; // Clear filters if no module selected
        return;
    }

    // Filter peer groups based on selected module
    filteredPeerGroups.value = props.peerGroups.filter((group) =>
        group.module === selectedModuleDetails.module_name
    );
});
// Computed property to dynamically update displayed peer groups
const displayedPeerGroups = computed(() => {
    let peerGroupsToDisplay = filteredPeerGroups.value.length > 0 ? filteredPeerGroups.value : props.peerGroups;

    // Apply search filter if searchQuery exists
    if (searchQueryPeer.value.trim() !== "") {
        peerGroupsToDisplay = peerGroupsToDisplay.filter((group) =>
            group.name.toLowerCase().includes(searchQueryPeer.value.toLowerCase())
        );
    }

    return peerGroupsToDisplay;
});

// Initialize peer groups with all available peer groups
filteredPeerGroups.value = props.peerGroups;
console.log(displayedPeerGroups);

</script>


<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <div class="pt-8 flex items-center flex-col">
            <div class="flex flex-col container w-full">
                <div class="flex justify-start">
                    <Link
                        href="#"
                        class="inline-flex items-center py-2 px-4"
                        :class="activeContent === 'solo' ? 'text-white bg-accent/80 relative z-0 overflow-hidden transition-all duration-200 after:absolute after:inset-0 after:-z-10 after:scale-[2.5] after:rounded-[100%] after:bg-gradient-to-l from-accentdark hover:bg-accent' : 'hover:bg-primary/15 text-gray-600 hover:text-gray-900'"
                        @click="() => (activeContent = 'solo')"
                        preserve-state
                        id="one-on-one"
                    >
                        <UserIcon class="w-5 h-5 mr-2" />
                        One-on-One
                    </Link>
                    <Link
                        href="#"
                        class="inline-flex items-center py-2 px-4"
                        :class="activeContent === 'peergroup' ? 'text-white bg-accent/80 relative z-0 overflow-hidden transition-all duration-200 after:absolute after:inset-0 after:-z-10 after:scale-[2.5] after:rounded-[100%] after:bg-gradient-to-l from-accentdark hover:bg-accent' : 'hover:bg-primary/15 text-gray-600 hover:text-gray-900'"
                        @click="() => (activeContent = 'peergroup')"
                        preserve-state
                        id="peergrp"
                    >
                        <UserGroupIcon class="w-5 h-5 mr-2" />
                        Peer Group
                    </Link>
                </div>
                <div class="container flex mb-4">
                    <span class="w-full h-0.5 bg-accent"></span>
                </div>
            </div>
            <!-- One on One Tutoring -->
            <div v-if="activeContent === 'solo'" class="container flex flex-col lg:flex-row gap-10 h-[85vh]">
                <!-- Tutor Listings -->
                <div class="flex flex-col flex-1 bg-white rounded-md shadow-sm py-4 px-6 gap-5">
                    <!-- Filters -->
                    <div class="flex flex-col flex-1 bg-white rounded-md shadow-sm py-4 px-6 gap-5">
                        <!-- Filters -->
                        <div class="flex flex-col gap-2">
                            <!-- Title -->
                            <h1 class="text-2xl font-bold text-slate-900">Tutors</h1>
                            <DynamicDropdown
                                label="Module"
                                v-model="selectedModule"
                                :options="sModules"
                                :error="moduleError"
                            />
                            <TextInput
                                v-model="searchQuery"
                                placeholder="Search Tutor by Name"
                            />
                        </div>

                        <!-- Scrollable Tutor Listings -->
                        <!-- Scrollable Tutor Listings -->
                        <div class="flex flex-col gap-2 overflow-y-auto max-h-[400px]"> <!-- Scrollable Container -->

                            <!-- Show Semester Tutors only when no search or filter is applied -->
                            <div v-if="searchQuery === '' && selectedModule === ''">
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
                                <div v-for="tutor in displayedTutors" :key="tutor.id">
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

                            <!-- Filtered Tutors -->
                            <div v-else-if="displayedTutors.length > 0" class="flex flex-col gap-2">
                                <TutorCard
                                    v-for="tutor in displayedTutors"
                                    :key="tutor.id"
                                    :tutorname="tutor.user.name"
                                    :cbnumber="tutor.profile.cb_number"
                                    :profile_pic="tutor.profile.profile_pic"
                                    :tutor_id="tutor.id"
                                    :school="getDegreeName(tutor.profile.degree_id)"
                                />
                            </div>

                            <!-- Fallback Message -->
                            <p v-else class="text-gray-600 text-center">No tutors available.</p>
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
                            <p class="text-gray-600">No upcoming sessions with tutor.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peer Group -->
            <div v-if="activeContent === 'peergroup'" class="container flex flex-col lg:flex-row gap-10 h-[85vh]">
                <div class="flex flex-col flex-1 bg-white rounded-md shadow-sm py-4 px-6 gap-5 " >
                    <div class="flex flex-col gap-2">
                        <div class="inline-flex justify-between items-center">
                            <h1 class="text-2xl font-bold text-slate-900">Peer Groups</h1>
                            <PrimaryButton
                                :icon="true"
                                iconPlacement="left"
                                id="createPeerGrpBtn"
                                @click="openModalWithData()"
                            >
                                <template #icon>
                                    <PlusIcon class="text-white" />
                                </template>
                                Create Peer Group
                            </PrimaryButton>
                        </div>
                        <DynamicDropdown
                            label="Module"
                            id="search-module-dropdown"
                            :options="sModules"
                            v-model="selectedModulePeer"
                            :error="moduleError"
                        />
                        <TextInput
                            id="searchTutor"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Search Peer Groups"
                            v-model="searchQueryPeer"
                        />
                        <CreatePeerGroup
                            :openModal="openModal"
                            :closeModal="closeModal"
                            :sModules="sModules"
                        />
                    </div>

                    <!-- filter peer groups -->
                    <div class="flex flex-col gap-2">
                        <div v-if="displayedPeerGroups.length > 0">
                            <div v-for="group in displayedPeerGroups" :key="group.id">
                                <div v-if="group.isUserLeader === false">
                                    <PeerGroupCard
                                        :peerGroup="group"
                                    />
                                </div>

                            </div>
                        </div>
                        <div v-else class="text-gray-600 mx-auto mt-6">
                            <p class="">No peer groups found for your modules.</p>
                        </div>
                    </div>

<!--                    <div class="flex flex-col overflow-y-auto gap-2" v-if="peerGroups.length > 0">-->
<!--                        <div v-for="group in peerGroups" :key="group.id" class="flex flex-col w-full">-->
<!--                            <div v-if="group.isUserLeader === false && group.isUserMember === false && group.currentMembers < group.totalMembers">-->
<!--                                <PeerGroupCard-->
<!--                                    :peerGroup="group"-->
<!--                                />-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div v-else class="text-gray-600 mx-auto mt-6">-->
<!--                        <p class="">No peer groups found for your modules.</p>-->
<!--                    </div>-->
                </div>
                <div class="flex flex-col flex-1 max-w-xl bg-white rounded-md shadow-sm py-4 px-6 gap-5 min-h-60 h-fit overflow-y-auto max-h-full" >
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Joined Groups</h1>
                    </div>
                    <div class="flex flex-col flex-1 overflow-y-auto gap-2">
                        <div
                            v-for="group in peerGroups"
                            :key="group.id"
                        >
                            <div v-if="group.isUserLeader === true || group.isUserMember === true">
                                <PeerGroupCard
                                    :peerGroup="group"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
