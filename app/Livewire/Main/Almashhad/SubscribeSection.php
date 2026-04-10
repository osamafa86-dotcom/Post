<?php

namespace App\Livewire\Main\Almashhad;

use App\Models\NewsLetterEmails;
use Livewire\Component;

class SubscribeSection extends Component
{
    public $email;

    public function subscribe()
    {
        $this->validate(['email' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->email]);
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
        $this->reset('email');

    }
    public function render()
    {
        return view('livewire.main.almashhad.subscribe-section');
    }
}
