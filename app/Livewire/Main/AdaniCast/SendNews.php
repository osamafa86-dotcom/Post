<?php

namespace App\Livewire\Main\AdaniCast;

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

    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.adani-cast.send-news');
    }

    /**
     * @throws ValidationException
     */
    public function createSendNews(): void
    {
        $validate = Validator::make($this->state, [
            'full_name' => 'required|string|max:100',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:700',
        ])->validate();

        \App\Models\SendNews::create($validate);

        $this->reset('state');
        $this->dispatch('hide_contact_form');
    }
}
