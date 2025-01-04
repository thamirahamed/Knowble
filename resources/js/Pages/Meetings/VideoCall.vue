<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ArrowLeftEndOnRectangleIcon } from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    meetingUrl: Array,
    bookingDetails: Array,
});

console.log (JSON.stringify(props.bookingDetails, null, 2));

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

const leaveMeeting = (booking) => {
    if (booking.isUserTutor === "Yes"){
        router.visit(route('profile.show'))
    } else {
        router.visit(route('tutor.profile', { id: booking.tutor_id }));
    }
};

</script>

<template>
    <AuthenticatedLayout>
        <div class="flex justify-center mt-8">
            <div class="flex flex-col max-w-7xl w-full bg-white px-8 py-6 min-h-[90vh] rounded-md shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl text-gray-800 font-semibold">Meeting with {{ bookingDetails.tutor_name }}</h1>
                        <h2 class="text-lg text-gray-600">{{ bookingDetails.module_name }}</h2>
                        <h2 class="text-lg text-gray-600">{{ formatDateToWords(bookingDetails.session_date) }} | {{ bookingDetails.start_time }} - {{ bookingDetails.end_time }}</h2>
                        <h2 v-if="bookingDetails.notes" class="text-lg text-gray-600">Notes: {{ bookingDetails.notes }}</h2>
                    </div>
                    <div>
                        <DangerButton 
                            :icon="true" 
                            iconPlacement="left"
                            @click="leaveMeeting(bookingDetails)"
                        >
                            <template #icon>
                                <ArrowLeftEndOnRectangleIcon class="h-5 w-5 text-white" />
                            </template>
                            Leave Meeting
                        </DangerButton>
                    </div>
                </div>
                <iframe
                    allow="camera; microphone; display-capture; fullscreen; clipboard-read; clipboard-write; web-share; autoplay"
                    :src="props.meetingUrl"
                    id="meeting"
                    class="flex w-full h-full bg-gray-900 mt-4 rounded-xl"
                ></iframe>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
