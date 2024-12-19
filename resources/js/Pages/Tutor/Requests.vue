<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

// Props
const props = defineProps({
    requests: Array,      // Array of pending requests
    userdetails: Array,   // Array of user details including profile and user data
});

// Combine requests with user and profile data
const enhancedRequests = ref(
    props.requests.map((request) => {
        // Match user details with tutor_id or user_id
        const userDetail = props.userdetails.find((user) => user.user.id === request.user_id);

        return {
            ...request,
            tutor_name: userDetail?.user.name || "Unknown Tutor",
            profile_pic: userDetail?.profile.profile_pic || "https://via.placeholder.com/150",
            cb_number: userDetail?.profile.cb_number || "N/A",
        };
    })
);

// Approve Request
const approveRequest = (id) => {
    router.post(route("request.session.accept", { id }), {}, {
        onSuccess: () => {
            enhancedRequests.value = enhancedRequests.value.map((request) =>
                request.id === id ? { ...request, status: "approved" } : request
            );
        },
    });
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
            <!-- Header -->
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Request Approval</h1>

            <!-- Request List -->
            <div v-if="enhancedRequests.length > 0" class="space-y-4">
                <div
                    v-for="request in enhancedRequests"
                    :key="request.id"
                    class="flex justify-between items-center bg-gray-50 p-4 rounded-md shadow-sm"
                >
                    <!-- Tutor Details -->
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

                    <!-- Status and Actions -->
                    <div class="flex items-center gap-2">
                        <span
                            v-if="request.status !== 'pending'"
                            :class="{
                                'text-green-500': request.status === 'approved',
                                'text-red-500': request.status === 'rejected',
                            }"
                            class="font-semibold"
                        >
                            {{ request.status.charAt(0).toUpperCase() + request.status.slice(1) }}
                        </span>

                        <button
                            v-if="request.status === 'pending'"
                            @click="approveRequest(request.id)"
                             id="requestApproveBtn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition"
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

            <!-- Empty State -->
            <div v-else class="text-center text-gray-500 py-6">
                No pending requests at the moment.
            </div>
        </div>
    </div>
</template>
