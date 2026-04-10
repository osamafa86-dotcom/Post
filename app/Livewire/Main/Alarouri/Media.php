<?php

namespace App\Livewire\Main\Alarouri;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\ReelTypeEnum;
use App\Models\Category;
use App\Models\Material;
use App\Models\Post;
use App\Models\Reel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Media extends Component
{
    #[Url]
    public $typeFilter = 'all';

    public $search = '';
    public $dateBeginFilter;
    public $dateEndFilter;

    // Load more counters for each type
    public $videosPerPage = 6;
    public $reelsPerPage = 8;
    public $photosPerPage = 8;
    public $audiosPerPage = 6;

    public function mount()
    {
        //
    }

    public function loadMoreVideos()
    {
        $this->videosPerPage += 6;
    }

    public function loadMoreReels()
    {
        $this->reelsPerPage += 8;
    }

    public function loadMorePhotos()
    {
        $this->photosPerPage += 8;
    }

    public function loadMoreAudios()
    {
        $this->audiosPerPage += 6;
    }

    #[Computed]
    public function videos()
    {
        $materials = Material::with(['files.file', 'category.relationable'])
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('type', MaterialTypeEnum::VIDEO->value)
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateBeginFilter, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateBeginFilter);
            })
            ->when($this->dateEndFilter, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateEndFilter);
            })
            ->latest('created_at')
            ->take($this->videosPerPage)
            ->get();

        return $materials->map(function ($material) {
            $videoFile = $material->files->first(function($f) {
                return $f->file && in_array($f->file->extension, ['mp4', 'webm', 'ogg', 'mov']);
            });

            $imageFile = $material->files->first(function($f) {
                return $f->file && in_array($f->file->extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            });

            return [
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'year' => $material->created_at->format('Y'),
                'date' => $material->created_at->format('Y-m-d'),
                'timestamp' => $material->created_at->translatedFormat('d F Y'),
                'source' => $material->material_album?->name ?? 'مصدر عند توفره',
                'video_path' => $videoFile && $videoFile->file && $videoFile->file->path ? file_url($videoFile->file->path) : null,
                'image_path' => $imageFile && $imageFile->file && $imageFile->file->path ? file_url($imageFile->file->path) : asset('assets/main/alarouri/images/videos/thumb2.jpg'),
            ];
        });
    }

    #[Computed]
    public function reels()
    {
        $reels = Reel::with(['files.file'])
            ->when($this->search, function ($q) {
                $q->where('reel_title', 'like', '%' . $this->search . '%');
            })
            ->latest('created_at')
            ->take($this->reelsPerPage)
            ->get();

        return $reels->map(function ($reel) {
            $reelType = (int) $reel->reel_type;
            $videoUrl = null;
            $posterUrl = asset('assets/main/alarouri/images/videos/thumb1.jpg');

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
                'title' => $reel->reel_title ?? 'لقطة عمودية قصيرة',
                'description' => '',
                'year' => $reel->created_at->format('Y'),
                'date' => $reel->created_at->format('Y-m-d'),
                'timestamp' => $reel->created_at->translatedFormat('d F Y'),
                'source' => 'مصدر عند توفره',
                'video_url' => $videoUrl,
                'poster_url' => $posterUrl,
                'type' => $reelType,
            ];
        });
    }

    #[Computed]
    public function photos()
    {
        $materials = Material::with(['files.file', 'category.relationable'])
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('type', MaterialTypeEnum::IMAGE->value)
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateBeginFilter, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateBeginFilter);
            })
            ->when($this->dateEndFilter, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateEndFilter);
            })
            ->latest('created_at')
            ->take($this->photosPerPage)
            ->get();

        return $materials->map(function ($material) {
            $imageFile = $material->files->first(function($f) {
                return $f->file && in_array($f->file->extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            });

            return [
                'id' => $material->id,
                'title' => $material->title,
                'description' => $material->description,
                'year' => $material->created_at->format('Y'),
                'date' => $material->created_at->format('Y-m-d'),
                'timestamp' => $material->created_at->translatedFormat('d F Y'),
                'source' => $material->material_album?->name ?? 'مصدر عند توفره',
                'image_path' => $imageFile && $imageFile->file && $imageFile->file->path ? file_url($imageFile->file->path) : asset('assets/main/alarouri/images/videos/thumb1.jpg'),
            ];
        });
    }

    #[Computed]
    public function audios()
    {
        $materials = Material::with(['files.file', 'category.relationable'])
            ->where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('type', MaterialTypeEnum::PODCAST->value)
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateBeginFilter, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateBeginFilter);
            })
            ->when($this->dateEndFilter, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateEndFilter);
            })
            ->latest('created_at')
            ->take($this->audiosPerPage)
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
                'year' => $material->created_at->format('Y'),
                'date' => $material->created_at->format('Y-m-d'),
                'timestamp' => $material->created_at->translatedFormat('d F Y'),
                'source' => $material->material_album?->name ?? 'مصدر عند توفره',
                'audio_path' => $audioFile && $audioFile->file && $audioFile->file->path ? file_url($audioFile->file->path) : null,
                'image_path' => $imageFile && $imageFile->file && $imageFile->file->path ? file_url($imageFile->file->path) : asset('assets/main/alarouri/images/videos/thumb2.jpg'),
            ];
        });
    }

    #[Computed]
    public function totalVideos()
    {
        return Material::where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('type', MaterialTypeEnum::VIDEO->value)
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateBeginFilter, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateBeginFilter);
            })
            ->when($this->dateEndFilter, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateEndFilter);
            })
            ->count();
    }

    #[Computed]
    public function totalReels()
    {
        return Reel::when($this->search, function ($q) {
            $q->where('reel_title', 'like', '%' . $this->search . '%');
        })
            ->count();
    }

    #[Computed]
    public function totalPhotos()
    {
        return Material::where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('type', MaterialTypeEnum::IMAGE->value)
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateBeginFilter, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateBeginFilter);
            })
            ->when($this->dateEndFilter, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateEndFilter);
            })
            ->count();
    }

    #[Computed]
    public function totalAudios()
    {
        return Material::where('publish_status', PublishEnum::PUBLISHED->value)
            ->where('type', MaterialTypeEnum::PODCAST->value)
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->dateBeginFilter, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateBeginFilter);
            })
            ->when($this->dateEndFilter, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateEndFilter);
            })
            ->count();
    }

    #[Layout('components.layouts.main.alarouri.main')]
    public function render()
    {
        return view('livewire.main.alarouri.media');
    }
}
