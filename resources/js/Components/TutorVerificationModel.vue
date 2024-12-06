<script setup>
import { defineProps, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    isVisible: {
        type: Boolean,
        required: true,
    },
    modalData: {
        type: Object,
        default: null,
    },
    name: {
        type: String,
        default: 'modal',
    },
    cbnumber: {
        type: String,
        default: 'cb-number',
    },
    tutorid : {
        type: Number,
        default: 0,
    }
});
const emit = defineEmits(['close']);

const closeModal = () => {
    const unapprovedModuleIds = []; // Collect only unapproved module IDs

    Object.values(props.modalData).forEach(level => {
        Object.values(level).forEach(semesterList => {
            semesterList.forEach(semester => {
                semester[Object.keys(semester)[0]].forEach(module => {
                    // Only add unapproved modules
                    if (!module.approved) {
                        unapprovedModuleIds.push(module.id);
                    }
                });
            });
        });
    });

    if (unapprovedModuleIds.length > 0) {
        rejectAllModules(unapprovedModuleIds, props.tutorid);
    }

    emit('close'); // Close the modal after processing
};


const toggleApproval = (subject, tutorId) => {
    if (!subject.approved) {
        approveModule(subject.id, tutorId);
        subject.approved = true;
    }
};


const approveModule = (subjectId, tutorId) => {
    console.log(`Approving module with ID ${subjectId} for Tutor with ID ${tutorId}...`);
    router.post(`/approve-tutor/${subjectId}`,
        { tutor_id: tutorId },
        // {headers : {'Content-Type': 'application/json'}},
        {
        onSuccess: () => {
            console.log(`Module with ID ${subjectId} for Tutor with ID ${tutorId} approved successfully!`);
        }
    });
};

const rejectAllModules = (moduleIds, tutorId) => {
    router.post('/reject-tutor', { module_ids: moduleIds, tutor_id: tutorId }, {
        onSuccess: () => {
            console.log(`Modules rejected successfully!`);
        },
        onError: (error) => {
            console.error('Error rejecting modules:', error);
        },
    });
};
</script>

<template>
    <div v-if="isVisible" class="modal-overlay">
        <div class="modal-content bg-white p-6 rounded-lg max-w-2xl w-full shadow-lg">
            <button class="close-btn bg-red-500 text-white px-4 py-2 rounded mb-4" @click="closeModal">Close</button>

            <h3 class="text-2xl font-semibold mb-2">{{ props.name }}</h3>
            <h2 class="text-xl font-semibold mb-2">{{ props.cbnumber }}</h2>

            <!-- Scrollable Content -->
            <div class="modal-body overflow-y-auto max-h-96">
                <div v-for="(level, levelIndex) in modalData" :key="levelIndex">
                    <h4 class="text-xl font-semibold mb-2">{{ Object.keys(level)[0] }}</h4>
                    <div v-for="(semester, semesterIndex) in level[Object.keys(level)[0]]" :key="semesterIndex">
                        <h5 class="text-lg font-medium mb-2">{{ Object.keys(semester)[0] }}</h5>

                        <ul class="list-disc pl-5">
                            <!-- Check if the semester array is non-empty -->
                            <li
                                v-for="subject in semester[Object.keys(semester)[0]]"
                                :key="subject.id"
                            class="mb-1 flex justify-between items-center"
                            >
                            <span>{{ subject.module_name }}</span>

                            <button
                                :class="subject.approved ? 'bg-green-500' : 'bg-blue-500'"
                                class="text-white px-4 py-2 rounded"
                                @click="toggleApproval(subject, props.tutorid)"
                            >
                                {{ subject.approved ? 'Approved!' : 'Approve' }}
                            </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    max-height: 90%; /* Ensures the modal doesn't grow too large */
    overflow: hidden; /* Prevents content from overflowing */
}

.modal-body {
    overflow-y: auto; /* Enables vertical scrolling */
    max-height: 20rem; /* Adjust based on your needs */
    padding-right: 0.5rem; /* Adds space for scrollbar */
}
</style>
