<script setup>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { CheckIcon } from "@heroicons/vue/24/solid/index.js";
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";

// Props
const props = defineProps({
    approvedModules: {
        type: Array,
        required: true,
    },
    rejectedModules: {
        type: Array,
        required: true,
    },
    rejectedReason: {
        type: Object,
        required: true,
    },
    tutorsSelectedModules: {
        type: Array,
        required: true,
    },
});

// Emits
const emit = defineEmits(["tutorRequest", "selectionChange"]);

// State to track selected modules
const selectedModules = ref(new Set());

// Initialize selected modules based on unique `tutorsSelectedModules` IDs
onMounted(() => {
    const uniqueModules = new Set(props.tutorsSelectedModules.map((module) => module.id));
    selectedModules.value = uniqueModules; // Set filtered module IDs
});

// Log the unique modules for debugging
console.log("Unique Selected Modules:", Array.from(selectedModules.value));

// Function to toggle module selection
const toggleApproval = (moduleId) => {
    if (selectedModules.value.has(moduleId)) {
        selectedModules.value.delete(moduleId); // Remove if already selected
        removeSelectedModule(moduleId); // Call function to remove module
    } else {
        selectedModules.value.add(moduleId); // Add to selected modules
        selectModule(moduleId); // Call function to approve module
    }
    emit("selectionChange", Array.from(selectedModules.value)); // Notify parent of changes
};

// Function to approve module
const selectModule = (id) => {
    router.post(`/tutor/select/${id}`, {}, {
        onSuccess: () => {
            console.log("Module approved:", id);
        },
    });
};

// Function to remove selected module
const removeSelectedModule = (id) => {
    router.post(`/tutor/remove/${id}`, {}, {
        onSuccess: () => {
            console.log("Module removed:", id);
        },
    });
};

// Function to request approval for rejected modules
const requestTutor = (id) => {
    emit("tutorRequest", id);
};
</script>

<template>
    <div class="flex flex-col w-full">
        <!-- Approved Modules -->
        <div class="mb-4">
            <h1 class="text-xl font-semibold">Approved Modules</h1>
            <div
                v-for="module in approvedModules"
                :key="module.id"
                class="my-2 flex justify-between items-center"
            >
                <p class="text-gray-700 text-lg">{{ module.module_name }}</p>
                <button
                    :id="`approveBtn-module-${module.id}`"
                    :class="selectedModules.has(module.id)
                        ? 'bg-green-500 hover:bg-green-600'
                        : 'bg-gray-300 hover:bg-gray-400'"
                    class="text-white p-1.5 rounded-full transition duration-200"
                    @click="toggleApproval(module.id)"
                >
                    <CheckIcon class="w-4" />
                </button>
            </div>
        </div>

        <!-- Rejected Modules -->
        <div class="mb-4">
            <h1 class="text-xl font-semibold">Rejected Modules</h1>
            <div
                v-for="module in rejectedModules"
                :key="module.id"
                class="my-2 flex justify-between items-center"
            >
                <p class="text-gray-700 text-lg">{{ module.module_name }}</p>
                <SecondaryButton @click="requestTutor(module.id)">
                    Request Again
                </SecondaryButton>
            </div>
        </div>

        <!-- Reason for Rejection -->
        <div class="mb-4">
            <h1 class="text-xl font-semibold">Reason for Rejection</h1>
            <p class="text-gray-700 text-lg">{{ rejectedReason.message }}</p>
        </div>
    </div>
</template>
