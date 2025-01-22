<script setup>
import { router, useForm } from '@inertiajs/vue3';
import InputLabel from './InputLabel.vue';
import { UserGroupIcon, XMarkIcon, ExclamationCircleIcon } from '@heroicons/vue/24/solid';
import DangerButton from './DangerButton.vue';

const props = defineProps({
    openModal: Boolean,
    closeModal: Function,
    booking: Array,
    sessionSlots: Array
});

// Initialize the form
const form = useForm({
    notes: '',
    sessionSlot: ''
});

const submitSessionCancel = () => {

    // Prepare the data to be sent
    const payload = {
        sessionId: props.booking.id,
        notes: form.notes,
        altSessionId: form.sessionSlot
    };

    // Submit the form using router.post
    router.post('/tutor/sessions/cancel', payload, {
        onSuccess: () => {
            alert('Session cancellation request sent successfully!');
            props.closeModal(); // Close the modal on success
            form.reset(); // Reset the form fields
        },
        onError: (errors) => {
            alert('Failed to cancel session:', errors);
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
                <h2 class="text-lg  font-light text-white">Cancel Session</h2>
                <button id="closeModal" @click="closeModal" class="p-1 rounded-full bg-red-500 text-white"><XMarkIcon class="w-4" /></button>
            </div>

            <!-- Modal Body -->
            <div class="mt-4 px-6 pb-4">
                <p v-if="booking.type === 'individual'" class="text-lg font-semibold">{{ booking.user }}</p>
                <p v-if="booking.type === 'group'" class="text-lg font-semibold flex items-center"><UserGroupIcon class="w-4 h-4 mr-2" /> {{ booking.peer_group_name }}<span class="text-gray-600 text-sm ml-2">({{ booking.current_members }} / {{ booking.total_members }})</span></p>
                <p class="text-lg text-gray-700">{{ booking.module_name }}</p>
                <p class="text-lg text-gray-700">{{ formatDateToWords(booking.session_date) }} | {{ booking.start_time }} - {{ booking.end_time }}</p>
                <p class="text-lg text-gray-700" v-if="booking.notes">Notes: {{ booking.notes }}</p>
                <div class="flex mb-2 mt-2">
                    <span class="w-full h-0.5 bg-accent"></span>
                </div>
                <p class="text-gray-600 flex items-center"> 
                    <ExclamationCircleIcon class="w-5 h-5 mr-1.5" />
                    Offer a session as compensation for the cancellation.
                </p>
                <form @submit.prevent="submitSessionCancel" class="mt-2 pt-2">
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
                        <inputLabel for="notes" value="Reason for Cancellation" />
                        <textarea
                            id="notes"
                            rows="3"
                            class="mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                            placeholder="Enter cancellation reason"
                            v-model="form.notes"
                            required
                        ></textarea>
                    </div>
                    <div class="mt-2 text-right">
                        <DangerButton
                            type="submit"
                            id="confirmCancellation"
                            @click =submitSessionRequest
                        >
                            Submit Cancellation
                        </DangerButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>