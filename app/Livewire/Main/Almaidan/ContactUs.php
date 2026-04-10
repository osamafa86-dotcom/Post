<?php

namespace App\Livewire\Main\Almaidan;

use App\Enums\LinkPosition;
use App\Models\NewsLetterEmails;
use App\Models\Setting;
use App\Models\SocialMedia;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
class ContactUs extends Component
{
    public array $state = [];


    public $email;

    #[Layout('components.layouts.main.almaidan.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {

        return view('livewire.main.almaidan.contact-us');
    }

    /**
     * @throws ValidationException
     */
    public function createContactUs():void
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
    }

    #[Computed]
    public function social_medias(): Collection|array
    {
        return SocialMedia::query()->where('position',LinkPosition::AllPlaces->value)->get();
    }

    public function addNewsLetterEmail(): void
    {
        $this->validate(['email' => 'required|email|unique:news_letter_emails,email']);
        NewsLetterEmails::create(['email' => $this->email]);
        $this->addError('successEmail', 'تم اضافة البريد الالكتروني بنجاح');
        $this->reset(['email']);
    }
}
