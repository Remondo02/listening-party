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
            'listeningParties' => ListeningParty::where('is_active', true)->orderBy('start_time', 'ASC')->with('episode.podcast')->get(),
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
            <h3 class="mb-8 font-serif font-bold">Ongoing Listening Parties</h3>
            <div class="bg-white rounded-lg shadow-lg">
                @if ($listeningParties->isEmpty())
                    <div>No awwdio listening parties started yet... 😔</div>
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
                                            <p class="text-[0.9rem] font-semibold truncate text-slate-900">{{ $listeningParty->name }}</p>
                                            <div class="mt-0.8">
                                                <p class="text-sm text-slate-600 truncate">{{ $listeningParty->episode->title }}</p>
                                                <p class="text-slate-400 uppercase tracking-tighter text-[0.7rem]">{{ $listeningParty->episode->podcast->title }}</p>
                                            </div>
                                            <p class="mt-1 text-xs">{{ $listeningParty->start_time }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
