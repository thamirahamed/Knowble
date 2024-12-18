<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">Jitsi Meetings</h1>
        <button
            @click="createMeeting"
            class="px-4 py-2 bg-blue-600 text-white rounded-md"
        >
            Create Meeting
        </button>

        <div v-if="meetingUrl" class="mt-4">
            <VideoCall :meetingUrl="meetingUrl" />
        </div>
    </div>
</template>

<script>
import VideoCall from './VideoCall.vue';

export default {
    components: { VideoCall },
    data() {
        return {
            meetingUrl: null,
        };
    },
    methods: {
        async createMeeting() {
            try {
                const response = await axios.post(route('meetings.create'), {
                    host_name: 'Your Name',
                });
                this.meetingUrl = response.data.meeting_url;
            } catch (error) {
                console.error('Error creating meeting:', error);
            }
        },
    },
};
</script>
