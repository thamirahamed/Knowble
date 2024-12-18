<script setup>
import { UserCircleIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from './PrimaryButton.vue';
import ProfilePicture from "@/Components/ProfilePicture.vue";
import { ref } from "vue";

const props = defineProps({
    tutorname: String,
    cbnumber: String,
    profile_pic: String,
    tutor_id: Number,
    school: String,
});
</script>

<template>
    <div class="flex w-full h-auto items-center px-4 py-2 rounded-md border border-gray-200 shadow-md space-x-4">
        <div class="flex">
            <img
                :src="props.profile_pic || defaultImageUrl"
                alt="Profile Picture"
                class="w-16 h-16 rounded-full object-cover"
            />
        </div>
        <div class="flex flex-1 flex-col text-lg">
            <h1 class="text-slate-900 font-bold">{{ props.tutorname }}</h1>
            <h2 class="text-slate-500 font-light">{{ props.cbnumber }}</h2>
            <h2 class="text-slate-500 font-light">{{props.school}}</h2>
        </div>
        <div>
            <PrimaryButton id="viewProfileBtn" class="!text-sm"><a :href="route('tutor.profile', props.tutor_id)">View Profile</a></PrimaryButton>
        </div>
    </div>
</template>
