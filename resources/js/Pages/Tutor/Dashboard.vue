<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps(
    {
       'approvedModules' : {
           type: Object,
           required: true
       },
         'rejectedModules' : {
              type: Object,
              required: true
         },
    }

);
const TutorRequest = id => {
    router.post(`/admin/request/${id}`, {},
        {
            onSuccess: () => {
                console.log("Request sent");
            },
        }
    );
};


</script>

<template>
    <Head title="Tutor Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-2xl mx-5 my-6">
            <h1 class="text-2xl font-semibold max-w-2xl">Approved Modules</h1>
            <div v-for="module in props.approvedModules" :key="module.id" class="my-3 mx-5 ">
                <p class="max-w-2xl">{{ module.module_name }}</p>
            </div>

            <h1 class="text-2xl font-semibold max-w-2xl">Rejected Modules</h1>
            <div v-for="module in props.rejectedModules" :key="module.id">
                <div class="flex justify-between items-center my-3 mx-5">
                    <p class="max-w-2xl">{{ module.module_name }}</p>
                    <secondary-button @click="TutorRequest(module.id)">Request Again</secondary-button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
