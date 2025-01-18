<?php

use Livewire\Volt\Component;
use App\Models\ListeningParty;

new class extends Component {
    public ListeningParty $listeningParty;

    public function mount(ListeningParty $listeningParty)
    {
        $this->listeningParty = $listeningParty->load('episode.podcast');
    }
}; ?>

<div x-data="listeningPartyPlayer({{ $listeningParty->start_time->timestamp }})" x-init="$nextTick(() => initializeAudioPlayer())">
    @if ($listeningParty->end_time === null)
        <div class="flex items-center justify-center p-6 font-serif text-sm" wire:poll.5s>
            Creating your <span class="font-bold">{{ $listeningParty->name }}</span>
        </div>
    @else
        <div>
            <audio x-ref="audioPlayer" :src="'{{ $listeningParty->episode->media_url }}'" preload="auto"></audio>
            <div>{{ $listeningParty->episode->podcast->title }}</div>
            <div>{{ $listeningParty->episode->title }}</div>
            <div>Current Time: <span x-text="formatTime(currentTime)"></span></div>
            <div>Start Time: {{ $listeningParty->start_time }}</div>
            <div x-show="isLoading">Loading...</div>
        </div>
    @endif
</div>
