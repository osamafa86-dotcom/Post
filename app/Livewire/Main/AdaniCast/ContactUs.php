<?php

namespace App\Livewire\Main\AdaniCast;

use App\Models\Setting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ContactUs extends Component
{
    public $Settings;
    public array $state = [];
    #[Layout('components.layouts.main.adani_cast.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $this->Settings=Setting::first();
        return view('livewire.main.adani-cast.contact-us');
    }

    /**
     * @throws ValidationException
     */
    public function createContactUs()
    {
        $validate = Validator::make($this->state,[
            'full_name' => 'required|string|max:100',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:700',
        ])->validate();


        \App\Models\ContactUs::create($validate);

        $this->reset('state');
        $this->dispatch('hide_contact_form');
        return redirect()->route('main.adani_cast.contact_us');
    }


}
