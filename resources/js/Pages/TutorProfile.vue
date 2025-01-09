<script setup>
import { defineProps, ref, onMounted, computed } from "vue";
import ProfilePicture from "@/Components/ProfilePicture.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { CheckBadgeIcon, AcademicCapIcon, CalendarDateRangeIcon, UserGroupIcon, UserIcon, StarIcon, PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import BookSession from "@/Components/BookSession.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head } from "@inertiajs/vue3";
import BookGroupSession from "@/Components/BookGroupSession.vue";
import Slider from "@/Components/Slider.vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import { router } from "@inertiajs/vue3";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    tutor: Array,
    profile: Array,
    school: Array,
    tutormodules: Array,
    degree: Array,
    level: Array,
    semester: Array,
    user: Array,
    sessions: [Array, null],
    commonModules: [Array, null],
    studentModules: Array,
    isLeader: Array,
    peerGroups: Array,
    hasCompletedSession: Array,
    hasCompletedGroupSession: Array,
    userFeedback: Array,
    feedbacks: Array,
    avgRating: String,
    resourcesShared: {
        type: Object,
        required: true,
    }
});

console.log(JSON.stringify(props.hasCompletedSession, null, 2));
console.log(JSON.stringify(props.hasCompletedGroupSession, null, 2));
console.log(JSON.stringify(props.userFeedback, null, 2));

const openModal = ref(null);

const closeModal = () => {
    openModal.value = null;
};

const openModalWithData = () => {
    openModal.value = true;
};

const openModal1 = ref(null);

const closeModal1 = () => {
    openModal1.value = null;
};

const openModalWithData1 = () => {
    openModal1.value = true;
};

const form = useForm({
    feedback: '',
    rating: 4,
});

const isEditing = ref(false);

const editForm = useForm({
  rating: 0,
  feedback: '',
});

function startEditing(feedback) {
    isEditing.value = true;
    editForm.rating = feedback.rating;
    editForm.feedback = feedback.feedback;
}

const submitCreateFeedback = () => {
    // Prepare the data to be sent
    const payload = {
        tutor: props.tutor.id,
        feedback: form.feedback,
        rating: form.rating,
    };

    console.log(payload);

    // Submit the form using router.post
    router.post('/feedback/create', payload, {
        onSuccess: () => {
            alert('Feedback submitted successfully!');
            form.reset(); // Reset the form fields
        },
        onError: (errors) => {
            console.log(errors); // Log errors to the console for debugging
            alert('Failed to submit feedback: ' + Object.values(errors).join(', ')); // Show error messages
        },
    });
};

const submitEditFeedback = () => {

    // Prepare the data to be sent
    const payload = {
        tutor: props.tutor.id,
        feedback: editForm.feedback,
        rating: editForm.rating,
    };

    console.log(payload);

    // Submit the form using router.post
    router.post('/feedback/edit', payload, {
        onSuccess: () => {
            alert('Feedback editted successfully!');
            isEditing.value = false;
            form.reset(); // Reset the form fields
        },
        onError: (errors) => {
            console.log(errors); // Log errors to the console for debugging
            alert('Failed to edit feedback: ' + Object.values(errors).join(', ')); // Show error messages
        },
    });
};

