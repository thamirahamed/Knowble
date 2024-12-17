<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

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

// Function to submit available times
const submitSessions = () => {
    const payload = timeSlots.value
        .filter((slot) => slot.isActive)
        .map((slot) => ({
            day: slot.day,
            start_time: slot.fromTime,
            end_time: slot.toTime,
        }));
    // Send active days to store
    router.post("/tutor/available-times", { sessions: payload }, {
        onSuccess: () => console.log("Available times saved successfully!"),
    });

};
</script>


<template>
    <div class="p-6 bg-white rounded-md shadow">
        <h1 class="text-2xl font-semibold mb-4">Set Your Available Time</h1>

        <!-- Day-wise Time Slots -->
        <div v-for="(slot, index) in timeSlots" :key="index" class="flex items-center mb-4">
            <label class="w-24 font-medium">{{ slot.day }}</label>
            <!-- Checkbox -->
            <input
                type="checkbox"
                v-model="slot.isActive"
                class="mr-4 w-5 h-5"
            />

            <!-- Time Inputs -->
            <div v-if="slot.isActive" class="flex gap-4 items-center">
                <div>
                    <label class="text-sm block">From</label>
                    <input
                        type="time"
                        v-model="slot.fromTime"
                        class="border p-1 rounded"
                    />
                </div>

                <div>
                    <label class="text-sm block">To</label>
                    <input
                        type="time"
                        v-model="slot.toTime"
                        class="border p-1 rounded"
                    />
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button
                @click="submitSessions"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
            >
                Save Available Times
            </button>
        </div>
    </div>
</template>

<style scoped>
input[type="time"] {
    width: 120px;
}
</style>
