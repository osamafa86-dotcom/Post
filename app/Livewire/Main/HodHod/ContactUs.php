<?php

namespace App\Livewire\Main\HodHod;

use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ContactUs extends Component
{
    public array $state = [];
    public function submitMessage()
    {
        $validate = Validator::make($this->state,[
            'full_name' => 'required|string|max:100',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:700',
        ])->validate();


        \App\Models\ContactUs::create($validate);

        $this->reset('state');
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
    }
    #[Layout('components.layouts.main.hodhod.main')]
    public function render()
    {
        return view('livewire.main.hod-hod.contact-us');
    }
}
