export default (startTime, isLive) => ({
    startTime: startTime,
    isLive: isLive,
    countdownText: "",
    interval: null,

    updateCountdown() {
        const start = new Date(this.startTime).getTime();
        const now = new Date().getTime();
        const distance = start - now;

        if (isNaN(start)) {
            this.countdownText = "Invalid start time";
            return;
        }

        if (distance < 0) {
            this.countdownText = "Started";
            this.isLive = true;
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor(
            (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    },

    init() {
        this.updateCountdown();
        this.interval = setInterval(() => this.updateCountdown(), 1000);
    },

    destroy() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    },
});
