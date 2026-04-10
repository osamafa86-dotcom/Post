<?php

namespace App\Livewire\Main\Fimed;

use App\Models\SendNews as SendNewsModel;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SendNews extends Component
{
    public string $name = '';
    public string $email = '';
    public string $title = '';
    public string $content = '';
    public string $source = '';
    public bool $sent = false;

    protected array $rules = [
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:100',
        'title' => 'required|string|max:255',
        'content' => 'required|string|max:5000',
        'source' => 'nullable|string|max:255',
    ];

    #[Layout('components.layouts.main.fimed.main')]
    public function render()
    {
        return view('livewire.main.fimed.send-news');
    }

    public function submit(): void
    {
        $this->validate();

        SendNewsModel::create([
            'full_name' => $this->name,
            'email' => $this->email,
            'subject' => $this->title,
            'message' => $this->content,
        ]);

        $this->reset(['name', 'email', 'title', 'content', 'source']);
        $this->sent = true;
    }
}
