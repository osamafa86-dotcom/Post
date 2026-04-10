<?php

namespace App\Livewire\Main\Maktoob;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\LinkPosition;
use App\Models\Advertisement;
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
    public $Settings;
    public $bottom_advert;
    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $this->Settings=Setting::first();
        return view('livewire.main.maktoob.contact-us');
    }

    public function mount(): void
    {
        $this->bottom_advert = Advertisement::query()->where('place' , AdvertisementPlaceEnum::MAIN_ADS->value)
            ->latest()->first();
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
}
