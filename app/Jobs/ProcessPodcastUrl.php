<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPodcastUrl implements ShouldQueue
{
    use Queueable;

    public $rssUrl;

    /**
     * Create a new job instance.
     */
    public function __construct($rssUrl)
    {
        $this->rssUrl = $rssUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Grab the podcast name information
        // Grab the latest episode
        // Add the latest episode media url to the existing episode
        // Update the existing episode's media url to the latest's episode's media url
        // Find the episodes length and set the listening end_time to the start_time + length of the episode
    }
}
