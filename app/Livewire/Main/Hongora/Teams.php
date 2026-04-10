<?php

namespace App\Livewire\Main\Hongora;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\UserDetailsTypeEnum;
use App\Models\Category;
use App\Models\Participant;
use App\Models\SpecialPage;
use App\Models\UserDetails;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Teams extends Component
{

    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.teams');
    }


    #[Computed]
    public function team()
    {
        return UserDetails::where('user_type', UserDetailsTypeEnum::HONGORA_TEAM->value)->with(['user' , 'userDetails_social_media'])->get();
    }


    #[Computed]
    public function content_creator()
    {
        return UserDetails::where('user_type', UserDetailsTypeEnum::CONTENT_CREATOR->value)->with(['user' , 'userDetails_social_media'])->get();
    }

}
