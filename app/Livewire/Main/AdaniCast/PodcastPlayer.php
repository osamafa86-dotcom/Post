<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\Material;
use App\Models\PodcastTrack;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class PodcastPlayer extends Component
{
    public $podcasts;

    #[On('playAudio')]
    public function playAudio($podcastId)
    {
        $this->podcasts = Material::where('id', $podcastId['podcastId'])
            ->with(['files.file', 'presenter.relationable'])
            ->first();
        $filePath = $this->podcasts?->files?->where('model_column', 'file')?->first()?->file?->path;
        Cache::put('podcast_id', $podcastId['podcastId'], now()->addMinutes(10));
        $this->dispatch('refreshPodcastButton');
        $this->dispatch('feedAudioPlayer', ['path' => $filePath]);
    }

    public function togglePlayPause()
    {
        if (Cache::get('podcast_id')) {
            Cache::forget('podcast_id');
            $this->dispatch('pauseAudioPlayer');
        } else {
            if (!$this->podcasts?->id) return;
            Cache::put('podcast_id', $this->podcasts->id, now()->addMinutes(10));
            $filePath = $this->podcasts?->files?->where('model_column', 'file')?->first()?->file?->path;
            $this->dispatch('feedAudioPlayer', ['path' => $filePath]);
        }

        $this->dispatch('refreshPodcastButton');
    }

    public function mount(PodcastTrack $podcasts)
    {
        $this->podcasts = $podcasts;
    }

    public function formatDurationAsTime($seconds)
    {
        if (!$seconds || $seconds <= 0) {
            return '00:00'; // Default for invalid or empty duration
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        // Format with leading zeros
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }

    public function render()
    {
        return view('livewire.main.adani-cast.podcast-player');
    }
}
