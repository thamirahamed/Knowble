<script setup>

import { StarIcon } from "@heroicons/vue/24/solid/index.js";

const props = defineProps({
    feedbacks: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <div class="flex flex-col w-full ">
        <h1 class="text-xl font-bold text-gray-800">Feedbacks and Ratings</h1>
        <p class="text-gray-500 mb-4">View your feedback and ratings from your sessions, helping you improve and understand your performance.</p>
        <div class="flex flex-col space-y-4">   
            <div
                v-if="feedbacks.length > 0"
                v-for="feedback in feedbacks"
                class="flex flex-col h-auto"
            >
                <div class="flex flex-col w-full rounded-md shadow-md px-4 py-3 bg-secondary/5 my-auto">
                    <div class="flex mb-2">
                        <img
                            :src="feedback.profile_pic"
                            alt="Profile Picture"
                            class="w-8 h-8 mr-3 rounded-full object-cover"
                        />
                        <div class="flex flex-col">
                            <p class="text-lg font-semibold text-gray-800">{{ feedback.user }}</p>
                            <p class="lg:text-sm text-gray-500">{{feedback.degree}}</p>
                        </div>
    
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
                <p class="text-gray-700 m-auto text-center">No feedback submitted yet</p>
            </div>
        </div>
    </div>
</template>
