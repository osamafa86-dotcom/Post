<?php

namespace App\Livewire\Main\Tamkeen;

use App\Enums\PublishEnum;
use App\Models\Event;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class Courses extends Component
{

    use WithFileUploads;

    public $course;

    public array $state = [];
    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tamkeen.courses');
    }


    public function create():void
    {
        $validate = Validator::make($this->state,[
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'residence' => 'required|string|max:255',
            'membership_number' => 'required',
            'details' => 'required|string|max:700',
            'city' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'arrival_date' => 'required|date',
            'departure_reason' => 'required|max:255',
            'agree1' => 'required',
            'agree2' => 'required',
            'signature' => 'required',
            'image' => 'required|image|max:2048',
        ])->validate();

        if(!Empty($this->state['image']))
        {
            $validate['image'] = $this->state['image']->store('CourseRequest', config('filesystems.default'));
        }


        \App\Models\CourseRequest::create($validate);

        $this->reset('state');
        $this->dispatch('hide_contact_form');
    }
    public function mount($id): void
    {
        $this->course = Event::where('id', $id)
        ->first();
    }
    }