const deleteFeedback = (feedback) => {

    // Prepare the data to be sent
    const payload = {
        tutor: props.tutor.id,
        feedback: feedback.id,
    };

    console.log(payload);

    // Submit the form using router.post
    router.post('/feedback/delete', payload, {
        onSuccess: () => {
            alert('Feedback deleted successfully!');
        },
        onError: (errors) => {
            console.log(errors); // Log errors to the console for debugging
            alert('Failed to delete feedback: ' + Object.values(errors).join(', ')); // Show error messages
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
    <Head :title='user.name' />

    <AuthenticatedLayout>
        <!-- Main Container -->
        <div class="flex justify-center">
            <div class="flex lg:max-w-7xl w-full gap-8">
                <!-- Profile Info Section -->
                <div class="flex flex-col max-w-xs w-full bg-white rounded-md mt-8 shadow h-fit">
                    <!-- Profile Pic Section -->
                    <div class="flex py-8 px-8 justify-center">
                        <ProfilePicture
                            :profile="profile"
                            class="w-full h-auto shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px]"
                        />
                    </div>

                    <!-- Book Session Buttons -->
                    <BookSession
                        :openModal="openModal"
                        :tutorid="tutor.id"
                        :closeModal="closeModal"
                        :commonModules="commonModules"
                        :sessionSlots="sessions"
                    />
                    <BookGroupSession
                        :openModal="openModal1"
                        :tutorid="tutor.id"
                        :closeModal="closeModal1"
                        :peerGroups="peerGroups"
                        :sessionSlots="sessions"
                    />

                    <!-- User Information Section -->
                    <div class="flex flex-col flex-1 pb-5 px-6">
                        <div class="border-y border-gray-300 py-3 px-1">
                            <h1 class="flex text-2xl font-extrabold tracking-wide text-slate-900">
                                {{ user.name }}
                                <CheckBadgeIcon class="ml-1 w-6 text-accent" />
                            </h1>
                            <p class="text-xl font-medium text-gray-700">{{ profile.cb_number }}</p>
                        </div>
                        <div class="p-1">
                            <p class="text-lg text-gray-700">{{ degree.degree_name }}</p>
                            <p class="text-lg text-gray-700">{{ level.level_name }} | {{ semester.semester_name }}</p>
                        </div>
                        <PrimaryButton
                            v-if="sessions.length > 0 && tutormodules.length > 0"
                            id="bookSessionBtn"
                            class="mt-4 w-full"
                            @click="openModalWithData(tutor.id)"
                        >
                            <div class="flex items-center">
                                <UserIcon class="text-white w-5 h-5 mr-2" />
                                <span>
                                    Book Session
                                </span>
                            </div>
                        </PrimaryButton>
                        <PrimaryButton
                            v-if="sessions.length > 0 && tutormodules.length > 0 && isLeader"
                            id="bookGroupSessionBtn"
                            class="mt-4 w-full"
                            @click="openModalWithData1(tutor.id)"
                        >
                            <div class="flex items-center">
                                <UserGroupIcon class="text-white w-5 h-5 mr-2" />
                                <span>
                                    Book Session for Peer Group
                                </span>
                            </div>
                        </PrimaryButton>
                    </div>


                </div>

                <!-- Tutor Details -->
                <div class="flex flex-col flex-1">
                    <div class="flex flex-col flex-1 bg-white rounded-md mt-8 shadow h-fit py-4 px-6">
                        <h2 class="text-2xl font-bold">Tutor Details</h2>
                        <p class="text-gray-700">
                            View the tutor's modules and availability to easily find when and what they teach.
                        </p>
    
                        <div class="flex w-full gap-12">
                            <!-- Selected Modules -->
                            <div v-if="tutormodules && tutormodules.length > 0" class="mt-4 flex flex-col flex-1">
                                <h3 class="text-lg font-semibold mb-2">Modules</h3>
                                <ul>
                                    <li v-for="module in tutormodules" :key="module.id" class="flex items-center even:bg-accent/5 px-4 py-2 text-gray-800">
                                        <AcademicCapIcon class="mr-2 w-4 h-4 text-gray-700" />
                                        {{ module.module_name }}
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="mt-4 flex flex-col flex-1">
                                <h3 class="text-lg font-medium mb-2">Modules</h3>
                                <p class="mt-2 text-gray-600 h-full">No modules available.</p>
                            </div>
    
                            <!-- Available Time -->
                            <div v-if="sessions.length > 0" class="mt-4 flex flex-col flex-1">
                                <h3 class="text-lg font-medium mb-2">Tutor Availability</h3>
                                <ul>
                                    <li
                                        v-for="session in sessions"
                                        :key="session.id"
                                        class="flex justify-between even:bg-accent/5 px-4 py-2 text-gray-800 "
                                    >
                                        <div class="flex items-center">
                                            <CalendarDateRangeIcon class="mr-2 w-4 h-4 text-gray-700" /> {{ formatDateToWords(session.session_date) }}
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
                    <!-- Resource Shared Section -->
                    <div class="flex flex-col flex-1 bg-white rounded-md mt-8 shadow h-fit py-4 px-6">
                        <h2 class="text-2xl font-bold">Study Resources</h2>
                        <p class="text-gray-700">
                            Access a variety of materials and tools to support your learning and enhance your understanding.
                        </p>
                        <div class="overflow-y-auto max-h-60 shadow-sm">
                            <ul class="divide-y divide-gray-200">
                                <li
                                    v-for="resource in resourcesShared"
                                    :key="resource.id"
                                    class="flex justify-between items-center px-4 py-3 text-gray-800 "
                                >
                                    <!-- Resource Name -->
                                    <span class="truncate">{{ resource.fileName }}</span>

                                    <!-- Download Button -->
                                    <a
                                        :href="route('resource-shares.download', resource.id)"
                                        class="text-blue-500 hover:underline"
                                    >
                                        Download
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex flex-col flex-1 bg-white rounded-md mt-8 shadow h-fit py-4 px-6">
                        <h2 class="text-2xl font-bold flex">Feedback and Ratings <span v-if="avgRating!==null" class="ml-2 flex items-center font-medium text-accent">(<StarIcon class="w-6 h-6" />{{ avgRating }} / 5)</span></h2>
                        <p class="text-gray-700">
                            Explore feedback and ratings from students to evaluate the tutor's teaching quality and effectiveness.
                        </p>
                        <div class="flex">
                            <!-- Create feedback -->
                            <div v-if="(hasCompletedGroupSession || hasCompletedSession) && userFeedback === null" class="flex flex-1 flex-col border-r border-gray-300 px-5 my-4 max-w-sm">
                                <h3 class="text-lg font-medium">Provide Your Feedback</h3>
                                <form @submit.prevent="submitCreateFeedback" class="mt-2 flex flex-col gap-4">
                                    <!-- Rating Slider -->
                                    <div>
                                        <Slider
                                            :min="1" 
                                            :max="5" 
                                            :value="form.rating" 
                                            v-model="form.rating" 
                                            sLabel="Rating" 
                                        />
                                    </div>
    
                                    <!-- Feedback Text Area -->
                                    <div>
                                        <InputLabel for="feedback" value="Feedback" />
                                        <textarea 
                                            id="feedback" 
                                            v-model="form.feedback" 
                                            rows="4" 
                                            class="mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                                            placeholder="Write your feedback here..."
                                        ></textarea>
                                    </div>
    
                                    <!-- Submit Button -->
                                    <div class="flex justify-end">
                                        <PrimaryButton
                                            type="submit" 
                                            class="w-fit text-right"
                                            id="submitReviewBtn"
                                        >
                                            Submit Feedback
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </div>
                            <!-- Your Feedback -->
                            <div v-else-if="userFeedback !== null" class="flex flex-1 flex-col border-r border-gray-300 px-5 my-4">
                                <div v-if="isEditing === false" class="my-auto">
                                    <h3 class="text-lg font-medium mb-2">Your Feedback</h3>
                                    <div
                                        class="flex flex-col rounded-md shadow-md px-4 py-3 bg-secondary/5"
                                    >
                                        <div class="flex items-center mb-2 justify-between flex-1">
                                            <div class="flex">
                                                <img
                                                    :src="userFeedback.pfp"
                                                    alt="Profile Picture"
                                                    class="w-8 h-8 mr-3 rounded-full object-cover"
                                                />
                                                <p class="text-lg font-semibold text-gray-800">{{ userFeedback.user_name }}</p>
                                            </div>
                                            <div class="flex flex-row gap-2">
                                                <PrimaryButton class="!p-1.5 !rounded-full" id="editReviewBtn" @click="startEditing(userFeedback)">
                                                    <PencilIcon class="w-4 h-4" />
                                                </PrimaryButton>
                                                <DangerButton class="!p-1.5 !rounded-full" id="deleteReviewButton" @click="deleteFeedback(userFeedback)">
                                                    <TrashIcon class="w-4 h-4" />
                                                </DangerButton>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="flex">
                                                <StarIcon
                                                    v-for="star in userFeedback.rating"
                                                    :key="star"
                                                    :class="star <= userFeedback.rating ? 'text-accent' : 'text-gray-400'"
                                                    class="h-5 w-5"
                                                />
                                            </div>
                                            <p class="text-lg text-gray-600">"{{ userFeedback.feedback }}"</p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="isEditing === true" class="flex flex-1 flex-col max-w-sm">
                                    <h3 class="text-lg font-medium">Edit Your Feedback</h3>
                                    <form @submit.prevent="submitEditFeedback" class="mt-2 flex flex-col gap-4">
                                        <!-- Rating Slider -->
                                        <div>
                                            <Slider
                                                :min="1" 
                                                :max="5" 
                                                :value="editForm.rating" 
                                                v-model="editForm.rating" 
                                                sLabel="Rating" 
                                            />
                                        </div>
        
                                        <!-- Feedback Text Area -->
                                        <div>
                                            <InputLabel for="feedback" value="Feedback" />
                                            <textarea 
                                                id="feedback" 
                                                v-model="editForm.feedback" 
                                                rows="4" 
                                                class="mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                                                placeholder="Write your feedback here..."
                                            ></textarea>
                                        </div>
        
                                        <!-- Submit Button -->
                                        <div class="flex justify-end">
                                            <PrimaryButton
                                                type="submit" 
                                                class="w-fit text-right"
                                            >
                                                Submit Feedback
                                            </PrimaryButton>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Error Message -->
                            <div v-else class="mt-4 flex flex-col flex-1 px-5 my-4 border-r border-gray-300 max-w-sm">
                                <p class="text-gray-700 m-auto text-center">You need to complete a session with this tutor before providing feedback.</p>
                            </div>
                            
                            <div class="flex flex-1 flex-col px-5 my-4 min-h-60 max-h-96 w-fit gap-3 overflow-y-auto">
                                <div
                                    v-if="feedbacks.length > 0"    
                                    v-for="feedback in feedbacks"
                                    class="flex flex-col flex-1 "
                                >
                                    <div class="flex flex-col rounded-md shadow-md px-4 py-3 bg-secondary/5 my-auto">
                                        <div class="flex mb-2">
                                            <img
                                                :src="feedback.pfp"
                                                alt="Profile Picture"
                                                class="w-8 h-8 mr-3 rounded-full object-cover"
                                            />
                                            <p class="text-lg font-semibold text-gray-800">{{ feedback.user_name }}</p>
                                        </div>
                                        
                                        <div class="px-2">
                                            <div class="flex">
                                                <StarIcon
                                                    v-for="star in feedback.rating"
                                                    :key="star"
                                                    :class="star <= feedback.rating ? 'text-accent' : 'text-gray-400'"
                                                    class="h-5 w-5"
                                                />
                                            </div>
                                            <p class="text-lg text-gray-600">"{{ feedback.feedback }}"</p>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="flex flex-1 px-5 my-4">
                                    <p class="text-gray-700 m-auto text-center">No feedback submitted for this tutor yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


