<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";
import { defineProps } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    profile: {
        type: Object,
        required: true,
    },
    courses: {
        type: Array,
        required: true
    },
    levels: {
        type: Array,
        required: true
    }
});

</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow rounded-lg sm:p-8 border">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        :profile="profile"
                        :courses="courses"
                        :levels="levels"
                        class="w-full"
                    />
                </div>

                <div class="bg-white p-4 shadow rounded-lg sm:p-8">
                    <UpdatePasswordForm class="w-full" />
                </div>           

                <div class="bg-white p-4 shadow rounded-lg sm:p-8">
                    <DeleteUserForm class="w-full" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
