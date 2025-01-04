<script setup>
import { router } from "@inertiajs/vue3";
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

// Props
const props = defineProps({
    bookings: Array,      // Array of booked sessions
});

console.log(JSON.stringify(props.bookings, null, 2))

// Redirect to Join Meeting with the meeting_url from a specific booking
const joinMeeting = (booking) => {
    // Get the base meeting URL from the booking
    let meetingUrl = booking.meeting_url;
    
    // Create the tutor's name (convert to lowercase and replace spaces with hyphens)
    const tutorName = booking.tutor.replace(" ", "-").toLowerCase();  // Example: Emily Davis -> emily-davis
    
    // append the 'name' parameter
    meetingUrl += `&name=${tutorName}`;

    router.visit(route("meetings.index", { meetingUrl, id: booking.id }));
};

// Reject Request
const rejectRequest = (id) => {
    router.post(route("request.session.cancel", { id }), {}, {
        onSuccess: () => {
            enhancedRequests.value = enhancedRequests.value.map((request) =>
                request.id === id ? { ...request, status: "rejected" } : request
            );
        },
    });
};

// Function to format date to words with suffix
const formatDateToWords = (date) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  const d = new Date(date);
  const day = d.getDate();
  const suffix = getDaySuffix(day);
  
  const formatter = new Intl.DateTimeFormat('en-US', options);
  return `${formatter.format(d).replace(day, day + suffix)}`;
};

// Function to get the day suffix (st, nd, rd, th)
const getDaySuffix = (day) => {
  const j = day % 10;
  const k = day % 100;
  if (j === 1 && k !== 11) {
    return 'st';
  }
  if (j === 2 && k !== 12) {
    return 'nd';
  }
  if (j === 3 && k !== 13) {
    return 'rd';
  }
  return 'th';
};

</script>

<template>
    <div class="flex flex-col w-full">
        <h1 class="text-xl font-bold text-gray-800 mb-4">Upcoming Bookings</h1>

        <div v-if="bookings.length > 0" class="space-y-4 mb-4">
            <div
                v-for="booking in bookings"
                :key="booking.id"
                class="flex justify-between items-center bg-accentdark/5 p-4 rounded-md shadow-md" 
            >
                <div class="flex items-center gap-4">
                    <img
                        :src="booking.profile_pic"
                        alt="Profile Picture"
                        class="w-16 h-16 rounded-full object-cover"
                    />
                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ booking.user }}</p>
                        <p class="text-lg text-gray-600">{{ booking.degree }}</p>
                        <p class="text-lg text-gray-600">{{ booking.module_name }}</p>
                        <p class="text-lg text-gray-600">
                            {{ formatDateToWords(booking.session_date) }} | {{ booking.start_time }} - {{ booking.end_time }}
                        </p>
                        <p v-if="booking.notes " class="text-lg text-gray-600">Notes: {{ booking.notes }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <PrimaryButton
                        :id="'joinSession-' + booking.id" 
                        @click="joinMeeting(booking)"
                    >
                        Join Now
                    </PrimaryButton>
                    <DangerButton>
                        Cancel
                    </DangerButton>
                    <!-- <span
                        v-if="request.status === 'accepted' && request.meeting_url"
                        class="font-semibold text-green-500"
                    >
                        Approved
                    </span>
                    <button
                        v-if="request.status === 'accepted' && request.meeting_url"
                        @click="joinMeeting(request.meeting_url)"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md"
                    >
                        Join Meeting
                    </button>

                    <button
                        v-if="request.status === 'pending'"
                        @click="approveRequest(request.id, request.tutor_name)"
                        id="requestApproveBtn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition"
                    >
                        Approve
                    </button>
                    <button
                        v-if="request.status === 'pending'"
                        @click="rejectRequest(request.id)"
                        id="requestRejectBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition"
                    >
                        Reject
                    </button> -->
                </div>
            </div>
        </div>

        <div v-else class="text-center text-gray-500 py-6 mb-4">
            No bookings at the moment.
        </div>
    </div>
</template>
