<template>
    <div v-if="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b pb-3">
                <h2 class="text-xl font-semibold">Request a Session</h2>
                <button id="clickCloseBtn" @click="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4">
                <div class="mb-4">
                    <label for="session-date" class="block text-sm font-medium text-gray-700">Select Date</label>
                    <input
                        type="date"
                        id="session-date"
                        v-model="sessionDate"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>

                <div class="mb-4">
                    <label for="start-time" class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input
                        type="time"
                        id="start-time"
                        v-model="startTime"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>

                <div class="mb-4">
                    <label for="end-time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input
                        type="time"
                        id="end-time"
                        v-model="endTime"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    />
                </div>

                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes
                        (Optional)</label>
                    <textarea
                        id="notes"
                        v-model="sessionNotes"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Any additional details for the tutor"
                    ></textarea>
                </div>

                <div class="mt-6">
                    <button
                        @click="submitSessionRequest" id="submitRequestBtn"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Submit Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    openModal: Boolean,
    tutorid: Number,
    closeModal: Function,
});


const sessionDate = ref('');
const startTime = ref('');
const endTime = ref('');
const sessionNotes = ref('');

const submitSessionRequest = () => {
    if (!sessionDate.value || !startTime.value || !endTime.value) {
        alert('Please select date, start time, and end time for the session.');
        return;
    }

    const requestData = {
        tutorId: props.tutorid, // Assuming modalData contains tutor details
        date: sessionDate.value,
        startTime: startTime.value,
        endTime: endTime.value,
        notes: sessionNotes.value,
    };

    // Send the request (adjust according to your API setup)
    router.post('/tutor/sessions/request', requestData, {
        onSuccess: () => {
            alert('Session request submitted successfully!');
            closeModal();
        },
        onError: (error) => {
            console.error('Failed to submit session request:', error);
        },
    });
};
</script>

<style scoped>
/* Add any custom styles if needed */
</style>
