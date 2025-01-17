<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\ListeningParty;
use App\Models\Episode;
use App\Jobs\ProcessPodcastUrl;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required')]
    public $startTime;

    #[Validate('required|url')]
    public string $mediaUrl = '';

    public function createListeningParty()
    {
        $this->validate();

        $episode = Episode::create([
            'media_url' => $this->mediaUrl,
        ]);

        $listeningParty = ListeningParty::create([
            'episode_id' => $episode->id,
            'name' => $this->name,
            'start_time' => $this->startTime,
        ]);

        ProcessPodcastUrl::dispatch($this->mediaUrl, $listeningParty, $episode);

        return redirect()->route('parties.show', $listeningParty);
    }

    public function with()
    {
        return [
            'listeningParties' => ListeningParty::where('is_active', true)->whereNotNull('end_time')->orderBy('start_time', 'ASC')->with('episode.podcast')->get(),
        ];
    }
}; ?>

<div class="min-h-screen bg-emerald-50 flex flex-col pt-8">
    {{-- Top half: create new listening party form  --}}
    <div class="flex items-center justify-center p-4">
        <div class="w-full max-w-lg">
            <x-card shadow="lg" rounded="lg">
                <h2 class="text-xl font-bold font-serif text-center">Let's listen together</h2>
                <form wire:submit="createListeningParty" class="space-y-6 mt-6">
                    <x-input wire:model="name" placeholder="Listening Party Name" />
                    <x-input wire:model="mediaUrl" placeholder="Podcast RSS Feed URL"
                        description="Entering the RSS feed will grab the latest episode" />
                    <x-datetime-picker wire:model="startTime" placeholder="Listening PartyStart Time"
                        :min="now()->subDays(1)" />
                    <x-button type="submit" class="w-full">Create Listening Party</x-button>
                </form>
            </x-card>
        </div>
    </div>
    {{-- Bottom half: existing listening parties --}}
    <div class="my-20">
        <div class="max-w-lg mx-auto">
            <h3 class="mb-4 font-serif text-[0.9rem] font-bold">Ongoing Listening Parties</h3>
            <div class="bg-white rounded-lg shadow-lg">
                @if ($listeningParties->isEmpty())
                    <div class="flex items-center justify-center p-4 font-serif text-sm">No listening parties
                        started yet... 😔</div>
                @else
                    @foreach ($listeningParties as $listeningParty)
                        <div wire:key="{{ $listeningParty->id }}">
                            <a href="{{ route('parties.show', $listeningParty) }}" class="block">
                                <div
                                    class="flex items-center justify-between p-4 transition-all border-b border-gray-200 hover:bg-gray-50 duration-150 ease-in-out">
                                    <div class="flex items-center space-x-4 ">
                                        <div class=" flex-shrink-0">
                                            <x-avatar src="{{ $listeningParty->episode->podcast->artwork_url }}"
                                                size="xl" rounded="sm" alt="Podcast Artwork" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[0.9rem] font-semibold truncate text-slate-900">
                                                {{ $listeningParty->name }}</p>
                                            <div class="mt-0.8">
                                                <p class="text-sm max-w-xs text-slate-600 truncate">
                                                    {{ $listeningParty->episode->title }}</p>
                                                <p class="text-slate-400 uppercase tracking-tighter text-[0.7rem]">
                                                    {{ $listeningParty->episode->podcast->title }}</p>
                                            </div>
                                            <div class="text-xs text-slate-600 mt-1" x-data="listeningPartyCountdown('{{ $listeningParty->start_time->toIso8601String() }}', {{ $listeningParty->start_time->isPast() && $listeningParty->is_active ? 'true' : 'false' }})"
                                                x-init="updateCountdown()">
                                                <div x-show="isLive">
                                                    <x-badge flat rose label="Live">
                                                        <x-slot name="prepend"
                                                            class="relative flex items-center w-2 h-2">
                                                            <span
                                                                class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-rose-500 animate-ping"></span>
                                                            <span
                                                                class="relative inline-flex w-2 h-2 rounded-full bg-rose-500"></span>
                                                        </x-slot>
                                                    </x-badge>
                                                </div>
                                                <div x-show="!isLive">
                                                    Starts in: <span x-text="countdownText"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <x-button flat xs class="w-20">Join</x-button>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
