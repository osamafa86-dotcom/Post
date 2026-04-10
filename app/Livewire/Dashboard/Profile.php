<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Foundation\Application as FoundationApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class Profile extends Component
{
    use WithFileUploads;

    public $avatar;
    public $user_avatar;
    public $is_change_password = false;
    public array $state = [];

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|FoundationApplication
    {
        return view('livewire.dashboard.profile');
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->state = [
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'email' => $user->email ?? '',
            'password' => '',
            'confirm_password' => '',
        ];
        $this->user_avatar = $user->avatar ?? null;
    }

    #[Renderless]
    public function openProfileModal(): void
    {
        $this->dispatch('show_profile');
    }

    public function changePassword(): void
    {
        $this->is_change_password = !$this->is_change_password;

        if (!$this->is_change_password) {
            $this->state['password'] = '';
            $this->state['confirm_password'] = '';
        }
    }

    public function clearColumn(): void
    {
        $this->avatar = null;
    }

    protected function rules(): array
    {
        $rules = [
            'state.first_name' => 'required|string|max:255',
            'state.last_name' => 'required|string|max:255',
            'state.email' => 'required|email|unique:users,email,' . Auth::id(),
        ];

        if ($this->avatar) {
            $rules['avatar'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        if ($this->is_change_password) {
            $rules['state.password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::defaults()
            ];
        }

        return $rules;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $user = Auth::user();

            // Update basic info
            $user->first_name = $this->state['first_name'];
            $user->last_name = $this->state['last_name'];
            $user->email = $this->state['email'];

            // Handle avatar upload
            if ($this->avatar) {
                // Store new avatar
                $avatarPath = $this->avatar->store('avatars', 'public');
                $user->avatar = $avatarPath;
                $this->user_avatar = $avatarPath;
            }

            // Handle password change
            if ($this->is_change_password && !empty($this->state['password'])) {
                $user->password = Hash::make($this->state['password']);
                $this->state['password'] = '';
                $this->state['confirm_password'] = '';
                $this->is_change_password = false;
            }

            $user->save();

            $this->avatar = null;

            session()->flash('message', 'Profile updated successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update profile');
        }
    }
}
