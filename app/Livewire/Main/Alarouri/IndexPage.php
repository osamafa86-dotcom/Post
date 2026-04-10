<?php

namespace App\Livewire\Main\Alarouri;


use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\ReelTypeEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\Post;
use App\Models\Quote;
use App\Models\Reel;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Url;

class IndexPage extends Component
{
    public string $quoteSearch = '';
    public string $songCategory = '';

    public function mount()
    {
      //
    }

    #[Computed]
    public function quotes()
    {
        return Quote::with(['author.files.file'])
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->when($this->quoteSearch, function ($query) {
                $query->where('quote_text', 'like', '%' . $this->quoteSearch . '%');
            })
            ->latest('created_at')
            ->take(6)
            ->get();
    }

    #[Computed]
    public function songs()
    {
        $materials = Material::with([
                'material_album',
                'files.file',
                'category.relationable'
            ])
            ->withCount('views')
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('type', \App\Enums\MaterialTypeEnum::PODCAST->value)
            ->latest('created_at')
            ->take(50)
            ->get();

        return $materials->map(function ($material) {
            $audioFile = $material->files->first(function($f) {
                return $f->file && in_array($f->file->extension, ['mp3', 'ogg', 'wav']);
            });

            $imageFile = $material->files->first(function($f) {
                return $f->file && in_array($f->file->extension, ['jpg', 'jpeg', 'png', 'webp']);
            });

            return [
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'album' => $material->material_album?->name,
                'category_id' => $material->category?->relationable?->id,
                'category' => $material->category?->relationable?->category_title ?? 'عام',
                'views' => $material->views_count,
                'created_at' => $material->created_at->format('Y'),
                'created_date' => $material->created_at->format('Y-m-d'),
                'audio_path' => $audioFile && $audioFile->file && $audioFile->file->path ? file_url($audioFile->file->path) : null,
                'image_path' => $imageFile && $imageFile->file && $imageFile->file->path ? file_url($imageFile->file->path) : asset('assets/main/alarouri/images/videos/thumb2.jpg'),
                'duration' => 0, // Will be calculated dynamically in frontend
            ];
        });
    }

    #[Computed]
    public function songCategories()
    {
        return Category::whereHas('material_relation', function ($q) {
                $q->whereHas('material', function ($mq) {
                    $mq->where('publish_status', PublishEnum::PUBLISHED->value)
                       ->where('type', \App\Enums\MaterialTypeEnum::PODCAST->value);
                });
            })
            ->pluck('category_title', 'id');
    }

