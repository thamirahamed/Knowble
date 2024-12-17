<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { computed } from "vue";
import { format } from "date-fns";
import { router } from '@inertiajs/vue3';
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TutorVerificationModel from "@/Components/TutorVerificationModel.vue";
import { ref } from "vue";
import TutorDetailsModel from "@/Components/TutorDetailsModel.vue";

const props = defineProps({
    users: Object, // Users data
    tutors: Array, // Tutors data with 'status'
    profiles: Object, // Profiles data
    schools: Object, // Schools data
    degrees: Object, // Degrees data
    levels: Object, // Levels data
    semesters: Object, // Semesters data
});
console.log(props.levels);
// Filter tutors with status 'pending'
const pendingTutors = computed(() => {
    return props.tutors
        .filter(tutor => tutor.status === "pending")
        .map(tutor => {
            const user = props.users[tutor.user_id - 1] || {};
            const profile = Object.values(props.profiles).find(p => p.user_id === tutor.user_id) || {};
            const degree = props.degrees[profile.degree_id - 1] || {};
            const year = props.levels[profile.level_id - 1] || {};

            const formattedDate = tutor.created_at ? format(new Date(tutor.created_at), "dd/MM/yyyy") : "N/A";

            return {
                id: tutor.id,
                date: formattedDate,
                name: user.name,
                cbNumber: profile.cb_number,
                course: degree.degree_name,
                year: year.level_name,
            };
        });
});

const processedTutors = computed(() => {
    return props.tutors
        .filter(tutor => tutor.status === "approved" || tutor.status === "rejected")
        .map(tutor => {

            const user = props.users[tutor.user_id - 1] || {};
            const profile = Object.values(props.profiles).find(p => p.user_id === tutor.user_id) || {};
            const degree = props.degrees[profile.degree_id - 1] || {};
            const year = props.levels[profile.level_id - 1] || {};
            const formattedDate = tutor.created_at ? format(new Date(tutor.created_at), "dd/MM/yyyy") : "N/A";

            return {
                id: tutor.id,
                date: formattedDate,
                name: user.name || "N/A",
                cbNumber: profile.cb_number || "N/A",
                course: degree.degree_name || "N/A",
                year: year.level_name || "N/A",
                status: tutor.status,
            };
        });
});
const showModal = ref(null);
const modalData = ref({});

const TdshowModal = ref(false);
const TdmodalData = ref('');
const modalTutorId = ref('');

const openModal = async (id) => {
    showModal.value = id;
    modalData.value = {}; // Clear previous data

    try {
        const response = await axios.get(`/admin/data/${id}`);
        // Merge the response data with modalData but do not overwrite tutor-related information
        modalData.value = {
            ...modalData.value,
            ...response.data,
        };

    } catch (error) {
        console.error('Error fetching data:', error);
        modalData.value = { error: 'Failed to load data.' }; // Handle errors gracefully
    }
};

const openTdModal = async (id) => {
    TdshowModal.value = true;
    TdmodalData.value = {}; // Clear previous data

    try {
        const response = await axios.get(`/admin/tutordata/${id}`);
        TdmodalData.value = {
            ...TdmodalData.value,
            ...response.data,
        };

        // Fetch tutor ID from the first approved module
        if (response.data && response.data.rejectedModules && response.data.rejectedModules.length > 0) {
            modalTutorId.value = response.data.rejectedModules[0].pivot.tutor_id;
        } else {
            console.error ('Error fetching Tutor ID:', error)
        }

    } catch (error) {
        console.error('Error fetching data:', error);
        TdmodalData.value = { error: 'Failed to load data.' }; // Handle errors gracefully
    }
};

const closeModal = () => {
    showModal.value = false;
};

const closeTdModal = () => {
    TdshowModal.value = false;
};

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
                        <th class="border-b font-semibold px-4 py-2 text-left">Tutor#</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Date</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Name</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">CB Number</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Course</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Level</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Actions</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="tutor in pendingTutors" :key="tutor.id" class="even:bg-secondary/15">
                        <td class="border-b px-4 py-2">{{ tutor.id }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.date }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.name }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.cbNumber.toUpperCase() }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.course }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.year }}</td>
                        <td class="border-b px-4 py-2 space-x-4">
                            <button :id="`reviewBtn-${tutor.id}`"  @click="openModal(tutor.id)" class="text-blue-500 hover:text-blue-700 underline">
                                Review
                            </button>
                        </td>
                        <td class="border-b px-4 py-2">
                            <button :id="`deleteBtn-${tutor.id}`"  @click="deleteTutor(tutor.id)" class="text-red-500 hover:text-red-700 underline">
                                Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-for="tutor in pendingTutors" :key="tutor.id">
                    <TutorVerificationModel
                        :is-visible="showModal === tutor.id"
                        :modal-data="modalData"
                        :name="tutor.name"
                        :cbnumber="tutor.cbNumber"
                        :tutorid="tutor.id"
                        :degree="tutor.course" 
                        @close="closeModal"
                    />
                </div>
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
                        <th class="border-b font-semibold px-4 py-2 text-left">Tutor#</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Date</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Name</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">CB Number</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Course</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Level</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Status</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Modules</th>
                        <th class="border-b font-semibold px-4 py-2 text-left">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(tutor, index) in processedTutors" :key="tutor.id" class="even:bg-secondary/15">
                        <td class="border-b px-4 py-2">{{ tutor.id }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.date }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.name }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.cbNumber.toUpperCase() }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.course }}</td>
                        <td class="border-b px-4 py-2">{{ tutor.year }}</td>
                        <td class="border-b px-4 py-2">
                            <span :class="{ 'text-green-600': tutor.status === 'approved', 'text-red-600': tutor.status === 'rejected' }">
                                 {{ tutor.status.charAt(0).toUpperCase() + tutor.status.slice(1) }}
                            </span>
                        </td>
                        <td class="border-b px-4 py-2">
                            <button :id="`viewBtn-${tutor.id}`" @click="openTdModal(tutor.id)" class="text-blue-500 hover:text-blue-700 underline">
                                View
                            </button>
                        </td>
                        <td class="border-b px-4 py-2">
                            <button :id="`deleteBtn-${tutor.id}`" class="text-red-500 hover:text-red-700 underline" @click="deleteTutor(tutor.id)">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-for="(tutor, index) in processedTutors" :key="tutor.id">
            <TutorDetailsModel
                v-if="TdshowModal && modalTutorId === tutor.id"
                :is-visible="TdshowModal"
                :modal-data="TdmodalData"
                :name="tutor.name"
                :cbnumber="tutor.cbNumber"
                :tutorid="tutor.id"
                :degree="tutor.course" 
                @close="closeTdModal"
            />
        </div>
        </div>
    </AdminLayout>
</template>
