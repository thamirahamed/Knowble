<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import ProfilePicture from "@/Components/ProfilePicture.vue";
import { PencilIcon, CheckBadgeIcon, AcademicCapIcon, CalendarDateRangeIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
    profile: Object,
    user: Object,
    course: Object,
    level: Object,
    semester: Object,
    tutor: [String, null],
    tutorsessions: [Array, null],
    tutorselectedmodules: [Array, null],
    cBookings: [Array, null],
});

console.log(JSON.stringify(props.cBookings, null, 2));

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
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="flex justify-center">
            <div class="flex lg:max-w-7xl w-full gap-8">
                <!-- Profile Info Section -->
                <div class="flex flex-col max-w-xs w-full bg-white rounded-md mt-8 shadow h-fit" >
                    <!-- Profile Pic Section -->
                    <div class="flex py-8 px-8 justify-center w-full md:w-fit">
                        <ProfilePicture
                            :profile="profile"
                            class="w-full h-auto shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        />
                    </div>
                    <!-- User Information Section -->
                    <div class="flex flex-col flex-1 pb-5 px-6">
                        <div class="border-y border-gray-300 py-3 p-1">
                            <h1 class="flex text-2xl font-extrabold tracking-wide text-slate-900">
                                <!-- {{ user.name }} -->
                                <template v-if="tutor === 'approved'">
                                    {{ user.name }} <CheckBadgeIcon class="ml-1 w-6 text-accent" />
                                </template>
                                <template v-else>
                                    {{ user.name }}
                                </template>
                            </h1>
                            <div class="flex justify-between items-center w-full gap-3">
                                <p class="text-xl font-medium text-gray-700">{{ profile.cb_number.toUpperCase() }}</p>
                                <a
                                    :href="route('profile.edit')"
                                    class="rounded-full bg-accent p-1.5 font-semibold text-white relative z-0 overflow-hidden transition-all duration-200 after:absolute after:inset-0 after:-z-10 after:translate-x-[-150%] after:translate-y-[150%] after:scale-[2.5] after:rounded-[100%] after:bg-gradient-to-l from-accentdark after:transition-transform after:duration-550  hover:after:translate-x-[0%] hover:after:translate-y-[0%]"
                                >
                                    <PencilIcon class="w-3" />
                                </a>
                            </div>
                        </div>
                        <div class="p-1">
                            <p class="text-lg text-gray-700">{{ course.degree_name}}</p>
                            <p class="text-lg text-gray-700">{{ level.level_name}} | {{ semester.semester_name}}</p>
                        </div>
                    </div>
                </div>

                <!-- Right side profile cards -->
                <div class="flex flex-col flex-1">
                    <!-- Tutoring Details -->
                    <div v-if="tutor === 'approved'" class="flex flex-col flex-1 bg-white rounded-md mt-8 shadow h-fit"> 
                        <div class="py-4 px-6">
                            <h2 class="text-2xl font-bold">Tutor Details</h2>
                            <p class="text-gray-700">
                                View the tutor's modules and availability to easily find when and what they teach.
                            </p>

                            <div class="flex w-full gap-12">   
                                <!-- Selected Modules -->
                                <div v-if="tutorselectedmodules && tutorselectedmodules.length > 0" class="mt-4 flex flex-col flex-1">
                                    <h3 class="text-lg font-medium mb-2">Modules</h3>
                                    <ul>
                                        <li v-for="module in tutorselectedmodules" :key="module.id" class="flex items-center even:bg-accent/5 px-4 py-2 text-gray-800 " >
                                            <AcademicCapIcon class="mr-2 w-4 h-4 text-gray-700" /> {{ module.module_name }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="mt-4 flex flex-col flex-1">
                                    <h3 class="text-lg font-medium mb-2">Modules</h3>
                                    <p class="mt-2 text-gray-600 h-full">No modules available.</p>
                                </div>

                                <!-- Available Time -->
                                <div v-if="tutorsessions.length > 0" class="mt-4 flex flex-col flex-1">
                                    <h3 class="text-lg font-medium mb-2">Tutor Availability</h3>
                                    <ul>
                                        <li 
                                            v-for="session in tutorsessions" 
                                            :key="session.id" 
                                            class="flex justify-between even:bg-accent/5 px-4 py-2 text-gray-800"
                                        >
                                            <div class="flex items-center">
                                                <CalendarDateRangeIcon class="mr-2 w-4 text-gray-700" /> {{ formatDateToWords(session.session_date) }} 
                                            </div>
                                            <div class="flex items-center">
                                                {{ session.start_time }} - {{ session.end_time }}
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="mt-4 flex flex-col flex-1">
                                    <h3 class="text-lg font-medium mb-2">Tutor Availability</h3>
                                    <p class="mt-2 text-gray-600 h-full">No sessions available.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Session Details -->
                    <div class="flex flex-col flex-1 bg-white rounded-md mt-8 shadow py-4 px-6">
                        <h2 class="text-2xl font-bold">Past Sessions</h2>
                        <p class="text-gray-700">
                            View your log of past sessions, including those that were completed and cancelled.
                        </p>
                        <div class=" flex flex-wrap gap-4 justify-between mt-4 max-h-96 overflow-y-auto h-fit">
                            <div   
                                v-if="cBookings.length > 0"
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
                                        <div class="flex items-center text-lg font-semibold text-gray-800">
                                            <p>{{ booking.tutor }}</p>
                                            <CheckBadgeIcon class="ml-1 w-5 h-5 text-accent" />
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-lg text-gray-600">{{ booking.module_name }}</p>
                                        <p class="text-lg text-gray-600">
                                            {{ formatDateToWords(booking.session_date) }} | {{ booking.start_time }} - {{ booking.end_time }}
                                        </p>
                                        <p v-if="booking.notes " class="text-lg text-gray-600">Notes: {{ booking.notes }}</p>
                                        <p v-if="booking.status==='cancelled'" class="text-lg text-red-500">Cancelled</p>
                                        <p v-if="booking.status==='completed'" class="text-lg text-accent">Completed</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <p class="text-gray-600">
                                    No past sessions with tutor found.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
