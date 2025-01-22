<script setup>
import DynamicDropdown from "@/Components/DynamicDropdown.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import TutorCard from "@/Components/TutorCard.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { UserGroupIcon, UserIcon, PlusIcon, CheckIcon, XMarkIcon, CheckBadgeIcon } from "@heroicons/vue/24/solid";
import { Link } from "@inertiajs/vue3";
import CreatePeerGroup from "@/Components/CreatePeerGroup.vue";
import PeerGroupCard from "@/Components/PeerGroupCard.vue";
import DangerButton from "@/Components/DangerButton.vue";

// Props
const props = defineProps({
    tutors: Array,
    sessions: Array,
    cancelledSessions: Array,
    sModules: Array,
    peerGroups: Array,
    peerGroupsAsMember: Array,
});

console.log(JSON.stringify(props.tutors, null, 2));

const openModal = ref(null);

const closeModal = () => {
    openModal.value = null;
};

const openModalWithData = () => {
    openModal.value = true;
};

const activeContent = ref("solo"); // Tracks active tab

const searchQuery = ref(""); // Tracks search input
const selectedModule = ref(""); // Tracks selected module
const moduleError = ref("");    // Tracks dropdown error
const selectedSorting = ref(1); // Tracks selected module
const sortError = ref("");    // Tracks dropdown error

// Filter tutors based on module selection
const filteredTutors = ref([]); // Tracks filtered tutors dynamically
// Initialize tutors with all available tutors
filteredTutors.value = props.tutors;

// Sorting Options
const sortOptions = [
  { id: 1, name: "Sort by Relevance" },
  { id: 2, name: "Sort by Rating" },
  { id: 3, name: "Sort by Cancellation %" }
];

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
        Object.values(tutor.modules).includes(selectedModuleDetails.module_name)
    );
});

watch(selectedSorting, (newValue) => {
    const selectedRating = sortOptions.find((sort) => sort.id === newValue);
    
    if (selectedRating && selectedRating.id === 1) {
        // Clear the filteredTutors if newValue is 1
        filteredTutors.value.sort((a, b) => {
            // First, check if degree matches
            if (a.matchesUserDegree === b.matchesUserDegree) {
                // If both match the degree, sort by rating (higher rating first)
                const ratingA = a.rating || 0; // Default to 0 if rating is null
                const ratingB = b.rating || 0; // Default to 0 if rating is null

                // Compare ratings, higher rating first
                if (ratingA === ratingB) {
                    return 0; // If ratings are equal, return 0
                }

                return ratingB > ratingA ? 1 : -1; // Descending order
            }

            // Tutors who match the degree come first (return negative value for a)
            return a.matchesUserDegree ? -1 : 1;
        });
        return;
    }

    if (selectedRating && selectedRating.id === 2) {
        // Create a sorted copy of the tutors list to avoid mutating the original list
        filteredTutors.value.sort((a, b) => {
            const ratingA = a.rating || 0; // Default to 0 if rating is null
            const ratingB = b.rating || 0; // Default to 0 if rating is null
            return ratingB - ratingA; // Sort in descending order
        });
    }

    if (selectedRating && selectedRating.id === 3) {
        // Create a sorted copy of the tutors list to avoid mutating the original list
        filteredTutors.value.sort((a, b) => {
            const cancelA = a.cancellation;
            const cancelB = b.cancellation;

            // If both values are null, keep their original order
            if (cancelA === null && cancelB === null) {
                return 0;
            }

            // Place null values at the end
            if (cancelA === null) {
                return 1;
            }
            if (cancelB === null) {
                return -1;
            }

            // Sort non-null values in ascending order
            return cancelA - cancelB;
        });
    }
});

