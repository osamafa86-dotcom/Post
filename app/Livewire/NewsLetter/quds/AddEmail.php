<?php

namespace App\Livewire\NewsLetter\quds;

use App\Models\NewsletterEmails;
use Livewire\Component;

class AddEmail extends Component
{

    public $email;
    public function addNewsLetterEmail()
    {
        $this->validate(['email' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->email]);
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
        $this->reset(['email']);
    }


    public function render()
    {
        return view('livewire.news-letter.quds.add-email');
    }
}
