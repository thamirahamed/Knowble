<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { ChevronDownIcon, ChevronUpIcon } from "@heroicons/vue/24/solid";
import PrimaryButton from "@/Components/PrimaryButton.vue";

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
    tutorsAvailableTimes: {
        type: Array,
        required: true,
    },

});

const showRejectedModules = ref(false);
const successMessage = ref(""); // State to track the success message

// Days of the week
const daysOfWeek = [
    { id: 1, name: "Monday" },
    { id: 2, name: "Tuesday" },
    { id: 3, name: "Wednesday" },
    { id: 4, name: "Thursday" },
    { id: 5, name: "Friday" },
    { id: 6, name: "Saturday" },
    { id: 7, name: "Sunday" },
];

// State to track time slots
const timeSlots = ref(
    daysOfWeek.map((day) => ({
        day: day.name,
        isActive: false,
        fromTime: "",
        toTime: "",
    }))
);

// Prefill time slots on mount
onMounted(() => {
    props.tutorsAvailableTimes.forEach((prefilled) => {
        const slot = timeSlots.value.find((s) => s.day === prefilled.day);
        if (slot) {
            slot.isActive = true;
            slot.fromTime = prefilled.start_time || "";
            slot.toTime = prefilled.end_time || "";
        }
    });
});

// Keep track of days that need deletion
const removedDays = ref([]);

// Watch for checkbox uncheck to track removals
const toggleDay = (slot) => {
    if (!slot.isActive) {
        removedDays.value.push(slot.day); // Add to removed days
    } else {
        removedDays.value = removedDays.value.filter((day) => day !== slot.day); // Remove if re-checked
    }
};

// State to keep track of validation results for each day
const validateResult = ref({});

// Function to validate each time slot when From or To time inputs are blurred
const validateAndUpdate = (slot) => {
    // Only validate toggled (active) slots
    if (!slot.isActive) return;

    const { isValid, errorMessage } = validateTimes(slot);
    validateResult.value[slot.day] = { isValid, errorMessage };
};

const submitSessions = () => {
    let allValid = true;

    // Validate only active slots
    timeSlots.value.forEach((slot) => {
        if (slot.isActive) {
            const { isValid, errorMessage } = validateTimes(slot);
            validateResult.value[slot.day] = { isValid, errorMessage };
            if (!isValid) allValid = false;
        }
    });

    if (!allValid) {
        console.log("Form contains errors. Please fix them before submitting.");
        return;
    }

    // Proceed with submission if all validations are passed
    const payload = timeSlots.value
        .filter((slot) => slot.isActive)
        .map((slot) => ({
            day: slot.day,
            start_time: slot.fromTime,
            end_time: slot.toTime,
        }));
    router.post("/tutor/available-times", { sessions: payload }, {
        onSuccess: () => {
            console.log("Available times saved successfully!");
            successMessage.value = "Available times have been saved successfully!"; // Set success message
        },
    });
};

const validateTimes = (slot) => {
    // Only validate if the slot is active
    if (!slot.isActive) return { isValid: true, errorMessage: "" };

    const fromTime = slot.fromTime ? new Date(`1970-01-01T${slot.fromTime}:00`) : null;
    const toTime = slot.toTime ? new Date(`1970-01-01T${slot.toTime}:00`) : null;
    let isValid = true;
    let errorMessage = "";

    if (!fromTime || !toTime) {
        isValid = false;
        errorMessage = "Both 'From' and 'To' times must be filled.";
    } else {
        const diffHours = (toTime - fromTime) / (1000 * 60 * 60);
        if (diffHours < 1) {
            isValid = false;
            errorMessage = "Time frame must be at least 1 hour.";
        } else if (diffHours > 6) {
            isValid = false;
            errorMessage = "Time frame cannot exceed 6 hours.";
        }
    }

    return { isValid, errorMessage };
};


// Emits
const emit = defineEmits(["tutorRequest", "selectionChange"]);

// State to track selected modules
const selectedModules = ref(new Set());

// Initialize selected modules based on unique `tutorsSelectedModules` IDs
onMounted(() => {
    const uniqueModules = new Set(props.tutorsSelectedModules.map((module) => module.id));
    selectedModules.value = uniqueModules; // Set filtered module IDs
});
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

</script>

