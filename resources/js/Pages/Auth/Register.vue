<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="flex flex-col w-full gap-3">
            <h1 class="text-7xl tracking-wide text-gray-900 font-semibold ">Register</h1>
        </div>

        <form @submit.prevent="submit" class="text-xl">
            <div class="my-6">
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="John Doe"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="my-6">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="Enter email here"

                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="my-6">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="Enter password Here"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="my-6">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm password here"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-8 flex flex-col flex-grow-1 items-center gap-6">
                <PrimaryButton
                    class="w-full text-2xl"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>

                <Link
                    :href="route('login')"
                    class="text-gray-700 underline hover:text-accentdark"
                >
                    Already registered?
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
