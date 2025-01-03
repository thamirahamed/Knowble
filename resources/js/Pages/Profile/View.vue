<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import ProfilePicture from "@/Components/ProfilePicture.vue";
import { PencilIcon, CheckBadgeIcon, AcademicCapIcon, CalendarDateRangeIcon } from "@heroicons/vue/24/solid";
import { onMounted, ref } from "vue";

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
});

// Create a reactive reference to store the filtered tutorsessions
const filteredSessions = ref([]);

// Sort sessionSlots by session_date in ascending order
onMounted(() =>{
    // Filter the tutorsessions to include only those with a 'pending' status
    filteredSessions.value = props.tutorsessions.filter(session => session.status === 'pending');
    
    filteredSessions.value.sort((a, b) => new Date(a.session_date) - new Date(b.session_date));
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
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="flex justify-center">
            <div class="flex lg:max-w-7xl w-full gap-8">
                <!-- Profile Info Section -->
                <div class="flex flex-col max-w-xs w-full bg-white rounded-md mt-8 shadow" >
                    <!-- Profile Pic Section -->
                    <div class="flex py-8 px-8 justify-center w-full md:w-fit">
                        <ProfilePicture
                            :profile="profile"
                            class="w-full h-auto shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        />
                    </div>
                    <!-- User Information Section -->
                    <div class="flex flex-col flex-1 pb-5 px-6 justify-between ">
                        <div class="border-y border-gray-300 py-3 p-1">
                            <p class="flex text-2xl font-extrabold tracking-wide">
                                <!-- {{ user.name }} -->
                                <template v-if="tutor === 'approved'">
                                    {{ user.name }} <CheckBadgeIcon class="ml-1 w-6" />
                                </template>
                                <template v-else>
                                    {{ user.name }}
                                </template>
                            </p>
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
                            <p class="text-lg text-gray-700">{{ level.level_name}}</p>
                            <p class="text-lg text-gray-700">{{ semester.semester_name}}</p>
                        </div>
                    </div>
                </div>

                <!-- Tutoring Details -->
                <div v-if="tutor === 'approved'" class="flex flex-col flex-1 bg-white rounded-md mt-8 shadow h-fit"> 
                    <div class="p-6">
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
                                        <AcademicCapIcon class="mr-2 w-4 text-gray-700" /> {{ module.module_name }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Available Time -->
                            <div v-if="filteredSessions.length > 0" class="mt-4 flex flex-col flex-1">
                                <h3 class="text-lg font-medium mb-2">Tutor Availability</h3>
                                <ul>
                                    <li 
                                        v-for="session in filteredSessions" 
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