<template>
    <div class="flex flex-col w-full">
        <!-- Approved Modules -->
        <div class="mb-4">
            <h1 class="text-xl font-semibold">Modules</h1>
            <p class="text-gray-500 mb-2">Select from a list of approved modules you're qualified to tutor and focus on what you do best.</p>
            <div
                v-for="module in approvedModules"
                :key="module.id"
                class="px-4 mb-2 flex justify-between items-center"
            >
                <p class="text-gray-800 text-lg">{{ module.module_name }}</p>
                <div
                    :id="'toggleBtn-module-' + module.id"
                    :class="selectedModules.has(module.id) ? 'bg-accent hover:bg-accentdark' : 'bg-gray-300 hover:bg-accent/50'"
                    class="relative inline-block w-12 h-6 rounded-full transition duration-200 cursor-pointer shadow-[inset_rgba(50,50,93,0.15)_0px_30px_60px_-12px,_inset_rgba(0,0,0,0.2)_0px_18px_36px_-18px]"
                    @click="toggleApproval(module.id)"
                >
                    <div
                        class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform duration-200"
                        :class="selectedModules.has(module.id) ? 'transform translate-x-6' : 'transform translate-x-0'"
                    ></div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h1 class="text-xl font-semibold">Session Availability</h1>
            <p class="text-gray-500 mb-3">Select from a list of approved modules you're qualified to tutor and focus on what you do best.</p>
            <!-- Day-wise Time Slots -->
            <div v-for="(slot, index) in timeSlots" :key="index" class="flex items-center justify-between mb-3 px-8">
                <div class="flex items-center">
                    <!-- Checkbox -->
                    <!-- <input
                        id="selectBtn-module-${subject.id}"
                        type="checkbox"
                        v-model="slot.isActive"
                        class="mr-4 w-5 h-5"
                    /> -->
                    <!-- Switch Button -->
                    <button
                        :id="'toggleBtn-slot-' + slot.day"
                        :class="slot.isActive ? 'bg-accent hover:bg-accentdark' : 'bg-gray-300 hover:bg-accent/50'"
                        class="relative inline-block w-12 h-6 rounded-full transition duration-200 shadow-[inset_rgba(50,50,93,0.15)_0px_30px_60px_-12px,_inset_rgba(0,0,0,0.2)_0px_18px_36px_-18px] cursor-pointer"
                        @click="slot.isActive = !slot.isActive; toggleDay(slot)"
                    >
                        <div
                            class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform duration-200"
                            :class="slot.isActive ? 'transform translate-x-6' : 'transform translate-x-0'"
                        ></div>
                    </button>

                    <label class="w-24 ml-4">{{ slot.day }}</label>
                </div>

                <!-- Time Inputs -->
                <div class="flex gap-4 items-center">
                    <div :class="!slot.isActive ? 'text-gray-400 cursor-not-allowed' : ''">
                        <label class="text-sm block">From</label>
                        <input
                            :id="'from-time-' + slot.day"
                            type="time"
                            v-model="slot.fromTime"
                            class="border p-1 rounded"
                            :disabled="!slot.isActive"
                            :class="!slot.isActive ? 'border-gray-400' : ''"
                        />
                        <!-- Show error message only for active slots -->
                        <div v-if="slot.isActive && validateResult[slot.day] && !validateResult[slot.day].isValid" class="text-red-500 text-xs">
                            {{ validateResult[slot.day].errorMessage }}
                        </div>
                    </div>

                    <div :class="!slot.isActive ? 'text-gray-400 cursor-not-allowed' : ''">
                        <label class="text-sm block">To</label>
                        <input
                            :id="'to-time-' + slot.day"
                            type="time"
                            v-model="slot.toTime"
                            class="border p-1 rounded"
                            :disabled="!slot.isActive"
                            :class="!slot.isActive ? 'border-gray-400' : ''"
                        />
                        <!-- Show error message only for active slots -->
                        <div v-if="slot.isActive && validateResult[slot.day] && !validateResult[slot.day].isValid" class="text-red-500 text-xs">
                            {{ validateResult[slot.day].errorMessage }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 px-8 flex gap-3 items-center justify-end">
                <div v-if="successMessage" class="text-green-500 text-sm">
                    {{ successMessage }}
                </div>
                <PrimaryButton
                    id="saveTimeBtn"
                    @click="submitSessions"
                >
                    Save Available Times
                </PrimaryButton>
            </div>
        </div>

        <template v-if="rejectedModules.length">

            <div class="border-t border-gray-300 px-1 mt-4">
                <!-- Header with Chevron Icon -->
                <h3
                    id="rejectedModulesList"
                    class="mt-2 text-xl font-semibold cursor-pointer flex justify-between items-center"
                    @click="showRejectedModules = !showRejectedModules"
                >
                    <span>Rejected Modules</span>
                    <template v-if="showRejectedModules">
                        <ChevronUpIcon class="w-5 text-gray-700" />
                    </template>
                    <template v-else>
                        <ChevronDownIcon class="w-5 text-gray-700" />
                    </template>
                </h3>
                <p class="text-gray-500 mb-2">View your rejected modules</p>


                <!-- Rejected Modules List -->
                <div
                    :class="{
                        'max-h-0 overflow-hidden': !showRejectedModules,
                        'max-h-fit overflow-y-auto pb-1': showRejectedModules,
                    }"
                    class="transition-all duration-300 ease-in-out px-4 text-gray-800"
                >
                    <div class="flex flex-col overflow-y-auto max-h-72">
                        <!-- Loop through rejected modules -->
                        <div
                            v-for="module in rejectedModules"
                            :key="module.id"
                            class="mb-2 flex justify-between items-center"
                        >
                            <p class="text-gray-800 text-lg">{{ module.module_name }}</p>
                        </div>
                    </div>

                    <!-- Reason for Rejection -->
                    <div class="mb-4 mt-3">
                        <h1 class="text-xl font-semibold -ml-4 text-gray-950">Feedback Notes</h1>
                        <p class="text-gray-500 mb-2 -ml-4">Feedback from the admin to understand areas for improvement.</p>
                        <p class="text-gray-7=800 text-lg">"{{ rejectedReason.message }}"</p>
                    </div>
                </div>
            </div>
        </template>
        <!-- No Rejected Modules -->
        <template v-else>
            <div class="mb-2 italic text-gray-500">
                No rejected modules
            </div>

            <!-- Reason for Rejection -->
            <div>
                <h1 class="text-xl font-semibold">Feedback Notes</h1>
                <p class="text-gray-500 mb-2">Feedback from the admin to understand areas for improvement.</p>
                <p class="text-gray-800 text-lg">{{ rejectedReason.message }}</p>
            </div>
        </template>
    </div>
</template>
