<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

</script>

<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />
        
        <div class="flex flex-col gap-10 items-center mt-10">
            <!--  -->
            <div class="flex flex-col container bg-white px-10 py-6 rounded-md shadow">
                <h1 class="text-3xl mb-1 tracking-wide font-bold">Upcoming Tutor Requests</h1>
                <p class="text-md mb-6 text-gray-500">Displays tutor requests that are awaiting review and approval.</p>
                <table class="min-w-full table-auto border-collapse">
                    <thead class="text-lg bg-secondary text-white">
                        <tr>
                            <th class="border-b font-semibold px-4 py-2 text-left">Request#</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Date & Time</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Name</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">CB Number</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">School of Study</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Year</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Status</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="even:bg-secondary/15">
                            <td class="border-b px-4 py-2">1</td>
                            <td class="border-b px-4 py-2">2024-11-20</td>
                            <td class="border-b px-4 py-2">John Doe</td>
                            <td class="border-b px-4 py-2">CB123456</td>
                            <td class="border-b px-4 py-2">School of Engineering</td>
                            <td class="border-b px-4 py-2">Year 2</td>
                            <td class="border-b px-4 py-2 space-x-4">
                                <PrimaryButton>
                                    Approve
                                </PrimaryButton>
                                <DangerButton>
                                    Reject
                                </DangerButton>
                            </td>
                            <td class="border-b px-4 py-2">
                                <button class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                        <tr class="even:bg-secondary/15">
                            <td class="border-b px-4 py-2">1</td>
                            <td class="border-b px-4 py-2">2024-11-20</td>
                            <td class="border-b px-4 py-2">John Doe</td>
                            <td class="border-b px-4 py-2">CB123456</td>
                            <td class="border-b px-4 py-2">School of Engineering</td>
                            <td class="border-b px-4 py-2">Year 2</td>
                            <td class="border-b px-4 py-2 space-x-4">
                                <PrimaryButton>
                                    Approve
                                </PrimaryButton>
                                <DangerButton>
                                    Reject
                                </DangerButton>
                            </td>
                            <td class="border-b px-4 py-2">
                                <button class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                        <!-- Repeat the above <tr> block for more rows -->
                    </tbody>
                </table>
            </div>

            <!-- All the Tutor  -->
            <div class="flex flex-col container bg-white px-10 py-6 rounded-md shadow">
                <h1 class="text-3xl mb-1 tracking-wide font-bold">Completed Tutor Requests</h1>
                <p class="text-md mb-6 text-gray-500">Stores all tutor requests that have been processed with either approval or rejection status.</p>
                <table class="min-w-full table-auto border-collapse">
                    <thead class="text-lg bg-secondary text-white">
                        <tr>
                            <th class="border-b font-semibold px-4 py-2 text-left">Request#</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Date & Time</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Name</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">CB Number</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">School of Study</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Year</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Status</th>
                            <th class="border-b font-semibold px-4 py-2 text-left">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="even:bg-secondary/15">
                            <td class="border-b px-4 py-2">1</td>
                            <td class="border-b px-4 py-2">2024-11-20</td>
                            <td class="border-b px-4 py-2">John Doe</td>
                            <td class="border-b px-4 py-2">CB123456</td>
                            <td class="border-b px-4 py-2">School of Engineering</td>
                            <td class="border-b px-4 py-2">Year 2</td>
                            <td class="border-b px-4 py-2 space-x-4">Approved</td>
                            <td class="border-b px-4 py-2">
                                <button class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                        <tr class="even:bg-secondary/15">
                            <td class="border-b px-4 py-2">1</td>
                            <td class="border-b px-4 py-2">2024-11-20</td>
                            <td class="border-b px-4 py-2">John Doe</td>
                            <td class="border-b px-4 py-2">CB123456</td>
                            <td class="border-b px-4 py-2">School of Engineering</td>
                            <td class="border-b px-4 py-2">Year 2</td>
                            <td class="border-b px-4 py-2 space-x-4">Rejected</td>
                            <td class="border-b px-4 py-2">
                                <button class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                        <!-- Repeat the above <tr> block for more rows -->
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

