<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\Material;
use App\Models\PodcastTrack;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use getID3;

class SinglePodcast extends Component
{
    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $slug;
    public $isPlaying = false;

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->isPlaying){
            $this->dispatch('playAudio', ['podcastId' => $this->id]);
            $this->isPlaying = true;
        }

        return view('livewire.main.adani-cast.single-podcast');
    }

    public function addFavorite($id)
    {
        $sessionId = session()->getId();
        $key = "favorites_session_{$sessionId}";
        $favorites = Cache::get($key, []);
        if (in_array($id, $favorites)) {
            $favorites = array_diff($favorites, [$id]);
        } else {
            $favorites[] = $id;
        }
        Cache::put($key, $favorites);
        $this->dispatch('updateFavoriteList');
    }

    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');
    }
    public function play(): void
    {
        if (Cache::has('podcast_id')){
            if (Cache::get('podcast_id') != $this->id){
                $this->dispatch('playAudio', ['podcastId' => $this->id]);
            }else{
                Cache::forget('podcast_id');
                $this->dispatch('pauseAudioPlayer');
                $this->dispatch('refreshPodcastButton');
            }
        }else{
            $this->dispatch('playAudio', ['podcastId' => $this->id]);
        }
    }

    #[Computed]
    public function podcast_track()
    {
        $material= Material::with([
            'material_album',
            'files.file',
            'guests.relationable.files.file',
            'presenters.relationable',
            'presenter.relationable',
            'category.relationable',
            'tags.relationable',
        ])->where(function ($q) {
            $q->where('id', $this->id);
            $q->where('title', $this->slug);
        })->first();

        if (!Session::has('viewed_podcast_' . $material->id)) {
            $now = Carbon::now();
            $threshold = $now->copy()->subHours(config('app.views_hours'));
            $view = $material->views()
                ->where('last_viewed_at', '>', $threshold)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($view) {
                $view->increment('views_number');
                $view->update(['last_viewed_at' => $now]);
            } else {
                $material->views()->create([
                    'last_viewed_at' => $now,
                    'views_number'   => 1,
                ]);
            }
            Session::put('viewed_podcast_' . $material->id, [
                'viewed' => true,
                'expires_at' => now()->addHours((int) config('app.view_expiration_hours'))
            ]);
        }        return $material;
    }

    public function formatDurationInArabic($seconds)
    {
        if (!$seconds || $seconds <= 0) {
            return '0 ثانية';
        }
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        $formattedDuration = '';
        if ($hours > 0) {
            $formattedDuration .= $hours . ' ' . ($hours === 1 ? 'ساعة' : 'ساعات');
        }
        if ($minutes > 0) {
            if (!empty($formattedDuration)) {
                $formattedDuration .= ' و';
            }
            $formattedDuration .= $minutes . ' ' . ($minutes === 1 ? 'دقيقة' : 'دقائق');
        }
        if ($remainingSeconds > 0) {
            if (!empty($formattedDuration)) {
                $formattedDuration .= ' و';
            }
            $formattedDuration .= $remainingSeconds . ' ' . ($remainingSeconds === 1 ? 'ثانية' : 'ثواني');
        }
        return $formattedDuration;
    }

    public function formatDurationInArabicFromFiles($files)
    {
        if (!$files || $files->isEmpty()) {
            return '0 ثانية';
        }
        $totalSeconds = 0;
        $getID3 = new getID3;
        foreach ($files as $file) {
            if (!$file?->file->path) {
                continue;
            }
            try {
                $info = $getID3->analyze(storage_path('app/public/uploads/' . $file?->file?->path));
                if (!empty($info['playtime_seconds'])) {
                    $totalSeconds += (int) $info['playtime_seconds'];
                }
            } catch (\Exception $e) {
                // ignore file if cannot analyze
            }
        }
        return $this->formatDurationInArabic($totalSeconds);
    }
}
