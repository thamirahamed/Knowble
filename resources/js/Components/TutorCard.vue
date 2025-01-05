<script setup>
import { CheckBadgeIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from './PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    tutorname: String,
    cbnumber: String,
    profile_pic: String,
    tutor_id: Number,
    school: String,
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
            <h2 class="text-slate-500 font-light">{{ cbnumber }}</h2>
            <h2 class="text-slate-500 font-light">{{ school }}</h2>
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
