<script setup>
import { ref } from 'vue';
import { defineProps, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';
import { Inertia } from "@inertiajs/inertia";
import PrimaryButton from './PrimaryButton.vue';
import { XMarkIcon, CheckIcon } from '@heroicons/vue/24/solid';

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
    degree: {
        type: String,
        default: "",
    }
});
const emit = defineEmits(['close']);

// Reactive Variables
const rejectionReason = ref(''); // To store the rejection reason
const rejectionError = ref(false); // To track if there’s an error in rejection reason

// Methods
const closeModal = () => {
    emit('close');
}

const submitModal = async () => {
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

    // Validation: Ensure rejection reason is filled if there are unapproved modules
    if (unapprovedModuleIds.length > 0 && !rejectionReason.value.trim()) {
        rejectionError.value = true; // Set error flag to true
        return; // Stop further execution
    }

    try {
        // Send combined request for approval and rejection
        await router.post('/process-tutor', {
            approved_module_ids: approvedModuleIds,
            unapproved_module_ids: unapprovedModuleIds,
            tutor_id: props.tutorid,
            rejection_reason: rejectionReason.value, // Include reason if modules are rejected
        });

        // Clear error and close modal on success
        rejectionError.value = false;
        emit('close');
    } catch (error) {
        console.error('Error processing modules:', error);
    }
};

const toggleApproval = (subject, tutorId) => {
    subject.approved = !subject.approved;
};

</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full shadow-lg overflow-hidden">
            <div class="flex justify-between items-center py-2 px-6 bg-primary">
                <h2 class="text-lg text-white font-light">Tutor Details</h2>
                <button id="closeModal" @click="closeModal" class="p-1 rounded-full bg-red-500 text-white"><XMarkIcon class="w-4" /></button>
            </div>
            <div class="px-6 pb-4">
                <div class="my-3 text-lg">
                    <p class="font-semibold">{{ name }}</p>
                    <p class="text-gray-700" >{{ cbnumber.toUpperCase() }}</p>
                    <p class="text-gray-700" >{{ degree }}</p>
                </div>

                <!-- Scrollable Content -->
                <div class="overflow-y-auto max-h-96 pr-2 border-y border-gray-300 py-2">
                    <div v-for="(level, levelIndex) in modalData" :key="levelIndex" class="mb-4">
                        <h4 class="text-lg font-semibold mb-1">{{ Object.keys(level)[0] }}</h4>
                        <div v-for="(semester, semesterIndex) in level[Object.keys(level)[0]]" :key="semesterIndex">
                            <h5 class="text-base font-medium mb-1 pl-2">{{ Object.keys(semester)[0] }}</h5>

                            <ul class="list-disc px-4">
                                <li
                                    v-for="subject in semester[Object.keys(semester)[0]]"
                                    :key="subject.id"
                                    class="mb-1 flex justify-between items-center"
                                >
                                    <span class="text-gray-800">{{ subject.module_name }}</span>
                                    <button
                                        :id="`approveBtn-module-${subject.id}`" 
                                        :class="subject.approved ? 'bg-accent hover:bg-accentdark' : 'bg-gray-300 hover:bg-accent/50'"
                                        class="text-white p-1.5 rounded-full transition duration-200"
                                        @click="toggleApproval(subject, tutorid)"
                                    >
                                        <CheckIcon class="w-4" />
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Rejection Reason -->
                <div class="my-2 flex flex-col items-center">
                    <p class="italic text-sm text-gray-600 mb-3">Unticked modules will be considered as rejected for tutoring by default when submitted.</p>
                    <textarea
                        id="rejectionReasonInput"
                        v-model="rejectionReason"
                        :class="rejectionError ? 'border-red-300 hover:border-red-500 focus:ring-red-500' : 'border-gray-300 hover:border-slate-500 focus:ring-slate-500'"
                        class="w-full px-2 -py-2 rounded-md  text-lg shadow-sm focus:border-transparent focus:outline-none focus:ring-2"
                        placeholder="Enter feedback..."
                        rows="2"
                    ></textarea>
                    <!-- Error Message -->
                    <p v-if="rejectionError" class="text-red-500 text-sm mt-1">Feedback is required.</p>
                </div>

                <!-- Close Button -->
                <div class="text-right">
                    <PrimaryButton
                        id="submitApproval"
                        @click="submitModal"
                    >
                        Submit
                    </PrimaryButton>
                </div>
            </div>
            
        </div>
    </div>
</template>

