<script setup>
import { usePage } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Get courses and levels from the page props
const pageProps = usePage().props;

const validInputs = {
    school : pageProps.SchoolOfStudy,
    degree : pageProps.DegreeProgram,
    levels : pageProps.Level,
    semester : pageProps.Semester,
};
console.log(validInputs); // Log to verify the data

// Initialize the form
const form = useForm({
    school: '',
    degree: '',
    level: '',
    semester: '',
    profile_pic: null,
    cb_number: '', // Will be set automatically from email
});

// Extract CB Number from the email automatically
const userEmail = usePage().props.auth.user.email;
const cbNumber = userEmail.split('@')[0]; // Assuming CB Number is part of the email prefix
form.cb_number = cbNumber; // Set CB Number in the form

// Handle form submission
const submitForm = () => {
    form.post(route('profile.store'), {
        onFinish: () => form.reset(),
    });
};

</script>

<template>
    <section>
        <header>
            <h2 class="text-7xl font-black text-gray-900">Create Your Profile</h2>
            <p class="mt-1 text-md text-gray-600">
                Please fill in the details below to complete your profile.
            </p>
        </header>

        <form @submit.prevent="submitForm" class="flex flex-col mt-6 gap-6">
            <!-- Courses -->
            <div>
                <InputLabel for="School" value="School of Study" />
                <select
                    id="school"
                    class="cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                    v-model="form.school"
                    required
                >
                    <option value="">Select School</option>
                    <option v-for="schools in validInputs.school" :key="schools.id" :value="schools.id">
                        {{ schools.school_name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.school" />
            </div>

            <!-- Degree Program -->
            <div>
                <InputLabel for="degree_program" value="Degree Program" />
                <select
                    id="degree_program"
                    class="cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                    v-model="form.degree"
                    required
                >
                    <option value="">Select Level</option>
                    <option
                        v-for="degrees in validInputs.degree.filter(l => l.school_id === form.school)"
                        :key="degrees.id"
                        :value="degrees.id"
                    >
                        {{ degrees.degree_name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.degree" />
            </div>
            <!-- Level -->
            <div>
                <InputLabel for="level" value="Level" />
                <select
                    id="level"
                    class="cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                    v-model="form.level"
                    required
                >
                    <option value="">Select Level</option>
                    <option
                        v-for="levels in validInputs.levels"
                        :key="levels.id"
                        :value="levels.id"
                    >
                        {{ levels.level_name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.level" />
            </div>

            <!-- Semester -->
            <div>
                <InputLabel for="semester" value="Semester" />
                <select
                    id="semester"
                    class="cursor-pointer mt-1 block w-full text-lg shadow-sm border-gray-300 rounded-md hover:border-slate-500 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-slate-500"
                    v-model="form.semester"
                    required
                >
                    <option value="">Select Semester</option>
                    <option
                        v-for="semesters in validInputs.semester"
                        :key="semesters.id"
                        :value="semesters.id"
                    >
                        {{ semesters.semester_name }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.semester" />
            </div>

            <!-- Submit Button -->
            <div class="flex mt-2 w-full justify-end">
                <PrimaryButton :disabled="form.processing">Create Profile</PrimaryButton>
            </div>
        </form>
    </section>
</template>
