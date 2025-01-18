export default (startTime, isLive) => ({
    startTime: startTime,
    isLive: isLive,
    countdownText: "",
    interval: null,

    updateCountdown() {
        const now = Math.floor(Date.now() / 1000);
        const timeUntilStart = this.startTime - now;

        if (timeUntilStart <= 0) {
            this.isLive = true;
        } else {
            const days = Math.floor(timeUntilStart / 86400);
            const hours = Math.floor((timeUntilStart % 86400) / 3600);
            const minutes = Math.floor((timeUntilStart % 3600) / 60);
            const seconds = timeUntilStart % 60;

            this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }
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
