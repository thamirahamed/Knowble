<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { computed } from "vue";
import { format } from "date-fns";
import { router } from '@inertiajs/vue3';

const props = defineProps({
    users: Object, // Users data
    tutors: Array, // Tutors data with 'status'
    profiles: Object, // Profiles data
    courses: Object, // Courses data with 'CourseName'
    years: Object, // Years data with 'level'
});

// Filter tutors with status 'pending'
const pendingTutors = computed(() => {
    return props.tutors
        .filter(tutor => tutor.status === "pending")
        .map(tutor => {
            const user = props.users[tutor.user_id] || {};
            const profile = Object.values(props.profiles).find(p => p.user_id === tutor.user_id) || {};
            const course = props.courses[profile.course_id] || {};
            const year = props.years[profile.level_id] || {};

            const formattedDate = tutor.created_at ? format(new Date(tutor.created_at), "dd/MM/yyyy") : "N/A";

            return {
                id: tutor.id,
                date: formattedDate,
                name: user.name,
                cbNumber: profile.cb_number,
                course: course.CourseName,
                year: year.level,
            };
        });
});

const processedTutors = computed(() => {
    return props.tutors
        .filter(tutor => tutor.status === "approved" || tutor.status === "rejected")
        .map(tutor => {
            const user = props.users[tutor.user_id] || {};
            const profile = Object.values(props.profiles).find(p => p.user_id === tutor.user_id) || {};
            const course = props.courses[profile.course_id] || {};
            const year = props.years[profile.level_id] || {};

            const formattedDate = tutor.created_at ? format(new Date(tutor.created_at), "dd/MM/yyyy") : "N/A";

            return {
                id: tutor.id,
                date: formattedDate,
                name: user.name || "N/A",
                cbNumber: profile.cb_number || "N/A",
                course: course.CourseName || "N/A",
                year: year.level || "N/A",
                status: tutor.status,
            };
        });
});

// Approve Tutor
const approveTutor = id => {
    router.post(`/approve-tutor/${id}`, {}, {
        onSuccess: () => {
            console.log("Tutor approved successfully!");
        }
    });
};

// Reject Tutor
const rejectTutor = id => {
    router.post(`/reject-tutor/${id}`, {}, {
        onSuccess: () => {
            console.log("Tutor rejected successfully!");
        }
    });
};

// Delete Tutor
const deleteTutor = id => {
    if (confirm("Are you sure you want to delete this tutor request?")) {
        router.delete(`/tutors/${id}`, {
            onSuccess: () => {
                console.log("Tutor deleted successfully!");
            }
        });
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <div class="flex flex-col gap-10 items-center mt-10">
            <!-- Upcoming Tutor Requests -->
            <div class="flex flex-col container bg-white px-10 py-6 rounded-md shadow">
                <h1 class="text-3xl mb-1 tracking-wide font-bold">Upcoming Tutor Requests</h1>
                <p class="text-md mb-6 text-gray-500">
                    Displays tutor requests that are awaiting review and approval.
                </p>

                <table class="min-w-full table-auto border-collapse">
                    <thead class="text-lg bg-secondary text-white">
                    <tr>
                        <th class="border-b font-semibold px-4 py-2 text-left">Request#</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Date & Time</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Name</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">CB Number</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Course</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Year</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Actions</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="tutor in pendingTutors" :key="tutor.id" class="even:bg-secondary/15">
                        <td class="border-b px-4 py-2">{{ tutor.id }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.date }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.name }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.cbNumber }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.course }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.year }}</td>
                        <td class="border-b px-4 py-2 space-x-4">
                            <PrimaryButton @click="approveTutor(tutor.id)">
                                Approve
                            </PrimaryButton>
                            <DangerButton @click="rejectTutor(tutor.id)">
                                Reject
                            </DangerButton>
                        </td>
                        <td class="border-b px-4 py-2">
                            <button @click="deleteTutor(tutor.id)" class="text-red-500 hover:text-red-700">
                                Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Completed Tutor Requests -->
            <div class="flex flex-col container bg-white px-10 py-6 rounded-md shadow">
                <h1 class="text-3xl mb-1 tracking-wide font-bold">Completed Tutor Requests</h1>
                <p class="text-md mb-6 text-gray-500">
                    Stores all tutor requests that have been processed with either approval or rejection status.
                </p>
                <table class="min-w-full table-auto border-collapse">
                    <thead class="text-lg bg-secondary text-white">
                    <tr>
                        <th class="border-b font-semibold px-4 py-2 text-left">Request#</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Date & Time</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Name</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">CB Number</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">School of Study</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Year</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Status</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(tutor, index) in processedTutors" :key="tutor.id" class="even:bg-secondary/15">
                        <td class="border-b px-4 py-2">{{ index + 1 }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.date }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.name }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.cbNumber }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.course }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.year }}</td>
                        <td class="border-b px-4 py-2">
                            <span :class="{ 'text-green-600': tutor.status === 'approved', 'text-red-600': tutor.status === 'rejected' }">
                                 {{ tutor.status }}
                             </span>
                        </td>

                        <td class="border-b px-4 py-2">
                            <button class="text-red-500 hover:text-red-700" @click="deleteTutor(tutor.id)">Delete</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
