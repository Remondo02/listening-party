<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\ListeningParty;
use App\Models\Episode;

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
            'media_url' => $this->medialUrl,
        ])

        $listeningParty = ListeningParty::create([
            'episode_id' => $episode->id,
            'name' => $this->name,
            'start_tile' => $this->startTime,
        ])

        // First, check that there are not existing episodes with the same URL.
        // If there is, use that, if not, create a new one.
        // When a new episode is created, grab information with a background job.
        // Then use that information to create a new listening party.

    }

    public function with()
    {
        return [
            'listening_parties' => ListeningParty::all(),
        ];
    }
}; ?>

<div class="flex items-center justify-center min-h-screen bg-slate-50">
    <div class="max-w-lg w-full px-4">
        <form wire:submit="createListeningParty" class="space-y-6">
            <x-input wire:model="name" placeholder="Listening Party Name" />
            <x-input wire:model="mediaUrl" placeholder="Podcast Episode URL"
                description="Direct episode link or YouTube link, RSS feeds will grab the latest episode" />
            <x-datetime-picker wire:model="startTime" placeholder="Listening PartyStart Time" />
            <x-button type="submit">Create Listening Party</x-button>
        </form>
    </div>
</div>
