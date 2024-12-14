<script setup>
import { UserCircleIcon } from '@heroicons/vue/24/solid';
import PrimaryButton from './PrimaryButton.vue';
</script>

<template>
    <div class="flex w-full h-auto items-center px-4 py-2 rounded-md border border-gray-200 shadow-md space-x-4">
        <div class="flex">
            <UserCircleIcon class="w-20" />
        </div>
        <div class="flex flex-1 flex-col text-lg">
            <h1 class="text-slate-900 font-bold">Name</h1>
            <h2 class="text-slate-500 font-light">CB Number</h2>
            <h2 class="text-slate-500 font-light">School of Study</h2>
        </div>
        <div>
            <PrimaryButton class="!text-sm">View Profile</PrimaryButton>
        </div>
    </div>
</template>