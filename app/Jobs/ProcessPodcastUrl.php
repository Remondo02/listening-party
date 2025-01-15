<?php
namespace App\Jobs;

use App\Models\Podcast;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPodcastUrl implements ShouldQueue
{
    use Queueable;

    public $rssUrl;
    public $listeningParty;
    public $episode;

    /**
     * Create a new job instance.
     */
    public function __construct($rssUrl, $listeningParty, $episode)
    {
        $this->rssUrl         = $rssUrl;
        $this->listeningParty = $listeningParty;
        $this->episode        = $episode;
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

        $xml = simplexml_load_file($this->rssUrl);

        $postcastTitle     = $xml->channel->title;
        $podcastArtworkUrl = $xml->channel->image->url;

        $latestEpisode = $xml->channel->item[0];

        $episodeTitle    = $latestEpisode->title;
        $episodeMediaUrl = (string) $latestEpisode->enclosure['url'];

        // Register the itunes namespace to grab the duration
        $namespaces      = $xml->getNamespaces(true);
        $itunesNamespace = $namespaces['itunes'];

        $episodeLength = $latestEpisode->children($itunesNamespace)->duration;

        $interval = CarbonInterval::createFromFormat('H:i:s', $episodeLength);

        $endTime = $this->listeningParty->start_time->add($interval);

        // Save these to the database
        // Create the Podcast, and the update the episode to be linked to the podcast

        $podcast = Podcast::updateOrcreate([
            'title'       => $postcastTitle,
            'artwork_url' => $podcastArtworkUrl,
            'rss_url'     => $this->rssUrl,
        ]);

        $this->episode->podcast()->associate($podcast);
        $this->episode->update([
            'title'     => $episodeTitle,
            'media_url' => $episodeMediaUrl,
        ]);

        $this->listeningParty->update([
            'end_time' => $endTime,
        ]);

    }
}
