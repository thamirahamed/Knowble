<script setup>
import DynamicDropdown from "@/Components/DynamicDropdown.vue";
import TextInput from "@/Components/TextInput.vue";
import TutorCard from "@/Components/TutorCard.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    semstertutors: Array,
    allDegree: Array,
    tutors: Array,
    sessions: Array,
});
console.log(props.sessions);
console.log(props.tutors);
console.log(props.semstertutors);
console.log(props.allDegree);

function getDegreeName(schoolId) {
    const school = props.allDegree.find((deg) => deg.id === schoolId);
    return school ? school.degree_name : 'Not Found';
}

// Define the state for the selected value
const selectedModule = ref("");

// Error message for validation
const moduleError = ref("");

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <div class="pt-8 flex justify-center">
            <div class="container flex flex-col lg:flex-row gap-10 h-[85vh]">

                <!-- Tutor Listings -->
                <div class="flex flex-col flex-1 bg-white rounded-md shadow-sm py-6 px-8 gap-5 " >
                    <div class="flex flex-col gap-2">
                        <h1 class="text-xl font-semibold">Tutors</h1>
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
                <div class="flex flex-1 bg-white rounded-md shadow-sm py-4 px-6 max-w-[30rem]">
                    <div class="w-full">
                        <!-- Section Header -->
                        <h1 class="text-2xl font-bold mb-4 text-gray-700">Upcoming Sessions</h1>

                        <!-- Session List -->
                        <div class="flex flex-col gap-3">
                            <div
                                v-for="(session, index) in sessions"
                                :key="index"
                                class="flex justify-between items-center bg-gray-100 p-3 rounded-md shadow-sm"
                            >
                                <!-- Tutor Name -->
                                <div>
                                    <p class="text-lg font-semibold text-gray-800">{{ session.tutor_name }}</p>
                                </div>

                                <!-- Session Actions -->
                                <div class="flex items-center gap-2">
                                    <!-- Session Status -->
                                    <span
                                        v-if="session.status !== 'accepted'"
                                        class="text-sm font-medium px-2 py-1 rounded-full"
                                        :class="{
                            'bg-yellow-200 text-yellow-700': session.status === 'pending',
                            'bg-red-200 text-red-700': session.status === 'cancelled'
                        }"
                                    >
                        {{ session.status }}
                    </span>

                                    <!-- Join Meeting Button -->
                                    <a
                                        v-if="session.status === 'accepted' && session.meeting_url"
                                        :href="route('meetings.index')"
                                        target="_blank"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-full transition"
                                    >
                                        Join Meeting
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
