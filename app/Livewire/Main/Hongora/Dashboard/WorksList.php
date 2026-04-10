<?php

namespace App\Livewire\Main\Hongora\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Category;
use App\Models\HongoraUserSound;
use App\Models\Participant;
use App\Models\SpecialPage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class WorksList extends Component
{

    public $hongoraUserSound;

    #[Layout('components.layouts.main.hongora.user_dashboard.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.user_dashboard.works-list');
    }


    #[Computed]
    public function works()
    {
        return HongoraUserSound::where('user_id', Auth::id())->get();
    }

    public function editingWork($id)
    {
        $this->dispatch('editingWork', id: $id);
    }

    public function getDelete($id)
    {
        $this->hongoraUserSound = HongoraUserSound::find($id);
    }

    public function delete()
    {
        $this->hongoraUserSound->delete();

        $this->dispatch('hide_delete_modal');
    }
}
