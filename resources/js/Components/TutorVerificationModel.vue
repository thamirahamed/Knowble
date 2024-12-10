<script setup>
import { ref } from 'vue';
import { defineProps, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';
import { Inertia } from "@inertiajs/inertia";

// Props and Emits
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
    tutorid: {
        type: Number,
        default: 0,
    },
});
const emit = defineEmits(['close']);

// Reactive Variables
const rejectionReason = ref(''); // To store the rejection reason

// Methods
const closeModal = () => {
    const unapprovedModuleIds = [];
    const approvedModuleIds = [];

    Object.values(props.modalData).forEach(level => {
        Object.values(level).forEach(semesterList => {
            semesterList.forEach(semester => {
                semester[Object.keys(semester)[0]].forEach(module => {
                    if (!module.approved) {
                        unapprovedModuleIds.push(module.id);
                    }
                    if (module.approved) {
                        approvedModuleIds.push(module.id);
                    }
                });
            });
        });
    });
    console.log(approvedModuleIds.length > 0);
    if (approvedModuleIds.length > 0) {
        approveModule(approvedModuleIds, props.tutorid);
    }
    if (unapprovedModuleIds.length > 0) {
        rejectAllModules(unapprovedModuleIds, props.tutorid, rejectionReason.value);
    }


    emit('close'); // Close the modal
};

const toggleApproval = (subject, tutorId) => {
    subject.approved = !subject.approved;
};

const approveModule = (subjectIds, tutorId) => {
    console.log('Calling API with:', { subject_ids: subjectIds, tutor_id: tutorId });

    Inertia.post('/approve-tutor', { subject_ids: subjectIds, tutor_id: tutorId }, {
        onSuccess: () => {
            console.log('API Call Successful');
        },
        onError: (error) => {
            console.error('API Call Failed:', error);
        },
    });
};


const rejectAllModules = (moduleIds, tutorId, reason) => {
    router.post('/reject-tutor', { module_ids: moduleIds, tutor_id: tutorId, reason }, {

        onSuccess: () => {
            console.log(`Modules rejected successfully with reason: "${reason}".`);
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
            <h3 class="text-2xl font-semibold mb-2">{{ name }}</h3>
            <h2 class="text-xl font-semibold mb-2">{{ cbnumber }}</h2>

            <!-- Scrollable Content -->
            <div class="modal-body overflow-y-auto max-h-96">
                <div v-for="(level, levelIndex) in modalData" :key="levelIndex">
                    <h4 class="text-xl font-semibold mb-2">{{ Object.keys(level)[0] }}</h4>
                    <div v-for="(semester, semesterIndex) in level[Object.keys(level)[0]]" :key="semesterIndex">
                        <h5 class="text-lg font-medium mb-2">{{ Object.keys(semester)[0] }}</h5>

                        <ul class="list-disc pl-5">
                            <li
                                v-for="subject in semester[Object.keys(semester)[0]]"
                                :key="subject.id"
                                class="mb-1 flex justify-between items-center"
                            >
                                <span>{{ subject.module_name }}</span>
                                <button
                                    :class="subject.approved ? 'bg-green-500' : 'bg-blue-500'"
                                    class="text-white px-4 py-2 rounded"
                                    @click="toggleApproval(subject, tutorid)"
                                >
                                    {{ subject.approved ? 'Approved!' : 'Approve' }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Rejection Reason -->
            <div class="my-3">
                <textarea
                    v-model="rejectionReason"
                    class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Enter reason for rejection..."
                    rows="3"
                ></textarea>
            </div>

            <!-- Close Button -->
            <div class="text-right">
                <button
                    class="close-btn bg-red-500 text-white px-4 py-2 rounded"
                    @click="closeModal"
                >
                    Close
                </button>
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
    max-height: 90%;
    overflow: hidden;
}

.modal-body {
    overflow-y: auto;
    max-height: 20rem;
    padding-right: 0.5rem;
}
</style>
