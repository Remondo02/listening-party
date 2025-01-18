export default (startTimestamp) => ({
    audio: null,
    isLoading: true,
    isLive: false,
    isPlaying: false,
    isReady: false,
    currentTime: 0,
    startTimestamp: startTimestamp,

    initializeAudioPlayer() {
        this.audio = this.$refs.audioPlayer;
        if (!this.audio) {
            console.error("Audio player element is missing");
            return;
        }

        this.audio.addEventListener("loadedmetadata", () => {
            this.isLoading = false;
            this.checkAndPlayAudio();
        });

        this.audio.addEventListener("timeupdate", () => {
            this.currentTime = this.audio.currentTime;
        });

        this.audio.addEventListener("play", () => {
            this.isPlaying = true;
        });

        this.audio.addEventListener("pause", () => {
            this.isPlaying = false;
        });
    },

    checkAndUpdate() {
        const now = Math.floor(Date.now() / 1000);
        const timeUntilStart = this.startTimestamp - now;

        if (timeUntilStart <= 0) {
            if (!this.isPlaying) {
                this.isLive = true;
                if (this.isReady) {
                    this.audio
                        .play()
                        .catch((error) =>
                            console.error("Playback failed:", error)
                        );
                }
            }
        } else {
            const days = Math.floor(timeUntilStart / 86400);
            const hours = Math.floor((timeUntilStart % 86400) / 3600);
            const minutes = Math.floor((timeUntilStart % 3600) / 60);
            const seconds = timeUntilStart % 60;

            this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }
    },

    checkAndPlayAudio() {
        const elapsedTime = Math.max(
            0,
            Math.floor(Date.now() / 1000) - this.startTimestamp
        );

        if (elapsedTime >= 0) {
            this.audio.currentTime = elapsedTime;
            this.audio
                .play()
                .catch((error) => console.error("Playback failed:", error));
        } else {
            setTimeout(() => this.checkAndPlayAudio(), 1000);
        }
    },

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, "0")}`;
    },
});
