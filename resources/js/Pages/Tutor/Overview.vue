<script setup>
import { ref, onMounted, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { ChevronDownIcon, ChevronUpIcon, ClockIcon, TrashIcon } from "@heroicons/vue/24/solid";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputLabel from "@/Components/InputLabel.vue";

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
    sessionSlots: {
        type: Array,
        required: true,
    }
});

const showRejectedModules = ref(false); // State to track dropdown menu for rejected modules
const successMessage = ref(""); // State to track the success message
const emit = defineEmits("selectionChange");

const sessionInputSlots = ref({
  session_date: '',
  start_time: '',
  end_time: '',
});

// State to track selected modules
const selectedModules = ref(new Set());

// Error message for session availability
const errorMessage = ref('');

// validating minimum date selected should be tomorrow
const tomorrowDate = computed(() => {
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  return tomorrow.toISOString().split('T')[0];
});

// Validating time slots to be one hour minimum and six hours max
const validateTime = () => {
  const { start_time, end_time } = sessionInputSlots.value;
  if (!start_time || !end_time) return;

  const startTime = new Date(`1970-01-01T${start_time}:00`);
  const endTime = new Date(`1970-01-01T${end_time}:00`);
  const duration = (endTime - startTime) / (1000 * 60 * 60);

  if (duration < 1) {
    errorMessage.value = 'The session duration must be at least 1 hour.';
  } else if (duration > 6) {
    errorMessage.value = 'The session duration cannot exceed 6 hours.';
  } else {
    errorMessage.value = '';
  }
};

// form submission for session creation
const handleSubmit = () => {
  validateTime();

  if (errorMessage.value) {
    return;
  }

  const payload = [
    {
      session_date: sessionInputSlots.value.session_date,
      start_time: sessionInputSlots.value.start_time,
      end_time: sessionInputSlots.value.end_time,
    },
  ];
  // Submit form data 
  router.post("/tutor/sessions/create", { sessions: payload }, {
    onSuccess: () => {
        successMessage.value = "New session added successfully!"; // Set success message

        // Clear form values
        sessionInputSlots.value = {
            session_date: "",
            start_time: "",
            end_time: "",
        };

        // Sort sessions by session_date in ascending order
        props.sessionSlots.sort((a, b) => new Date(a.session_date) - new Date(b.session_date));

        // Scroll to the newly created session
        setTimeout(() => {
          const lastSessionIndex = props.sessionSlots.findIndex(
            session => session.session_date === payload[0].session_date &&
                    session.start_time === payload[0].start_time
          );

          const newSessionId = props.sessionSlots[lastSessionIndex]?.id;  // Get the ID of the last session
          const newSessionElement = document.getElementById('sessionCard-' + newSessionId);

          // Scroll to the session and add the pulse effect
          if (newSessionElement) {
            newSessionElement.scrollIntoView({ behavior: "smooth", block: "center" });

            // Apply the pulse effect
            newSessionElement.classList.add("pulse-green");
          }
        }, 100);
    },
    onError: (errors) => {
        console.log(errors); // Log errors to the console for debugging
        alert('Failed to create session: ' + Object.values(errors).join(', ')); // Show error messages
    },
  });
};

// delete available sessions
const deleteSession = (sessionId) => {
  // Send a delete request to the backend
  router.post(`/tutor/sessions/delete/${sessionId}`, {}, {
    onSuccess: () => {
      // Sort the sessionSlots by session_date in ascending order after deleting
      props.sessionSlots.sort((a, b) => new Date(a.session_date) - new Date(b.session_date));
      
      // Alert user after successful deletion
      alert("Session deleted successfully!");
    },
    onError: (error) => {
      console.error("Failed to delete session:", error);
      alert("An error occurred while trying to delete the session.");
    }
  });
};

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
            <p class="text-gray-500 mb-3">Easily create your available sessions, with the option to delete any unbooked slot as needed.</p>

            <div class="flex flex-col lg:flex-row items-center">
                <div class="px-6 py-0.5 w-full lg:max-w-sm">
                    <h2 class="text-lg font-medium mb-2">Create Tutor Session</h2>
                    <form @submit.prevent="handleSubmit">
                        <div class="mb-4">
                            <InputLabel for="session_date">
                            Session Date
                            </InputLabel>
                            <input
                            type="date"
                            id="session_date"
                            v-model="sessionInputSlots.session_date"
                            class="rounded-md border-gray-300 text-lg shadow-sm hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500 w-full"
                            :min="tomorrowDate"
                            required
                            />
                        </div>
                        <div class="mb-4">
                            <InputLabel for="start_time">
                            Start Time
                            </InputLabel>
                            <input
                            type="time"
                            id="start_time"
                            v-model="sessionInputSlots.start_time"
                            class="rounded-md border-gray-300 text-lg shadow-sm hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500 w-full"
                            @change="validateTime"
                            required
                            />
                        </div>
                        <div class="mb-4">
                            <InputLabel for="end_time">
                            End Time
                            </InputLabel>
                            <input
                            type="time"
                            id="end_time"
                            v-model="sessionInputSlots.end_time"
                            class="rounded-md border-gray-300 text-lg shadow-sm hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500 w-full"
                            @change="validateTime"
                            required
                            />
                        </div>
                        <div v-if="errorMessage" class="mb-4 text-red-600 text-sm text-center">
                            {{ errorMessage }}
                        </div>
                        <div v-if="successMessage" class="mb-4 text-green-600 text-sm text-center">
                            {{ successMessage }}
                        </div>
                        <div class="flex justify-end">
                            <PrimaryButton
                                type="submit"
                                id="createSessionBtn"
                            >
                                Create Session
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
    
                <div class="space-y-3 overflow-y-auto lg:border-l lg:border-gray-300 max-h-[30rem] lg:min-h-80 mt-4 lg:mt-0 flex-1 px-6 py-0.5">
                    <!-- Check if sessionSlots is empty -->
                    <div v-if="sessionSlots.length === 0" class="text-center text-gray-500 h-auto">
                    No sessions available.
                    </div>
    
                    <!-- Loop through sessionSlots and display each session in a card -->
                    <div 
                        v-for="session in sessionSlots" 
                        :key="session.id" 
                        class="w-full shadow-md rounded-md p-4"
                        :id="'sessionCard-' + session.id"
                    >
                        <div v-if="session.status === 'pending'">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Session on {{ formatDateToWords(session.session_date) }}</h3>
                                <DangerButton
                                    @click="deleteSession(session.id)"
                                    class="!p-1.5"
                                    :id="'deleteSessionBtn-' + session.id"
                                >
                                    <TrashIcon class="w-4" />
                                </DangerButton>
                            </div>
                            
                            <div class="text-gray-600">
                                <p class="flex items-center">
                                    <ClockIcon class="w-4 mr-1.5"/> <span class="font-medium mr-1">Start Time : </span> {{ session.start_time }}
                                </p>
                                <p class="flex items-center">
                                    <ClockIcon class="w-4 mr-1.5"/> <span class="font-medium mr-1">End Time : </span> {{ session.end_time }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
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
