<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import InputLabel from './InputLabel.vue';
import PrimaryButton from './PrimaryButton.vue';
import { XMarkIcon } from '@heroicons/vue/24/solid';
import { onMounted } from 'vue';

const props = defineProps({
    openModal: Boolean,
    tutorid: Number,
    closeModal: Function,
    commonModules: Array,
    sessionSlots: Array,
});

// Initialize the form
const form = useForm({
    module: '',
    sessionSlot: '',
    notes: '',
});

const submitSessionBooking = () => {

    // Prepare the data to be sent
    const payload = {
        module: form.module,
        sessionSlot: form.sessionSlot,
        notes: form.notes,
    };

    // Submit the form using router.post
    router.post('/tutor/sessions/book', payload, {
        onSuccess: () => {
            alert('Session booked successfully!');
            props.closeModal(); // Close the modal on success
            form.reset(); // Reset the form fields
        },
        onError: (errors) => {
            alert('Failed to book session:', errors);
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
    <div v-if="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow-md max-w-lg w-full overflow-hidden">
            <!-- Modal Header -->
            <div class="flex justify-between items-center py-2 px-6 bg-primary">
                <h2 class="text-lg  font-light text-white">Tutor Details</h2>
                <button id="closeModal" @click="closeModal" class="p-1 rounded-full bg-red-500 text-white"><XMarkIcon class="w-4" /></button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4 px-6 pb-4">
                <form @submit.prevent="submitSessionBooking">
                    <div class="mb-4">
                        <InputLabel for="session_module" value="Module" />
                        <select
                            id="session_module"
                            class="cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                            required
                            v-model="form.module"
                        >
                            <option value="">Select Module</option>
                            <option
                                v-for="cModule in commonModules"
                                :key="cModule.id"
                                :value="cModule.id"
                                :id="'session-'+ cModule.id"
                            >
                                {{ cModule.module_name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.module" />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="session-slot" value="Session Slot" />
                        <select
                            id="session_slots"
                            class="cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                            required
                            v-model="form.sessionSlot"
                        >
                            <option value="">Select Session</option>
                            <option
                                v-for="slots in sessionSlots"
                                :key="slots.id"
                                :value="slots.id"
                                :id="'session-'+slots.id"
                            >
                                {{ formatDateToWords(slots.session_date) }} - {{ slots.start_time }} to {{ slots.end_time }}
                            </option>
                            <InputError class="mt-2" :message="form.errors.sessionSlot" />
                        </select>
                    </div>
    
                    <div class="mb-4">
                        <inputLabel for="notes" value="Additional Notes" />
                        <textarea
                            id="notes"
                            rows="3"
                            class="mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                            placeholder="Any additional details for the tutor"
                            v-model="form.notes"
                        ></textarea>
                    </div>
                    <div class="mt-2 text-right">
                        <PrimaryButton
                            id="submitSessionBtn"
                            type="submit"
                        >
                            Submit Request
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>