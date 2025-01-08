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
    <div class="flex flex-1 justify-evenly items-baseline max-w-2xl px-5 my-4 min-h-60 max-h-96 ">
        <div
            v-if="feedbacks.length > 0"
            v-for="feedback in feedbacks"
            class="flex flex-col flex-1 "
        >
            <div class="flex flex-col rounded-md shadow-md px-4 py-3 bg-secondary/5 my-auto mx-5">
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
            <p class="text-gray-700 m-auto text-center">No feedback submitted for this tutor yet</p>
        </div>
    </div>
</template>
