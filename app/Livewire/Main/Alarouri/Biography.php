<?php

namespace App\Livewire\Main\Alarouri;

use App\Models\Setting;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Biography extends Component
{
    public $name,$email,$subject,$message;

    #[Computed]
    public function settings()
    {
        return Setting::first();
    }
    public function resetInputs()
    {
        $this->reset(['name','email','subject','message']);
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
    #[Layout('components.layouts.main.alarouri.main')]
    public function render()
    {
        return view('livewire.main.alarouri.biography');
    }
}
