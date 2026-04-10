<?php

namespace App\Livewire\Main\Hongora\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\HongoraUserSound;
use App\Models\Participant;
use App\Models\SpecialPage;
use App\Models\Tag;
use App\Models\UserDetailRelation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Profile extends Component
{
    public $user;
    public $works;
    public $state = [];
    public $state_password = [];
    public $tags;
    #[Url(keep: true)]
    public $tab = 'info';

    #[Layout('components.layouts.main.hongora.user_dashboard.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.hongora.user_dashboard.profile');
    }

    public function mount()
    {
        $this->user = Auth()->user();
        $this->state = [
            'first_name' => Auth()->user()->first_name,
            'last_name' => Auth()->user()->last_name,
            'email' => Auth()->user()->email,
            'phone' => Auth()->user()->details->phone,
            'country' => Auth()->user()->details->country,
            'city' => Auth()->user()->details->city,
            'work' => Auth()->user()->details->work,
            'description' => Auth()->user()->details->description,
        ];
        $languages = $this->user->details->languages ? implode(',', $this->user->details->languages->pluck('tag_name')->toArray()) : null;
        $this->state['languages'] = $languages;
        $dialects = $this->user->details->dialects ? implode(',', $this->user->details->dialects->pluck('tag_name')->toArray()) : null;
        $this->state['dialects'] = $dialects;
        $this->tags = $this->user->details?->tags?->pluck('tag_name')->toArray() ?? [];
        $this->works = HongoraUserSound::where('user_id', Auth::id())->get();
    }

    public function saveInfo()
    {
        $this->user->update([
            'first_name' => $this->state['first_name'],
            'last_name' => $this->state['last_name'],
            'email' => $this->state['email'],
        ]);
        $this->user->details->update([
            'phone' => $this->state['phone'],
            'country' => $this->state['country'],
            'city' => $this->state['city'],
            'work' => $this->state['work'],
            'description' => $this->state['description'],
        ]);
        if (!empty($this->state['languages'])) {
            $this->user->details->languages()->forceDelete();
            $tags = explode(',', $this->state['languages']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::LANGUAGES->value]);
                UserDetailRelation::create([
                    'user_detail_id' => $this->user->details->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
        if (!empty($this->state['dialects'])) {
            $this->user->details->dialects()->forceDelete();
            $tags = explode(',', $this->state['dialects']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::DIALECTS->value]);
                UserDetailRelation::create([
                    'user_detail_id' => $this->user->details->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
        $this->dispatch('hide_contact_form');
    }

    public function changePassword()
    {
        $this->validate([
            'state_password.current_password' => 'required|min:8',
            'state_password.new_password' => 'required|min:8',
            'state_password.password_confirmation' => 'required|same:state_password.new_password',
        ]);
        if (!Hash::check($this->state_password['current_password'], $this->user->password)) {
            session()->flash('error', 'كلمة المرور غير صحيحة');
        } else {
            $this->user->update([
                'password' => Hash::make($this->state_password['new_password']),
            ]);
            $this->dispatch('hide_contact_form');
        }
    }

    #[Computed]
    public function languages(): array
    {
        return Tag::where('tag_type', TagTypeEnum::LANGUAGES->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }

    #[Computed]
    public function dialects(): array
    {
        return Tag::where('tag_type', TagTypeEnum::DIALECTS->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }

}
