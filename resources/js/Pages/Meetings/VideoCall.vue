<template>
    <div>
        <div id="jitsi-container" style="height: 600px; width: 100%;"></div>
    </div>
</template>

<script>
export default {
    props: {
        meetingUrl: String,
    },
    mounted() {
        this.startJitsi();
    },
    methods: {
        startJitsi() {
            const domain = "meet.jit.si"; // Use your Jitsi domain if self-hosted
            const options = {
                roomName: this.meetingUrl.split('/').pop(),
                width: "100%",
                height: "100%",
                parentNode: document.getElementById("jitsi-container"),
                userInfo: {
                    displayName: "Your Name", // Customize user display name
                },
            };
            new JitsiMeetExternalAPI(domain, options);
        },
    },
};
</script>
