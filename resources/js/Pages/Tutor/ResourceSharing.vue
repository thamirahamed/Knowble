<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    resourceShares: {
        type: Object,
        required: true,
    },
});

// Reactive state for form data
const form = ref({
    fileName: "",
    file: null,
});

// Handles file selection
const handleFileChange = (event) => {
    form.value.file = event.target.files[0]; // Capture the selected file
};

// Submit the form
const submitForm = () => {
    // Create FormData object to handle file uploads
    const formData = new FormData();
    formData.append("fileName", form.value.fileName); // Add title
    formData.append("file", form.value.file);   // Add file

    // Post the form data using Inertia
    router.post(route("resource-shares.store"), formData, {
        preserveScroll: true, // Keeps scroll position after form submission
        onSuccess: () => {
            alert("File uploaded successfully!");
        },
        onError: (errors) => {
            alert("Failed to upload file!");
            console.error(errors);
        },
    });

    form.value.fileName = ""; // Clear the file name
    form.value.file = null;   // Clear the file
};
// Delete file function
const deleteFile = (id) => {
    if (confirm("Are you sure you want to delete this file?")) {
        router.delete(route("resource-shares.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                alert("File deleted successfully!");
            },
            onError: (errors) => {
                alert("Failed to delete file!");
                console.error(errors);
            },
        });
    }
};
</script>
<template>
    <form @submit.prevent="submitForm" class="flex justify-between items-center align-middle space-x-4">
        <!-- File Name Input -->
        <div class="flex flex-col w-1/2">
            <label for="fileName" class="text-sm font-semibold mb-1">File Name</label>
            <input
                type="text"
                v-model="form.fileName"
                id="fileName"
                class="border border-gray-300 rounded-md p-2"
            />
        </div>

        <!-- File Input -->
        <div class="flex flex-col w-1/2">
            <label for="file" class="text-sm font-semibold mb-1">File</label>
            <input
                type="file"
                @change="handleFileChange"
                id="file"
                class="border border-gray-300 rounded-md p-2"
                accept=".jpg, .jpeg, .png, .pdf, .doc, .docx"
            />
        </div>

        <!-- Submit Button -->
        <div>
            <PrimaryButton
                type="submit"
                class="text-white rounded-md p-2 px-6 hover:bg-primary-dark transition duration-300"
            >
                Submit
            </PrimaryButton>
        </div>
    </form>

    <!-- Files Table -->
    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-200 shadow-md rounded-md">
            <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">File Name</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Uploaded Date</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="resource in props.resourceShares" :key="resource.id" class="hover:bg-gray-50">
                <!-- File Name -->
                <td class="border border-gray-300 px-4 py-2 text-sm">{{ resource.fileName }}</td>

                <!-- Uploaded Date -->
                <td class="border border-gray-300 px-4 py-2 text-sm">
                    {{ new Date(resource.created_at).toLocaleDateString() }}
                </td>

                <!-- Actions -->
                <td class="border border-gray-300 px-4 py-2 text-sm flex space-x-4">
                    <!-- Download Button -->
                    <a
                        :href="route('resource-shares.download', resource.id)"
                    class="text-blue-500 hover:underline"
                    >
                    Download
                    </a>

                    <!-- Delete Button -->
                    <button
                        @click="deleteFile(resource.id)"
                        class="text-red-500 hover:underline"
                    >
                        Delete
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</template>
