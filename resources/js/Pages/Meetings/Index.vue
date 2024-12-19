<template>
    <AuthenticatedLayout>
        <div>
            <div id="jitsi-container" style="height: 600px; width: 100%;"></div>
        </div>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    components: { AuthenticatedLayout },
    props: {
        meetingUrl: String, // Meeting URL passed from the parent component
    },
    mounted() {
        console.log(this.meetingUrl);
        this.startJitsi();
    },
    methods: {
        startJitsi() {
            const domain = "meet.jit.si"; // Use your Jitsi domain if self-hosted
            const options = {
                roomName: this.meetingUrl.split("/").pop(),
                width: "100%",
                height: "100%",
                parentNode: document.getElementById("jitsi-container"),
                userInfo: {
                    displayName: "Your Name", // Customize the user's display name
                },
            };

            // Initialize Jitsi Meet External API
            new JitsiMeetExternalAPI(domain, options);
        },
    },
};
</script>
