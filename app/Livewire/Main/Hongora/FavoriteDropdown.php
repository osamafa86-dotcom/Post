<?php

namespace App\Livewire\Main\Hongora;

use App\Models\Material;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class FavoriteDropdown extends Component
{

    #[Computed]
    public function favorites()
    {
        $sessionId = session()->getId();
        $key = "favorites_session_{$sessionId}";
        $favorites=Cache::get($key, []);
        return Material::whereIn('id', $favorites)->get();
    }

    #[On('updateFavoriteList')]
    public function updateFavoriteList()
    {
        unset($this->favorites);
    }

    public function removeFromFavorites($id)
    {
        $sessionId = session()->getId();
        $key = "favorites_session_{$sessionId}";
        $favorites = Cache::get($key, []);
        $favorites = array_diff($favorites, [$id]);
        Cache::put($key, $favorites);
        $this->dispatch('updatePodcastFavoriteList');
    }

    public function render()
    {
        return view('livewire.main.hongora.favorite-dropdown');
    }
}
