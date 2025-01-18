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

<div x-data="listeningPartyPlayer({{ $listeningParty->start_time->timestamp }})" x-init="initializeAudioPlayer()">
    @if ($listeningParty->end_time === null)
        <div class="flex items-center justify-center p-6 font-serif text-sm" wire:poll.5s>
            Creating your <span class="font-bold">{{ $listeningParty->name }}</span>
        </div>
    @else
        <audio x-ref="audioPlayer" :src="'{{ $listeningParty->episode->media_url }}'" preload="auto"></audio>

        <div x-show="!isLive" class="flex items-center justify-center min-h-screen bg-emerald-50">
            <div class="w-full max-w-2xl shadow-lg rounded-lg bg-white p-8">
                <div class="flex items-center space-x-4 ">
                    <div class="flex-shrink-0">
                        <x-avatar src="{{ $listeningParty->episode->podcast->artwork_url }}" size="xl"
                            rounded="sm" alt="Podcast Artwork" />
                    </div>
                    <div class="flex justify-between w-full items-center">
                        <div class="flex-1 min-w-0">
                            <p class="text-[0.9rem] font-semibold truncate text-slate-900">
                                {{ $listeningParty->name }}</p>
                            <div class="mt-0.8">
                                <p class="text-sm max-w-xs text-slate-600 truncate">
                                    {{ $listeningParty->episode->title }}</p>
                                <p class="text-slate-400 uppercase tracking-tighter text-[0.7rem]">
                                    {{ $listeningParty->episode->podcast->title }}</p>
                            </div>
                            <div class="text-xs text-slate-600 mt-1">
                            </div>
                        </div>
                        <p class="text-sm text-slate-600">Starts in: <span x-text="countdownText"></span></p>
                    </div>
                </div>
                <x-button x-show="!isReady" class="w-full mt-8" @click="joinAndBeReady()">Join and Be Ready</x-button>
                <h2 class="text-lg font-bolder text-slate-900 text-center font-serif tracking-tight mt-8" x-show="isReady">Ready to start the audio party! Stay tuned. 🫶</h2>
            </div>
        </div>
        <div x-show="isLive">
            <div>{{ $listeningParty->episode->podcast->title }}</div>
            <div>{{ $listeningParty->episode->title }}</div>
            <div>Current Time: <span x-text="formatTime(currentTime)"></span></div>
            <div>Start Time: {{ $listeningParty->start_time }}</div>
            <div x-show="isLoading">Loading...</div>
            <x-button x-show="!isReady" @click="joinAndBeReady()">Join and Be Ready</x-button>
        </div>
    @endif
</div>
