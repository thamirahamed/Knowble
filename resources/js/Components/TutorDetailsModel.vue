<script setup>
import { ref } from 'vue';
import { defineProps, defineEmits } from 'vue';
import { router } from '@inertiajs/vue3';

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

const closeTdModal = () => {
    emit('close');
};

</script>
<template>
    <div v-if="isVisible" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">Tutor Details</h2>
                <button @click="closeTdModal" class="close-btn p-2 rounded-full text-white">X</button>
            </div>
            <div class="mt-4 text-2xl">
                <p>{{ props.name }}</p>
                <p>{{ props.cbnumber }}</p>
            </div>

            <div class="mt-4">
                <div class="modal-body overflow-y-auto max-h-96">
                    <h3 class="my-3 font-bold">Approved Modules</h3>
                    <ul>
                        <li class="my-3" v-for="module in props.modalData.approvedModules" :key="module.id">
                            {{ module.module_name }}
                        </li>
                    </ul>
                    <h3 class="my-3 font-bold">Rejected Modules</h3>
                    <ul>
                        <li class="my-3" v-for="module in props.modalData.rejectedModules" :key="module.id">
                            {{ module.module_name }}
                        </li>
                    </ul>
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
        z-index: 9999;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .close-btn {
        background-color: #f56565;
    }
    .modal-body {
        overflow-y: auto;
        max-height: 20rem;
        padding-right: 0.5rem;
    }
</style>
