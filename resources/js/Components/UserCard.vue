
<script setup>
import { CheckBadgeIcon, StarIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from './PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    profile: Object,
    user_id: Number,
    username: String,
    isTutor: Boolean,
});

const viewHistory = (userid) => {
    router.visit(route('admin.user.history', { id: userid }));
}

</script>

<template>
    <div class="flex w-full h-auto items-center px-4 py-2 rounded-md border border-gray-200 shadow-md space-x-4">
        <div class="flex">
            <img
                :src="props.profile.profile_pic"
                alt="Profile Picture"
                class="w-16 h-16 rounded-full object-cover"
            />
        </div>
        <div class="flex flex-1 flex-col text-lg">
            <div class="flex flex-row items-center">
                <h1 class="text-slate-900 font-semibold">{{ props.username }}</h1>
                <CheckBadgeIcon v-if="props.isTutor === 'Yes'" class="ml-1 w-5 h-5 text-accent" />
            </div>
            <p class="text-slate-500 font-light">{{ props.profile.cb_number }}</p>
        </div>
        <div>
            <PrimaryButton
                :id="'viewProfileBtn-' + user_id"
                class="!text-sm"
                @click="viewHistory(user_id)"
            >
                View History
            </PrimaryButton>
        </div>
    </div>
</template>
