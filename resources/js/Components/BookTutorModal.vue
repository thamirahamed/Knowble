<script setup>
import { ref } from 'vue';
import { XMarkIcon, ExclamationCircleIcon } from '@heroicons/vue/24/solid';
import { computed, watch } from 'vue';
import TextInput from './TextInput.vue';
import DynamicDropdown from './DynamicDropdown.vue';
import TutorCard from './TutorCard.vue';
import PeerCard from './PeerCard.vue';

const props = defineProps({
    openModal: Boolean,
    closeModal: Function,
    tutors: Array,
    sModules: Array
});


console.log(JSON.stringify(props.tutors, null, 2));

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

</script>

<template>
    <div v-if="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-md max-w-3xl w-full h-fit overflow-hidden">
            <!-- Modal Header -->
            <div class="flex justify-between items-center py-2 px-6 bg-primary">
                <h2 class="text-lg  font-light text-white">Book Tutors</h2>
                <button id="closeModal" @click="closeModal" class="p-1 rounded-full bg-red-500 text-white"><XMarkIcon class="w-4" /></button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4 px-6 pb-4 flex flex-col gap-3 h-full">
                <div class="flex flex-col flex-1 gap-5 h-full">
                    <!-- Filters -->
                    <div class="flex flex-col gap-2">
                        <!-- Title -->
                        <h1 class="text-2xl font-bold text-slate-900">Tutors</h1>
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

                    <div v-else-if="searchQuery === '' && selectedModule === ''" class="flex flex-col flex-1 gap-3.5 max-h-[32rem] overflow-y-auto">   
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
                    <div v-else-if="displayedTutors.length > 0" class="flex flex-col flex-1 gap-3.5 max-h-[32rem] overflow-y-auto">
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
        </div>
    </div>
</template>