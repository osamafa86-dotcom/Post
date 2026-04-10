<?php

namespace App\Livewire\Main\Ufok;

use App\Enums\TagTypeEnum;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class Registration extends Component
{
    use WithFileUploads;

    public $state = [
        'portfolio' => null,
        'image' => null,
    ];
    public  $event_id , $event_title;

    #[Layout('components.layouts.main.ufok.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.ufok.registration');
    }

    public function mount($id, $title): void
    {
       $this->event_id = $id;
       $this->event_title = $title;
    }
    public function create():void
    {

        $validate = Validator::make($this->state,[
            'name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'country' => 'required|max:100',
            'city' => 'required|max:100',
            'town' => 'required|max:100',
            'gender' => 'required|max:100',
            'specialization' => 'required|max:100',
            'skills' => 'required|string|max:100',
            'whatsapp' => 'required|max:100',
            'email' => 'required|max:100',
            'facebook' => 'required|max:100',
            'image' => 'required|image|max:2048',
            'portfolio' => 'required|image|max:2048',
        ])->validate();

        $disk = config('filesystems.default');
        $baseFolder = config('app.aws_base_folder');
        
        // تحديد المسار بناءً على وجود base folder
        if ($disk === 's3' && !empty($baseFolder)) {
            $baseFolder = trim($baseFolder, '/');
            $dir = $baseFolder . '/applications';
        } else {
            $dir = 'applications';
        }
        
        $validate['image'] = $this->state['image']->store($dir, $disk);
        $validate['portfolio'] = $this->state['portfolio']->store($dir, $disk);
        $validate['course'] =  $this->event_id;

        $validate['course_type'] =  Event::find($this->event_id)->category->category_type;

            \App\Models\Application::create($validate);

        $this->reset('state');
        $this->dispatch('hide_contact_form');
    }


    #[Computed]
    public function tags(): array
    {
        return Tag::where('tag_type', TagTypeEnum::SKILLS->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }
}