    #[Computed]
    public function latestMedia()
    {
        $materials = Material::with([
                'files.file',
                'category.relationable',
                'material_album'
            ])
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->whereIn('type', [
                \App\Enums\MaterialTypeEnum::VIDEO->value,
                \App\Enums\MaterialTypeEnum::IMAGE->value
            ])
            ->latest('created_at')
            ->take(4)
            ->get();

        return $materials->map(function ($material) {
            $videoFile = $material->files->first(function($f) {
                return $f->file && in_array($f->file->extension, ['mp4', 'webm', 'ogg', 'mov']);
            });

            $imageFile = $material->files->first(function($f) {
                return $f->file && in_array($f->file->extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            });

            $isVideo = $material->type == \App\Enums\MaterialTypeEnum::VIDEO->value;

            return [
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'type' => $isVideo ? 'video' : 'photo',
                'badge' => $isVideo ? 'فيديو' : 'صورة',
                'badge_class' => $isVideo ? 'c-badge--video' : 'c-badge--photo',
                'timestamp' => $material->created_at->format('F Y'),
                'timestamp_ar' => $material->created_at->translatedFormat('F Y'),
                'category' => $material->category?->relationable?->category_title ?? 'أرشيف عام',
                'source' => $material->material_album?->name ?? 'أرشيف عام',
                'video_path' => $videoFile && $videoFile->file && $videoFile->file->path ? file_url($videoFile->file->path) : null,
                'image_path' => $imageFile && $imageFile->file && $imageFile->file->path ? file_url($imageFile->file->path) : asset('assets/main/alarouri/images/articles/Horizontal/placeholder.jpg'),
                'has_video' => $isVideo && $videoFile,
                'has_overlay' => $isVideo,
            ];
        });
    }

    #[Computed]
    public function reels()
    {
        $reels = Reel::with(['files.file'])
            ->latest('created_at')
            ->take(4)
            ->get();

        return $reels->map(function ($reel) {
            $reelType = (int) $reel->reel_type;
            $videoUrl = null;
            $posterUrl = asset('assets/main/alarouri/images/articles/Vertical/2.jpg');

            $posterFile = $reel->files->where('model_column', 'poster')->first();
            if ($posterFile && $posterFile->file && $posterFile->file->path) {
                $posterUrl = file_url($posterFile->file->path);
            }

            if ($reelType === ReelTypeEnum::LOCAL->value) {
                $videoFile = $reel->files->where('model_column', 'file')->first();

                if ($videoFile && $videoFile->file && $videoFile->file->path) {
                    $videoUrl = file_url($videoFile->file->path);
                }
            } elseif ($reelType === ReelTypeEnum::YOUTUBE->value) {
                $videoUrl = $reel->reel_url;
            } elseif ($reelType === ReelTypeEnum::INSTAGRAM->value) {
                $videoUrl = $reel->reel_url;
            }

            return [
                'id' => $reel->id,
                'title' => $reel->reel_title ?? 'ريل',
                'type' => $reelType,
                'type_label' => ReelTypeEnum::fromValue($reelType),
                'video_url' => $videoUrl,
                'poster_url' => $posterUrl,
                'created_at' => $reel->created_at->format('Y-m-d'),
            ];
        });
    }

    #[Computed]
    public function news()
    {
        $allNews = Post::with(['category.relationable', 'tags.relationable', 'files.file', 'type.relationable'])
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->latest('publish_date')
            ->take(4)
            ->get();

        $featuredNews = null;
        $sideNews = $allNews->where('news_type', NewsTypeEnum::SUB_NEWS->value)->take(4);

        if ($allNews->isNotEmpty()) {
            $featuredNews = null;
            $sideNews = $allNews;
        }

        return [
            'featured' => $featuredNews ? [
                'id' => $featuredNews->id,
                'slug' => $featuredNews->slug,
                'title' => $featuredNews->title,
                'excerpt' => $featuredNews->excerpt ?? strip_tags(substr($featuredNews->content, 0, 200)),
                'image' => $featuredNews->files->first()?->file?->path
                    ? file_url($featuredNews->files->first()->file->path)
                    : 'https://dummyimage.com/800x520/dddddd/000000',
                'badge' => $featuredNews->type?->relationable?->type_title ?? 'خبر مميز',
                'date' => $featuredNews->publish_date?->translatedFormat('d F Y') ?? $featuredNews->created_at->translatedFormat('d F Y'),
                'source' => $featuredNews->category?->relationable?->category_title ?? 'وحدة البيانات الرسمية',
            ] : null,
            'side' => $sideNews->map(function ($news) {
                return [
                    'id' => $news->id,
                    'slug' => $news->slug,
                    'title' => $news->title,
                    'badge' => $news->type?->relationable?->type_title ?? 'خبر',
                    'date' => $news->publish_date?->translatedFormat('d F Y') ?? $news->created_at->translatedFormat('d F Y'),
                    'source' => $news->category?->relationable?->category_title ?? 'الأرشيف الرسمي',
                ];
            })->values(),
        ];
    }

    public function songViewIncrees($id)
    {
        $song = Material::find($id);
        if (!$song) {
            return;
        }

        // Check if already viewed in this session
        $sessionKey = 'viewed_material_' . $song->id;
        $sessionData = session()->get($sessionKey);

        // Check if session exists and hasn't expired
        if ($sessionData && isset($sessionData['expires_at'])) {
            if (now()->lt($sessionData['expires_at'])) {
                return; // Already viewed and not expired
            }
        }

        $now = \Carbon\Carbon::now();
        $threshold = $now->copy()->subHours((int) config('app.views_hours', 4));

        // Find recent view record within threshold (regardless of creation date)
        $view = $song->views()
            ->where('last_viewed_at', '>', $threshold)
            ->orderBy('last_viewed_at', 'desc')
            ->first();

        if ($view) {
            // Update existing view
            $view->increment('views_number');
            $view->update(['last_viewed_at' => $now]);
        } else {
            // Create new view record
            $song->views()->create([
                'last_viewed_at' => $now,
                'views_number' => 1,
            ]);
        }

        // Store in session with expiration
        session()->put($sessionKey, [
            'viewed' => true,
            'expires_at' => now()->addHours((int) config('app.view_expiration_hours', 4))
        ]);
    }

    #[Layout('components.layouts.main.alarouri.main')]
    public function render()
    {
        return view('livewire.main.alarouri.index-page');
    }

}
