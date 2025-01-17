export default (startTime, isLive) => ({
    startTime: startTime,
    isLive: isLive,
    countdownText: "",
    updateCountdown() {
        const start = new Date(this.startTime).getTime();
        const now = new Date().getTime();
        const distance = start - now;

        if (distance < 0) {
            this.countdownText = "Started";
            this.isLive = true;
        } else {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            const minutes = Math.floor(
                (distance % (1000 * 60 * 60)) / (1000 * 60)
            );
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            this.countdownText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
        }
    },
    init() {
        this.updateCountdown();
        setInterval(() => this.updateCountdown(), 1000);
    },
});
