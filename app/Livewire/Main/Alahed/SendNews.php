<?php

namespace App\Livewire\Main\Alahed;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SendNews extends Component
{
    public array $state = [];

    #[Layout('components.layouts.main.alahed.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.alahed.send-news');
    }

    /**
     * @throws ValidationException
     */
    public function createSendNews(): void
    {
        $validate = Validator::make($this->state, [
            'email' => 'required|email',
            'full_name' => 'required|string|max:50',
            'dob' => 'required|date',
            'place_before_missed' => 'required|string|max:150',
            'date_before_missed' => 'required|date',
            'missed_place' => 'nullable|string|max:300',
            'missed_note' => 'required|string|max:300',
            'question' => 'required|boolean|in:0,1',
            'healthy' => 'required|string|max:300',
            'report' => 'nullable|string|max:300',
            'help' => 'required|string|max:300',
            'contact' => 'required|string|max:300',
            'more_info' => 'nullable|string|max:300',
        ])->validate();

        \App\Models\SendNews::create($validate);

        $this->state = [];
        $this->dispatch('hide_contact_form');
    }
}
