import './bootstrap';

// Register the listeningPartyCountdown Alpine.js component.
// Handles countdown logic for listening parties.
import listeningPartyCountdown from './alpine/listening-party-countdown.js';

import listeningPartyPlayer from './alpine/listening-party-player.js';

Alpine.data('listeningPartyCountdown', listeningPartyCountdown);
Alpine.data('listeningPartyPlayer', listeningPartyPlayer);
