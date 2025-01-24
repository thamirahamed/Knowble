<script setup>
import { CheckBadgeIcon, StarIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from './PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    tutorname: String,
    rating: String,
    profile_pic: String,
    tutor_id: Number,
    degree: String,
});

const viewProfile = (tutorid) => {
    router.visit(route('tutor.profile', { id: tutorid }));
}
</script>

<template>
    <div class="flex w-full h-auto items-center px-4 py-2 rounded-md border border-gray-200 shadow-md space-x-4">
        <div class="flex">
            <img
                :src="props.profile_pic"
                alt="Profile Picture"
                class="w-16 h-16 rounded-full object-cover"
            />
        </div>
        <div class="flex flex-1 flex-col text-lg">
            <div class="flex flex-row items-center">
                <h1 class="text-slate-900 font-semibold">{{ tutorname }}</h1>
                <CheckBadgeIcon class="ml-1 w-5 h-5 text-accent" />
            </div>
            <h2 class="text-slate-500 font-light">{{ degree }}</h2>
            <h2 v-if="rating !== null" class="text-slate-500 font-light flex items-center"><StarIcon class="w-4 h-4 text-accent mr-1"/>({{ rating }})</h2>
            <h2 v-else class="text-slate-500 font-light flex items-center"><StarIcon class="w-4 h-4 text-accent mr-1"/>(-)</h2>
        </div>
        <div>
            <PrimaryButton
                :id="'viewProfileBtn-' + tutor_id"
                class="!text-sm"
                @click="viewProfile(tutor_id)"
            >
                View Profile
            </PrimaryButton>
        </div>
    </div>
</template>
