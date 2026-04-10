<?php

namespace App\Livewire\Main\Hongora;

use App\Models\HongoraUserSound;
use App\Models\UserDetails;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Profile extends Component
{
    use WithPagination;
    public $item;

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.profile');
    }

    public function mount($id): void
    {
        $this->item = UserDetails::where('id', $id)->first();
    }
    #[Computed]
    public function userSounds()
    {
        return HongoraUserSound::where('user_id',$this?->item?->user_id)->paginate(8);
    }

}
