<?php

namespace App\Livewire\Main\Hongora;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Models\Category;
use App\Models\Participant;
use App\Models\SpecialPage;
use App\Models\UserDetails;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Studio extends Component
{

    public array $state = [];
    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.studio');
    }


    public function create():void
    {
        $validate = Validator::make($this->state,[
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:100',
            'company' => 'required|string|max:100',
            'usage_purpose' => 'required|string|max:700',
            'number_people' => 'required|numeric|max:30',
            'date' => 'required',
            'usage_duration' => 'required',
            'time' => 'required',
            'required_equipment' => 'required|string|max:700',
        ])->validate();

        \App\Models\BookingRequests::create($validate);

        $this->reset('state');
        $this->dispatch('hide_contact_form');
    }

    #[Computed]
    public function team()
    {
        return Participant::where('type', ParticipantTypeEnum::TEAM->value)->with(['files.file'])->get();
    }


}
