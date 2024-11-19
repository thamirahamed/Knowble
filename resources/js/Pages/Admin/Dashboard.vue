<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    components: { PrimaryButton },
    methods: {
        logout() {
            // Use Inertia to make a POST request to the logout route
            this.$inertia.post(this.route('logout'));
        }
    }
};
</script>

<template>

    <!-- logout button -->
    <PrimaryButton @click="logout">Logout</PrimaryButton>
</template>

