
<script setup>
import { ref } from 'vue';
import { defineProps, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';
import { XMarkIcon } from '@heroicons/vue/24/solid';

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
    }
);
const emit = defineEmits(['close']);
console.log(props.modalData)
const closeTdModal = () => {
    emit('close');
};

</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-md max-w-lg w-full">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">Tutor Details</h2>
                <button @click="closeTdModal" class="p-1 rounded-full bg-red-500 text-white"><XMarkIcon class="w-4" /></button>
            </div>
            <div class="mt-2 text-lg">
                <p>{{ props.name }}</p>
                <p>{{ props.cbnumber.toUpperCase() }}</p>
            </div>

            <div class="mt-1">
                <div class="overflow-y-auto max-h-96 border-t border-gray-300 px-2">
                    <h3 class="my-2 font-bold">Approved Modules</h3>
                    <ul>
                        <li class="my-1" v-for="module in props.modalData.approvedModules" :key="module.id">
                            {{ module.module_name }}
                        </li>
                    </ul>
                </div>
                <div class="overflow-y-auto max-h-96 border-t border-gray-300 px-2">
                    <h3 class="my-2 font-bold">Rejected Modules</h3>
                    <ul>
                        <li class="my-1" v-for="module in props.modalData.rejectedModules" :key="module.id">
                            {{ module.module_name }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-1">
                <p v-for="module in props.modalData.rejectedModules" :key="module.id">
                    {{ module.rejection_reason }}
                </p>
            </div>
        </div>
    </div>
</template>
