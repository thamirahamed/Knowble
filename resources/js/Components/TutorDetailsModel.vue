
<script setup>
import { ref } from 'vue';
import { defineProps, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';
import { XMarkIcon, ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/solid';

const props = defineProps(
    {
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
    }
);
const emit = defineEmits(['close']);
console.log(props.modalData)
const closeTdModal = () => {
    emit('close');
};

// Reactive State for Module Visibility
const showApprovedModules = ref(true);
const showRejectedModules = ref(false);
const showRejectedReason = ref(false);

</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-md max-w-lg w-full overflow-hidden">
            <div class="flex justify-between items-center py-2 px-6 bg-primary">
                <h2 class="text-lg  font-light text-white">Tutor Details</h2>
                <button id="closeModal" @click="closeTdModal" class="p-1 rounded-full bg-red-500 text-white"><XMarkIcon class="w-4" /></button>
            </div>

            <div class="px-6 pb-4">
                <div class="my-3 text-lg">
                    <p class="font-semibold" >{{ props.name }}</p>
                    <p class="text-gray-700" >{{ props.cbnumber.toUpperCase() }}</p>
                    <p class="text-gray-700" >{{ degree }}</p>
                </div>

                <div>
                    <!-- Approved Modules Section -->
                    <div class="max-h-96 border-t border-gray-300 px-1">
                        <h3
                            id="approvedModulesList"
                            class="my-2 font-medium cursor-pointer flex justify-between"
                            @click="showApprovedModules = !showApprovedModules"
                        >
                            <span>
                                Approved Modules
                            </span>
                            <!-- Conditionally render Chevron icon -->
                            <template v-if="showApprovedModules">
                                <ChevronUpIcon class="w-4" />
                            </template>
                            <template v-else>
                                <ChevronDownIcon class="w-4" />
                            </template>
                        </h3>
                        <ul
                            :class="{
                                'max-h-0 overflow-hidden': !showApprovedModules,
                                'max-h-80 overflow-auto pb-1': showApprovedModules,
                            }"
                            class="transition-all duration-300 ease-in-out px-2 text-gray-700"
                        >
                            <template v-if="modalData.approvedModules && modalData.approvedModules.length">
                                <li
                                    class="mb-2"
                                    v-for="module in modalData.approvedModules"
                                    :key="module.id"
                                >
                                    {{ module.module_name }}
                                </li>
                            </template>
                            <template v-else>
                                <li class="mb-2 italic text-gray-500">No approved modules</li>
                            </template>
                        </ul>
                    </div>

                    <!-- Rejected Modules Section -->
                    <div class="max-h-96 border-t border-gray-300 px-1">
                        <h3
                            id="rejectedModulesList"
                            class="my-2 font-medium cursor-pointer flex justify-between"
                            @click="showRejectedModules = !showRejectedModules"
                        >
                            <span>
                                Rejected Modules
                            </span>
                            <!-- Conditionally render Chevron icon -->
                            <template v-if="showRejectedModules">
                                <ChevronUpIcon class="w-4" />
                            </template>
                            <template v-else>
                                <ChevronDownIcon class="w-4" />
                            </template>
                        </h3>
                        <ul
                            :class="{
                                'max-h-0 overflow-hidden': !showRejectedModules,
                                'max-h-80 overflow-y-auto pb-1': showRejectedModules,
                            }"
                            class="transition-all duration-300 ease-in-out px-2 text-gray-700"
                        >
                            <template v-if="modalData.rejectedModules && modalData.rejectedModules.length">
                                <li
                                    class="mb-2"
                                    v-for="module in modalData.rejectedModules"
                                    :key="module.id"
                                >
                                    {{ module.module_name }}
                                </li>
                            </template>
                            <template v-else>
                                <li class="mb-2 italic text-gray-500">No rejected modules</li>
                            </template>
                        </ul>
                    </div>

                    <!-- Rejected Reason Section -->
                    <div class="max-h-48 border-t border-gray-300 px-1">
                        <h3
                            id="rejectedReasonLbl"
                            class="my-2 font-medium cursor-pointer flex justify-between"
                            @click="showRejectedReason = !showRejectedReason"
                        >
                            <span>
                                Reason for Rejection
                            </span>
                            <!-- Conditionally render Chevron icon -->
                            <template v-if="showRejectedReason">
                                <ChevronUpIcon class="w-4" />
                            </template>
                            <template v-else>
                                <ChevronDownIcon class="w-4" />
                            </template>
                        </h3>
                        <p
                            :class="{
                                'max-h-0 overflow-hidden': !showRejectedReason,
                                'max-h-40 overflow-y-auto pb-2': showRejectedReason,
                            }"
                            class="transition-all duration-300 ease-in-out px-2 text-gray-700"
                        >
                            <!-- {{ modalData.rejectedreason[0].message }} -->
                            blank
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
