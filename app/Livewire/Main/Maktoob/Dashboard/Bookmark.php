<?php

namespace App\Livewire\Main\Maktoob\Dashboard;

use App\Models\Favoirate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Bookmark extends Component
{
    #[Url(keep: true)]
    public $type = 'bookmark';
    public $search;
    public $filterDateBegin;
    public $filterDateEnd;
    public $category;

    #[Computed]
    public function bookmarks()
    {
        return Favoirate::where('user_id', auth('web_user')->id())
            ->where('type', $this->type)
            ->when($this->search || $this->filterDateBegin || $this->filterDateEnd || $this->category, function ($query) {
                $query->whereHas('post', function ($postQuery) {
                    if ($this->search) {
                        $postQuery->where('title', 'like', '%' . $this->search . '%');
                    }

                    if ($this->filterDateBegin) {
                        $postQuery->whereDate('publish_date', '>=', $this->filterDateBegin);
                    }

                    if ($this->filterDateEnd) {
                        $postQuery->whereDate('publish_date', '<=', $this->filterDateEnd);
                    }
                    if ($this->category) {
                        $postQuery->whereHas('category.relationable', function ($categoryQuery) {
                            $categoryQuery->where('id', $this->category);
                        });
                    }
                });

            })
            ->with([
                'post.files.file',
                'post.category.relationable',
                'post.authors.relationable'
            ])
            ->paginate(10);
    }

    public function toggleBookmark(int $postId, string $type = 'bookmark'): void
    {
        $userId = auth('web_user')->id();

        $bookmark = Favoirate::where([
            ['user_id', $userId],
            ['post_id', $postId],
            ['type', $type],
        ])->first();

        if ($bookmark) {
            $bookmark->delete();
        } else {
            Favoirate::create([
                'user_id' => $userId,
                'post_id' => $postId,
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    #[Layout('components.layouts.main.maktoob.user_dashboard.main')]
    public function render()
    {
        return view('livewire.main.maktoob.dashboard.bookmark');
    }

    public function mount($type = 'bookmark')
    {
        $this->type = $type;
    }
}
