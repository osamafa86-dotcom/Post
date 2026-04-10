<?php

namespace App\Livewire\Main\Ufok;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class SinglePost extends Component
{
    #[Url(keep: true)]
    public $id;
    #[Url(keep: true)]
    public $slug;

    public Event $event;
    public $randomEvents;
    public $relatedEvents;

    #[Layout('components.layouts.main.ufok.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.ufok.single-post', [
            'event' => $this->event
        ]);
    }

    public function mount(): void
    {
        $this->id = request()->query('id');
        $this->slug = request()->query('slug');

        $this->event = Event::with('presenters')
            ->withTrashed()
            ->where('id', $this->id)
            ->firstOrFail();


        $this->randomEvents = Event::where('id', '!=', $this->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $this->relatedEvents = Event::where('id', '!=', $this->id)
            ->whereHas('categories', function ($query) {
                $query->whereIn('id', $this->event->categories->pluck('id'));
            })
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function presenters()
    {
        return $this->event->presenters;
    }
}
