<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileModal extends Component
{
    use WithFileUploads;


    public array $state = [];

    public $user;

    public $avatar , $user_avatar;

    public $is_change_password;
    #[Layout('components.layouts.dashboard.app')]
    public function render()
    {

        return view('livewire.dashboard.profile-modal');
    }

    #[On('show_profile')]
    public function getUserData()
    {
       $this->state = Auth::user()->toArray();
       $this->user = Auth::user();
       $this->avatar = '';
        $this->user_avatar  = $this->user?->avatar;
    }

    public function clearColumn(): void
    {

        $this->avatar = null;
        $this->user_avatar = null;
    }
    public function save()
    {
    //$role = $this->avatar  ? "required|max:2048" : "nullable";
        $this->state['avatar'] = $this->avatar;

        $role = $this->state['avatar'] ? "required|max:2048" : "nullable";
        $validation = Validator::make($this->state, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'avatar' => $role,
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
            'password' => 'nullable|string|min:8',
            'confirm_password' => 'nullable|string|min:8|same:password',

        ])->validate();



        if ($this->state['avatar']) {

            if ($this->user->avatar) {
                Storage::disk('public')->delete($this->user->avatar);
            }

            $disk = config('filesystems.default');
            $baseFolder = config('app.aws_base_folder');
            
            // تحديد المسار بناءً على وجود base folder
            if ($disk === 's3' && !empty($baseFolder)) {
                $baseFolder = trim($baseFolder, '/');
                $dir = $baseFolder . '/uploads';
            } else {
                $dir = 'uploads';
            }
            
            $avatarPath = $this->state['avatar']->store($dir, $disk);
            $validation['avatar'] = $avatarPath;
        }

        unset($validation['confirm_password']);

        if (!empty($validation['password'])) {
            $validation['password'] = bcrypt($validation['password']);
        } else {
            unset($validation['password']);
        }

        $this->user->update($validation);


        $this->dispatch('hide_profile');
    }

    public function changePssword()
    {
        if ($this->is_change_password) {
            $this->is_change_password = false;
        } else {
            $this->is_change_password = true;
        }

    }

}
