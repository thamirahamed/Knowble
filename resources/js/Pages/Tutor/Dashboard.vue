<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    approvedModules: {
        type: Array,
        required: true,
    },
    rejectedModules: {
        type: Array,
        required: true,
    },
});

const TutorRequest = (id) => {
    router.post(`/admin/request/${id}`, {}, {
        onSuccess: () => {
            console.log("Request sent");
        },
    });
};
</script>

<template>
    <Head title="Tutor Dashboard" />

    <AuthenticatedLayout>
        <div class="flex">
            <!-- Sidebar -->
            <div class="w-1/4 bg-gray-100 p-4">
                <h2 class="text-lg font-semibold mb-4">Navigation</h2>
                <ul>
                    <li class="mb-2">
                        <Link
                            href="/approved-modules"
                            class="text-blue-500 hover:underline"
                            :class="{ 'font-bold': $page.component === 'ApprovedModules' }"
                        >
                            Approved Modules
                        </Link>
                    </li>
                    <li class="mb-2">
                        <Link
                            href="/rejected-modules"
                            class="text-blue-500 hover:underline"
                            :class="{ 'font-bold': $page.component === 'RejectedModules' }"
                        >
                            Rejected Modules
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="w-3/4 p-4">
                <h1 class="text-2xl font-semibold">
                    {{ $page.component === 'ApprovedModules' ? 'Approved Modules' : 'Rejected Modules' }}
                </h1>

                <div v-if="$page.component === 'ApprovedModules'">
                    <div v-for="module in approvedModules" :key="module.id" class="my-3">
                        <p class="text-gray-700">{{ module.module_name }}</p>
                    </div>
                </div>

                <div v-else-if="$page.component === 'RejectedModules'">
                    <div v-for="module in rejectedModules" :key="module.id" class="flex justify-between items-center my-3">
                        <p class="text-gray-700">{{ module.module_name }}</p>
                        <SecondaryButton @click="TutorRequest(module.id)">Request Again</SecondaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
