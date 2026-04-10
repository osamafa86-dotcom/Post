<?php

namespace App\Livewire\Main\Hongora;

use App\Models\Material;
use App\Models\PodcastTrack;
use Livewire\Attributes\On;
use Livewire\Component;

class PodcastPlayer extends Component
{
    public Material $podcasts;

    #[On('playAudio')]
    public function playAudio($podcastId)
    {
        $this->podcasts = Material::where('id', $podcastId)
            ->with(['files.file', 'material_album', 'presenter.relationable', 'category.relationable', 'guest.relationable'])
            ->first();
        $filePath = $this->podcasts?->files?->where('model_column', 'file')?->first()?->file?->path;
        $this->dispatch('feedAudioPlayer', ['path' => $filePath]);
    }

    public function mount(Material $podcasts)
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
        return view('livewire.main.hongora.podcast-player');
    }
}
