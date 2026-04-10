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
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use getID3;

class AddWork extends Component
{
    use WithFileUploads;

    public $state = [];
    public $id;
    public $newImage;
    public $hongoraUserSound;

    #[Layout('components.layouts.main.hongora.user_dashboard.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.user_dashboard.add-work');
    }

    public function mount()
    {
        if ($this->id) {
            $this->hongoraUserSound = HongoraUserSound::find($this->id);
            $this->state = $this->hongoraUserSound->toArray();
        }
    }

    public function create()
    {
        $validate = Validator::make($this->state, [
            'title' => 'required|string|max:100',
            'newImage' => ($this->id ? 'nullable' : 'required') . '|image|max:2048',
            'file' => ($this->id ? 'nullable' : 'required|file|max:20480'),
            'description' => 'required|string|max:700',
        ])->validate();

        $image = $this->state['image'] ?? null;
        if ($this->newImage) {
            $image = $this->newImage->store('HongoraUserSound', config('filesystems.default'));
        }
        $file = $this->state['file'] ?? null;
        $fileType = null;
        $fileDuration = null;
        if (isset($this->state['file']) && is_object($this->state['file'])) {
            $file = $this->state['file']->store('HongoraUserSound', config('filesystems.default'));
            $getID3 = new getID3;
            $filePath = file_url($file);
            $info = $getID3->analyze($filePath);
            $fileType = $info['mime_type'] ?? 'unknown';
            $fileDuration = $info['playtime_seconds'] ?? 0;
        }
        if ($this->id) {
            $record = HongoraUserSound::findOrFail($this->id);
            $record->update([
                'title' => $validate['title'],
                'image' => $image ?? $record->image,
                'file' => $file ?? $record->file,
                'description' => $validate['description'],
                'file_type' => $fileType ?? $record->file_type,
                'file_duration' => $fileDuration ?? $record->file_duration,
            ]);
        } else {
            HongoraUserSound::create([
                'title' => $validate['title'],
                'image' => $image,
                'file' => $file,
                'description' => $validate['description'],
                'is_active' => 0,
                'user_id' => Auth::id(),
                'file_type' => $fileType,
                'file_duration' => $fileDuration,
            ]);
            $this->reset('state', 'newImage');
        }
        $this->dispatch('add_success');
        // return redirect()->route('main.hongora.user_dashboard_works_list');
    }
}
