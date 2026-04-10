<?php

namespace App\Livewire\Main\Almashhad;

use App\Models\Setting;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ContactUs extends Component
{
    public $name,$email,$subject,$message;

    #[Computed]
    public function settings()
    {
        return Setting::first();
    }
    public function send()
    {
        $this->validate([
            'name'=>['required','string','max:1024'],
            'email'=>['required','email','max:1024'],
            'subject'=>['required','string','max:1024'],
            'message'=>['required','string'],
        ]);
        \App\Models\ContactUs::create([
            'full_name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);
        $this->reset();
        session()->flash('success','تم أرسال طلبك بنجاح');
    }
    #[Layout('components.layouts.main.almashhad.main')]
    public function render()
    {
        return view('livewire.main.almashhad.contact-us');
    }
}
