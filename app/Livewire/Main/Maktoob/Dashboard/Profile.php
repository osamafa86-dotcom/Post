<?php

namespace App\Livewire\Main\Maktoob\Dashboard;

use Illuminate\Support\Facades\Lang;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;
    public $user,$first_name,$last_name,$email,$description,$phone,$password,$currentPassword,$password_confirmation,$photo;
    public $changingPassword=false;
    public function mount()
    {
        $this->user = auth('web_user')->user();
        $this->first_name=$this->user?->first_name;
        $this->last_name=$this->user?->last_name;
        $this->email=$this->user?->email;
        $this->phone=$this->user?->details?->phone;
        $this->description=$this->user?->details?->description;
        $this->photo=$this->user?->profile_photo_path;
    }
    public function changePassword()
    {
        $this->validate([
            'currentPassword' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if (!\Hash::check($this->currentPassword, $this->user->password)) {
            session()->flash('error', 'Current password is incorrect.');
            return;
        }
        $this->user->update([
            'password' => bcrypt($this->password),
        ]);
        $this->reset(['currentPassword', 'password', 'password_confirmation', 'changingPassword']);
        session()->flash('success', 'Password changed successfully.');
    }

    public function saveChanges()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone'=>'nullable|string|max:255',
            'description'=>'nullable|string|max:255',
            'photo'=>'nullable|image|max:10240',
        ]);
        $path=null;
        if ($this->photo) {
            $path = $this->photo->store('profile_photo', 'public'); // saves to storage/app/public/profile_photo
            $this->user->profile_photo_path = $path; // save path in DB
        }
        $this->user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'profile_photo_path' => $this->user->profile_photo_path ?: $path,
        ]);
        $this->user->details->update([
            'phone'=>$this->phone,
            'description'=>$this->description,
        ]);

        session()->flash('success', 'Data saved successfully');
    }
    #[Layout('components.layouts.main.maktoob.user_dashboard.main')]
    public function render()
    {
        return view('livewire.main.maktoob.dashboard.profile');
    }
}
