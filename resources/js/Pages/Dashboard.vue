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
});

function getDegreeName(schoolId) {
    console.log(schoolId);
    console.log(props.allDegree);
    const school = props.allDegree.find((deg) => deg.id === schoolId);
    return school ? school.degree_name : 'Not Found';
}
console.log(props.semstertutors);
console.log(props.tutors);
// Define the state for the selected value
const selectedModule = ref("");

// Define options for the dropdown
const modules = [
    { id: 1, module_name: "Software Development and Application Modelling 1" },
    { id: 2, module_name: "Digital Technologies 1" },
    { id: 3, module_name: "Web Development and Operating Systems 1" },
    { id: 4, module_name: "Networking Concepts and Cyber Security 1" },
];

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
                                :tutorname="tutor.user.name"
                                :cbnumber="tutor.profile.cb_number"
                                :profile_pic="tutor.profile.profile_pic"
                                :tutor_id="tutor.id"
                                :school="getDegreeName(tutor.profile.degree_id)"
                            />
                        </div>
                        <div v-for="tutor in tutors" :key="tutor.id">
                            <TutorCard
                                :tutorname="tutor.user.name"
                                :cbnumber="tutor.profile.cb_number"
                                :profile_pic="tutor.profile.profile_pic"
                                :tutor_id="tutor.id"
                                :school="getDegreeName(tutor.profile.degree_id)"
                            />
                        </div>

                    </div>
                </div>

                <!-- Upcoming Sessions -->
                <div class="flex flex-1 bg-white rounded-md shadow-sm py-2 px-4 max-w-[30rem]">
                    <h1>Upcoming Sessions</h1>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
