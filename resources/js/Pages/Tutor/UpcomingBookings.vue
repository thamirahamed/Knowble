<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import CancelSession from "@/Components/CancelSession.vue";

// Props
const props = defineProps({
    bookings: Array,      // Array of booked sessions
});

const openModal = ref(null);
const selectedBooking = ref(null);

const closeModal = () => {
    openModal.value = null;
    selectedBooking.value = null;
};

const openModalWithData = (booking) => {
    selectedBooking.value = booking;
    openModal.value = true;
};

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
        <h1 class="text-xl font-bold text-gray-800">Upcoming Bookings</h1>
        <p class="text-gray-500 mb-4">View your upcoming sessions, where you can join the meeting or cancel it.</p>

        <div v-if="bookings.length > 0" class="space-y-4 mb-4">
            <div
                v-for="booking in bookings"
                :key="booking.id"
                class="flex justify-between items-center bg-secondary/5 p-4 rounded-md shadow-md" 
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

                <div class="flex flex-col gap-2">
                    <PrimaryButton
                        :id="'joinSession-' + booking.id" 
                        @click="joinMeeting(booking)"
                    >
                        Join Now
                    </PrimaryButton>
                    <DangerButton
                        :id="'cancelSession-' + booking.id"
                        @click="openModalWithData(booking)"
                    >
                        Cancel
                    </DangerButton>
                </div>
            </div>
        </div>

        <div v-else class="text-center text-gray-500 py-6 mb-4">
            No bookings at the moment.
        </div>

        <CancelSession
            :openModal="openModal"
            :closeModal="closeModal"
            :booking="selectedBooking"
        />
    </div>
</template>
