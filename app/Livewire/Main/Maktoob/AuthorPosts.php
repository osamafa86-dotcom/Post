<?php

namespace App\Livewire\Main\Maktoob;

use App\Enums\AdvertisementPlaceEnum;
use App\Enums\PublishEnum;
use App\Models\Advertisement;
use App\Models\Participant;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AuthorPosts extends Component
{
    public int $author_id;
    public object $author;
    public $bottom_advert;

    #[Layout('components.layouts.main.maktoob.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.maktoob.author-posts');
    }

    public function mount(): void
    {
        $this->author = Participant::where('id', $this->author_id)->with(['files.file', 'participant_social_media'])->first();
        $this->bottom_advert = Advertisement::query()->where('place', AdvertisementPlaceEnum::MAIN_ADS->value)
            ->latest()->first();
    }

    #[Computed]
    public function posts()
    {
        return Post::latest('publish_date')
            ->when(!empty($this->author_id), function ($query) {
                $query->WhereHas('author', function ($q) {
                    $q->whereHasMorph(
                        'relationable',
                        [Participant::class],
                        function ($subQuery) {
                            $subQuery->where('id', $this->author_id);
                        }
                    );
                });
            })
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->with(['categories.relationable', 'authors.relationable', 'files.file'])
            ->paginate(8);
    }
}
