<script setup>

// Props
const props = defineProps({
    cBookings: Array,      // Array of booked sessions
});

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
        <h1 class="text-xl font-bold text-gray-800">Completed Bookings</h1>
        <p class="text-gray-500 mb-4">View your log of past sessions, including those that were completed and cancelled.</p>

        <div v-if="cBookings.length > 0" class="mb-4 flex flex-wrap gap-4 justify-between">
            <div
                v-for="booking in cBookings"
                :key="booking.id"
                class="flex justify-between items-center bg-secondary/5 p-4 rounded-md shadow-md xl:w-[49%] flex-wrap" 
            >
                <div class="flex flex-col">
                    <div class="inline-flex">
                        <img
                            :src="booking.profile_pic"
                            alt="Profile Picture"
                            class="w-8 h-8 mr-3 rounded-full object-cover"
                        />
                        <p class="text-lg font-semibold text-gray-800">{{ booking.user }}</p>
                    </div>
                    <div>
                        <p class="text-lg text-gray-600">{{ booking.degree }}</p>
                        <p class="text-lg text-gray-600">{{ booking.module_name }}</p>
                        <p class="text-lg text-gray-600">
                            {{ formatDateToWords(booking.session_date) }} | {{ booking.start_time }} - {{ booking.end_time }}
                        </p>
                        <p v-if="booking.notes && booking.status==='completed'" class="text-lg text-gray-600">Notes: {{ booking.notes }}</p>
                        <p v-if="booking.notes && booking.status==='cancelled'" class="text-lg text-gray-600">Reason: {{ booking.notes }}</p>
                        <p v-if="booking.status==='cancelled'" class="text-lg text-red-500">Cancelled</p>
                        <p v-if="booking.status==='completed'" class="text-lg text-accent">Completed</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center text-gray-500 py-6 mb-4">
            No bookings completed at the moment.
        </div>
    </div>
</template>
