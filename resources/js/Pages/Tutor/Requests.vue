<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";

// Props
const props = defineProps({
    requests: Array,      // Array of pending requests
    userdetails: Array,   // Array of user details including profile and user data
});

// Combine requests with user and profile data
const enhancedRequests = ref(
    props.requests.map((request) => {
        const userDetail = props.userdetails.find((user) => user.user.id === request.user_id);
        return {
            ...request,
            tutor_name: userDetail?.user.name || "Unknown Tutor",
            profile_pic: userDetail?.profile.profile_pic || "https://via.placeholder.com/150",
            cb_number: userDetail?.profile.cb_number || "N/A",
            meeting_url: request.meeting_url || null, // Add meeting URL
        };
    })
);

// Approve Request and Create Meeting
const approveRequest = async (id, tutorName) => {
    try {
        const response = await axios.post(route("meetings.create"), {
            host_name: tutorName,
        });

        const meetingUrl = response.data.meeting_url;

        // Update session to approved and store meeting URL
        router.post(route("request.session.accept", { id }), {}, {
            onSuccess: () => {
                enhancedRequests.value = enhancedRequests.value.map((request) =>
                    request.id === id
                        ? { ...request, status: "approved", meeting_url: meetingUrl }
                        : request
                );
            },
        });
    } catch (error) {
        console.error("Error creating meeting:", error);
    }
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
</script>


<template>
    <div class="min-h-screen bg-gray-100 flex justify-center items-start py-6">
        <div class="w-full max-w-6xl bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Request Approval</h1>

            <div v-if="enhancedRequests.length > 0" class="space-y-4">
                <div
                    v-for="request in enhancedRequests"
                    :key="request.id"
                    class="flex justify-between items-center bg-gray-50 p-4 rounded-md shadow-sm"
                >
                    <div class="flex items-center gap-4">
                        <img
                            :src="request.profile_pic"
                            alt="Profile Picture"
                            class="w-12 h-12 rounded-full object-cover"
                        />
                        <div>
                            <p class="text-lg font-semibold text-gray-800">{{ request.tutor_name }}</p>
                            <p class="text-sm text-gray-500">
                                CB Number: {{ request.cb_number }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Date: {{ request.date }} | Time: {{ request.startTime }} - {{ request.endTime }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span
                            v-if="request.status === 'approved' && request.meeting_url"
                            class="font-semibold text-green-500"
                        >
                            Approved
                        </span>
                        <a
                            v-if="request.status === 'accepted' && request.meeting_url"
                            :href="request.meeting_url"
                            target="_blank"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition"
                        >
                            Join Meeting
                        </a>

                        <button
                            v-if="request.status === 'pending'"
                            @click="approveRequest(request.id, request.tutor_name)"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition"
                        >
                            Approve
                        </button>
                        <button
                            v-if="request.status === 'pending'"
                            @click="rejectRequest(request.id)"
                            id="requestRejectBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition"
                        >
                            Reject
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="text-center text-gray-500 py-6">
                No pending requests at the moment.
            </div>
        </div>
    </div>
</template>

