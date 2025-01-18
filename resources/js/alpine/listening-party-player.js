export default ({ start, end, wire }) => ({
    audio: null,
    isLoading: true,
    isLive: false,
    isPlaying: false,
    isReady: false,
    currentTime: 0,
    countdownText: "",
    startTimestamp: start,
    endTimestamp: end,
    $wire: wire,
    copyNotification: false,

    init() {
        this.startCountdown();
        if (this.$refs.audioPlayer && !this.isFinished) {
            this.initializeAudioPlayer();
        }
    },

    initializeAudioPlayer() {
        // Use Alpine's nextTick to wait until the element is available in the DOM
        this.$nextTick(() => {
            this.audio = this.$refs.audioPlayer;
            if (!this.audio) {
                console.error("Audio player element is missing");
                return;
            }

            this.audio.addEventListener("loadedmetadata", () => {
                this.isLoading = false;
                this.checkLiveStatus();
            });

            this.audio.addEventListener("timeupdate", () => {
                this.currentTime = this.audio.currentTime;
                if (
                    this.endTimestamp &&
                    this.currentTime >= this.endTimestamp - this.startTimestamp
                ) {
                    this.finishListeningParty();
                }
            });

            this.audio.addEventListener("play", () => {
                this.isPlaying = true;
                this.isReady = true;
            });

            this.audio.addEventListener("pause", () => {
                this.isPlaying = false;
            });

            this.audio.addEventListener("ended", () => {
                this.finishListeningParty();
            });
        });
    },

    finishListeningParty() {
        this.$wire.isFinished = true;
        this.$wire.$refresh();
        this.isPlaying = false;
        if (this.audio) {
            this.audio.pause();
        }
    },

    startCountdown() {
        this.checkLiveStatus();
        setInterval(() => this.checkLiveStatus(), 1000);
    },

    checkLiveStatus() {
        const now = Math.floor(Date.now() / 1000);
        const timeUntilStart = this.startTimestamp - now;

        if (timeUntilStart <= 0) {
            this.isLive = true;
            if (this.audio && !this.isPlaying && !this.isFinished) {
                this.playAudio();
            }
        } else {
            const days = Math.floor(timeUntilStart / 86400);
            const hours = Math.floor((timeUntilStart % 86400) / 3600);
            const minutes = Math.floor((timeUntilStart % 3600) / 60);
            const seconds = timeUntilStart % 60;
            this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            setTimeout(() => this.checkLiveStatus(), 1000);
        }
    },

    playAudio() {
        if (!this.audio) return;
        const now = Math.floor(Date.now() / 1000);
        const elapsedTime = Math.max(0, now - this.startTimestamp);
        this.audio.currentTime = elapsedTime;
        this.audio.play().catch((error) => {
            console.error("Playback failed:", error);
            this.isPlaying = false;
            this.isReady = false;
        });
    },

    joinAndBeReady() {
        this.isReady = true;
        if (this.isLive && this.audio && !this.isFinished) {
            this.playAudio();
        }
    },

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, "0")}`;
    },

    copyToClipboard() {
        if (navigator.clipboard) {
            navigator.clipboard
                .writeText(window.location.href)
                .then(() => {
                    this.copyNotification = true;
                    setTimeout(() => {
                        this.copyNotification = false;
                    }, 3000);
                })
                .catch((error) => {
                    console.error("Failed to copy to clipboard:", error);
                });
        } else {
            console.error("Clipboard API is not available.");
        }
    },
});
