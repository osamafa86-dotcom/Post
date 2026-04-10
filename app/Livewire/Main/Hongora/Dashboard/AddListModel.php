<?php

namespace App\Livewire\Main\Hongora\Dashboard;

use App\Models\HongoraUserSound;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use getID3;

class AddListModel extends Component
{
    use WithFileUploads;

    public $state = [];

    public $id;
    public $isEditing=false;

    public $hongoraUserSound;

    public function mount($id = null)
    {
        $this->id = $id;
        if ($this->id) {
            $this->hongoraUserSound = HongoraUserSound::find($this->id);
            $this->state = $this->hongoraUserSound->toArray();
        }
    }

    #[On('editingWork')]
    public function editingWork($id = null)
    {
        $this->isEditing=true;
        $this->id = $id;
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
            'views' => 'required|integer|min:0',
        ])->validate();
        $image = $this->state['newImage'] ?? null;
        if ($image) {
            $image = $image->store('HongoraUserSound', 'public');
        }
        $file = $this->state['file'] ?? null;
        $fileType = null;
        $fileDuration = null;
        if (isset($this->state['file']) && is_object($this->state['file'])) {
            $file = $this->state['file']->store('HongoraUserSound', 'public');
            $getID3 = new getID3;
            $filePath = storage_path('app/public/' . $file);
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
                'views' => $validate['views'],
                'description' => $validate['description'],
                'file_type' => $fileType ?? $record->file_type,
                'file_duration' => $fileDuration ?? $record->file_duration,
            ]);
        } else {
            HongoraUserSound::create([
                'title' => $validate['title'],
                'image' => $image,
                'file' => $file,
                'views' => $validate['views'],
                'description' => $validate['description'],
                'is_active' => 0,
                'user_id' => Auth::id(),
                'file_type' => $fileType,
                'file_duration' => $fileDuration,
            ]);
        }
        $this->reset('state');
        $this->dispatch('add_success');

    }

    public function render()
    {
        return view('livewire.main.hongora.dashboard.add-list-model');
    }
}
