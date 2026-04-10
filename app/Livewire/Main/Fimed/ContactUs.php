<?php

namespace App\Livewire\Main\Fimed;

use App\Models\ContactUs as ContactUsModel;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ContactUs extends Component
{
    public string $name = '';
    public string $email = '';
    public string $message = '';
    public bool $sent = false;

    protected array $rules = [
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:100',
        'message' => 'required|string|max:2000',
    ];

    #[Layout('components.layouts.main.fimed.main')]
    public function render()
    {
        return view('livewire.main.fimed.contact-us');
    }

    public function submit(): void
    {
        $this->validate();

        ContactUsModel::create([
            'full_name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'message']);
        $this->sent = true;
    }
}