// Computed property to dynamically update displayed tutors
const displayedTutors = computed(() => {
    let tutorsToDisplay = filteredTutors.value.length > 0 ? filteredTutors.value : props.tutors;

    // Apply search filter if searchQuery exists
    if (searchQuery.value.trim() !== "") {
        tutorsToDisplay = tutorsToDisplay.filter((tutor) =>
            tutor.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }
    
    return tutorsToDisplay;
});

const acceptCancellation = (session) => {
    // Submit the form using router.post
    router.post('/tutor/sessions/acceptCancel', session, {
        onSuccess: () => {
            alert('Session rescheduled successfully!');
        },
        onError: (errors) => {
            alert('Failed to reschedule session:', errors);
        },
    });
};

const denyCancellation = (session) => {
    // Submit the form using router.post
    router.post('/tutor/sessions/denyCancel', session, {
        onSuccess: () => {
            alert('Session cancelled successfully!');
        },
        onError: (errors) => {
            alert('Failed to cancel session:', errors);
        },
    });
};
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
            <div v-if="activeContent === 'solo'" class="container flex flex-col lg:flex-row gap-10 max-h-[85vh] min-h-[85vh]">
                <!-- Tutor Listings -->
                <div class="flex flex-col flex-1 bg-white rounded-md shadow-sm py-4 px-6">
                    <!-- Filters -->
                    <div class="flex flex-col flex-1 gap-5 max-h-full h-full">
                        <!-- Filters -->
                        <div class="flex flex-col gap-2">
                            <!-- Title -->
                            <h1 class="text-2xl font-bold text-slate-900">Tutors</h1>
                            <p class="text-gray-600 -mt-2 mb-2">Find and book sessions with tutors for your modules.</p>
                            <TextInput
                                v-model="searchQuery"
                                placeholder="Search Tutor by Name"
                            />
                            <div class="flex gap-3 flex-col xl:flex-row">
                                <DynamicDropdown
                                    label="Search by Module"
                                    v-model="selectedModule"
                                    :options="sModules"
                                    :error="moduleError"
                                    class="flex flex-1"
                                />
                                <DynamicDropdown
                                    label=""
                                    v-model="selectedSorting"
                                    :options="sortOptions"
                                    :error="sortError"
                                    class="flex flex-1"
                                />
                            </div>
                        </div>
                        <div class="flex">
                            <span class="w-full h-0.5 bg-accent"></span>
                        </div>

                        <!-- Scrollable Tutor Listings -->
                        <!-- Show tutors when no search or filter is applied -->
                        <p v-if="!(tutors.length > 0)" class="text-gray-600 text-center">No tutors are currently available for your registered modules.</p>
    
                        <div v-else-if="searchQuery === '' && selectedModule === ''" class="flex flex-col flex-1 gap-3.5 max-h-full overflow-y-auto">   
                            <div v-for="tutor in tutors" :key="tutor.id">
                                <TutorCard
                                    :tutorname="tutor.name"
                                    :profile_pic="tutor.profilePic"
                                    :tutor_id="tutor.id"
                                    :degree="tutor.degree"
                                    :rating="tutor.rating"
                                />
                            </div>
                        </div>

                        <!-- Filtered Tutors -->
                        <div v-else-if="displayedTutors.length > 0" class="flex flex-col flex-1 gap-3.5 max-h-full overflow-y-auto">
                            <TutorCard
                                v-for="tutor in displayedTutors"
                                :key="tutor.id"
                                :tutorname="tutor.name"
                                :profile_pic="tutor.profilePic"
                                :tutor_id="tutor.id"
                                :degree="tutor.degree"
                                :rating="tutor.rating"
                            />
                        </div>
                        <p v-else class="text-gray-600 text-center"> No tutors match the search criteria. Please adjust your search and try again.</p>
                        
                        <!-- Fallback Message -->
                    </div>
                </div>


                <!-- Upcoming sessions -->
                <div class="flex flex-1 bg-white rounded-md shadow-sm py-4 px-6 max-w-[30rem] min-h-60 overflow-y-auto max-h-full h-auto">
                    <div class="flex flex-col w-full h-auto space-y-4">
                        <!-- Section Header -->
                        <h1 class="text-xl font-bold text-slate-900">Upcoming Sessions</h1>

                        <div v-if="cancelledSessions.length > 0" class="flex flex-col gap-3 overflow-y-auto h-auto max-h-96">
                            <div
                                v-for="session in cancelledSessions"
                                :key="session.sessionId"
                                class="flex flex-col bg-secondary/5 p-3 rounded-md shadow-md"
                            >
                                <!-- Tutor Name -->
                                <div class="flex items-center">
                                    <img
                                        :src="session.profile_pic"
                                        alt="Profile Picture"
                                        class="w-8 h-8 mr-3 rounded-full object-cover"
                                    />
                                    <p class="text-lg font-semibold text-gray-800">{{ session.tutor_name }}</p>
                                    <CheckBadgeIcon class="ml-1 w-5 h-5 text-accent" />
                                </div>
                                <div>
                                    <p class="text-lg text-gray-600">{{ session.module }}</p>
                                    <p class="text-lg text-gray-600">{{ formatDateToWords(session.sessionDate) }} | {{ session.sessionStartTime }} - {{ session.sessionEndTime }}</p>
                                    <p class="text-lg text-red-500">CANCELLED</p>
                                    <p class="text-lg text-gray-600">Reason: {{ session.reason }}</p>
                                </div>
                                <div class="flex my-2">
                                    <span class="w-full h-0.5 bg-accentdark"></span>
                                </div>
                                <div class="flex flex-1 justify-between items-center">
                                    <div>
                                        <p class="text-lg text-gray-600">Proposed Reschedule:</p>
                                        <p class="text-lg text-gray-600">{{ formatDateToWords(session.altDate) }} | {{ session.altStartTime }} - {{ session.altEndTime }}</p>
                                    </div>
                                    <div class="flex gap-3 h-fit">
                                        <PrimaryButton 
                                            :id="'acceptCancel-' + session.sessionId" 
                                            class="!p-1.5 !rounded-full"
                                            @click="acceptCancellation(session)"
                                        >
                                            <CheckIcon class="w-5 h-5"/>
                                        </PrimaryButton>
                                        <DangerButton
                                            :id="'denyCancel-' + session.sessionId" 
                                            class="!p-1.5 !rounded-full" 
                                            @click="denyCancellation(session)"
                                        >   
                                            <XMarkIcon class="w-5 h-5"/>
                                        </DangerButton>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex">
                            <span class="w-full h-0.5 bg-accentdark"></span>
                        </div>

                        <!-- Session List -->
                        <div v-if="sessions.length > 0" class="flex flex-col gap-3 overflow-y-auto h-auto max-h-full">
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
                                <div>
                                    <p v-if="session.reason" class="text-lg text-gray-600">Notes: {{ session.reason }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-col w-full">
                            <p class="text-gray-600 text-center">No upcoming sessions with tutor.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peer Group -->
            <div v-if="activeContent === 'peergroup'" class="container flex flex-col lg:flex-row gap-10 max-h-[85vh] min-h-[85vh]">
                <div class="flex flex-col flex-1 bg-white rounded-md shadow-sm py-4 px-6 gap-5 " >
                    <div class="flex flex-col gap-2">
                        <div class="inline-flex justify-between items-center">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-900">My Peer Groups</h1>
                                <p class="text-gray-600">List of peer groups under your leadership.</p>
                            </div>
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
                        <CreatePeerGroup
                            :openModal="openModal"
                            :closeModal="closeModal"
                            :sModules="sModules"
                        />
                    </div>
                    <div class="flex">
                        <span class="w-full h-0.5 bg-accent"></span>
                    </div>
                    <!-- filter peer groups -->
                    <div class="flex flex-col flex-1 max-h-full overflow-y-auto gap-2">
                        <PeerGroupCard
                            v-if="peerGroups.length > 0"
                            v-for="group in peerGroups"
                            :key="group.peerGroupId"
                            :peerGroup="group"
                        />
                        <div v-else class="text-gray-600 mx-auto mt-6">
                            <p class="">No peer groups created for you.</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col flex-1 max-w-2xl bg-white rounded-md shadow-sm py-4 px-6 gap-5" >
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Joined Peer Groups</h1>
                        <p class="text-gray-600">List of peer groups you are a member of.</p>
                    </div>
                    <div class="flex">
                        <span class="w-full h-0.5 bg-accent"></span>
                    </div>
                    <div class="flex flex-col flex-1 max-h-full overflow-y-auto gap-2">
                        <PeerGroupCard
                            v-if="peerGroupsAsMember.length > 0"
                            v-for="group in peerGroupsAsMember"
                            :key="group.peerGroupId"
                            :peerGroup="group"
                        />
                        <div v-else class="text-gray-600 mx-auto mt-6">
                            <p class="">You are not currently a member of any peer group.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
