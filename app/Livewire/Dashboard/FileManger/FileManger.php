<?php

namespace App\Livewire\Dashboard\FileManger;

use App\Models\Files;
use App\Models\ModelHasFile;
use App\Models\PinnedFile;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileManger extends Component
{
    use WithFileUploads;

    public array $files = [];
    public bool $pureLoad = true;
    public bool $editFile = false;
    public bool $multipleSelection = true;
    public bool $hideSideBarOptions = false;
    public $selectedFile = [];
    public $selectedFileType = [];
    public $editingFile;
    public $fileDetails;
    public $imageForPreview;
    public string $search = '';
    public $column = null;
    public string $showType = 'all';
    #[Validate(['file_name' => ['required', 'string', 'max:512000']])]
    public $file_name;
    public bool $refreshing = false;
    public int $perPage = 12;
    public $currentRoute;

    public function mount()
    {
        $this->currentRoute = Route::currentRouteName();
    }

    #[Computed]
    public function images(): Collection|array
    {
        static $extensionMap = [
            'images' => ['jpg', 'jpeg', 'png', 'webp'],
            'videos' => ['mp4', 'mov'],
            'files' => ['pdf'],
            'audio' => ['mp3', 'ogg', 'wav'],
        ];

        $query = Files::query();

        // Filter by type
        $query->when($this->showType !== 'all' && isset($extensionMap[$this->showType]), function ($q) use ($extensionMap) {
            $q->whereIn('extension', $extensionMap[$this->showType]);
        });

        // Search logic - FIXED to include posts files
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';

            $postIDS = Post::where('image_alt', 'like', $searchTerm)
                ->with(['files' => function ($q) {
                    $q->where('model_column', 'image');
                }])
                ->get()
                ->pluck('files')
                ->flatten()
                ->pluck('file_id')
                ->unique()
                ->toArray();

            $query->where(function ($q) use ($postIDS, $searchTerm) {
                // Direct file name search
                $q->where('file_name', 'like', $searchTerm)
                    ->orWhere('original_name', 'like', $searchTerm)
                    ->orWhereIn('files.id', $postIDS);
            });
        }

        return $query
            ->leftJoin('pinned_files', function ($join) {
                $join->on('files.id', '=', 'pinned_files.file_id');
            })
            ->with(['isPinned', 'posts']) // Include posts relationship
            ->select('files.*')
            ->orderByRaw('CASE WHEN pinned_files.id IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('pinned_files.pinned_at')
            ->orderByDesc('files.id')
            ->orderByDesc('files.created_at')
            ->take($this->perPage)
            ->get();
    }
    #[Computed]
    public function postsByImageAltSearch()
    {
        if (empty($this->search)) {
            return collect();
        }

        $searchTerm = '%' . $this->search . '%';

        $posts = \App\Models\Post::where('image_alt', 'like', $searchTerm)
            ->orWhere('title', 'like', $searchTerm)
            ->with(['files']) // Make sure this relationship is defined in Post model
            ->take(6)
            ->get();

        // Debug the first post to see files
        if ($posts->isNotEmpty()) {
            $firstPost = $posts->first();
            \Log::debug('Post files check', [
                'post_id' => $firstPost->id,
                'post_title' => $firstPost->title,
                'files_count' => $firstPost->files->count(),
                'files' => $firstPost->files->pluck('id', 'file_name')->toArray()
            ]);
        }

        return $posts;
    }    public function debugPostFilesRelationship()
    {
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';

            // Get a sample post with matching image_alt
            $samplePost = \App\Models\Post::where('image_alt', 'like', $searchTerm)
                ->first();

            if ($samplePost) {
                dd([
                    'post_id' => $samplePost->id,
                    'post_title' => $samplePost->title,
                    'image_alt' => $samplePost->image_alt,
                    'files_relationship_loaded' => $samplePost->relationLoaded('files'),
                    'files_count' => $samplePost->files->count(),
                    'files' => $samplePost->files,
                    'files_relationship_method' => method_exists($samplePost, 'files') ? 'exists' : 'missing',
                    'post_model_check' => get_class($samplePost),
                ]);
            }
        }
    }    #[Computed]
    public function similarPostsByImageAlt()
    {
        if (empty($this->search)) {
            return collect();
        }

        $searchTerm = '%' . $this->search . '%';

        // Get posts that have similar image_alt text
        $similarPosts = \App\Models\Post::where('image_alt', 'like', $searchTerm)
            ->orWhere('title', 'like', $searchTerm)
            ->with(['files']) // This should use the relationship from Post model
            ->take(5)
            ->get();

        // Debug output
        // dd([
        //     'search_term' => $this->search,
        //     'similar_posts_count' => $similarPosts->count(),
        //     'similar_posts' => $similarPosts->map(function($post) {
        //         return [
        //             'id' => $post->id,
        //             'title' => $post->title,
        //             'image_alt' => $post->image_alt,
        //             'files_count' => $post->files->count(),
        //             'files' => $post->files->pluck('id', 'file_name')
        //         ];
        //     })
        // ]);

        return $similarPosts;
    }
    public function debugModelHasFilesSearch()
    {
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';

            // Debug 1: Check model_has_files table structure
            $modelHasFilesSample = \DB::table('model_has_files')->limit(5)->get();

            // Debug 2: Check files with posts through model_has_files
            $filesWithPosts = Files::where(function($q) use ($searchTerm) {
                $q->where('file_name', 'like', $searchTerm)
                    ->orWhere('original_name', 'like', $searchTerm)
                    ->orWhereHas('posts', function ($postQuery) use ($searchTerm) {
                        $postQuery->where('image_alt', 'like', $searchTerm)
                            ->orWhere('title', 'like', $searchTerm);
                    });
            })->with(['posts' => function($query) {
                $query->select('id', 'title', 'image_alt');
            }])->get();

            // Debug 3: Direct query to model_has_files
            $directModelFiles = \DB::table('model_has_files')
                ->join('posts', 'model_has_files.model_id', '=', 'posts.id')
                ->where('model_has_files.model_type', \App\Models\Post::class)
                ->where(function($q) use ($searchTerm) {
                    $q->where('posts.image_alt', 'like', $searchTerm)
                        ->orWhere('posts.title', 'like', $searchTerm);
                })
                ->select('model_has_files.*', 'posts.title', 'posts.image_alt')
                ->get();

            // Debug 4: Posts with their files
            $postsWithFiles = \App\Models\Post::where('image_alt', 'like', $searchTerm)
                ->orWhere('title', 'like', $searchTerm)
                ->with(['files'])
                ->get();

            dd([
                'search_term' => $this->search,
                'model_has_files_sample' => $modelHasFilesSample,
                'files_with_posts_count' => $filesWithPosts->count(),
                'files_with_posts' => $filesWithPosts->map(function($file) {
                    return [
                        'file_id' => $file->id,
                        'file_name' => $file->file_name,
                        'posts_count' => $file->posts->count(),
                        'posts' => $file->posts->map(function($post) {
                            return [
                                'post_id' => $post->id,
                                'post_title' => $post->title,
                                'image_alt' => $post->image_alt
                            ];
                        })
                    ];
                }),
                'direct_model_files_count' => $directModelFiles->count(),
                'direct_model_files' => $directModelFiles,
                'posts_with_files_count' => $postsWithFiles->count(),
                'posts_with_files' => $postsWithFiles->map(function($post) {
                    return [
                        'post_id' => $post->id,
                        'post_title' => $post->title,
                        'image_alt' => $post->image_alt,
                        'files_count' => $post->files->count(),
                        'files' => $post->files->map(function($file) {
                            return [
                                'file_id' => $file->id,
                                'file_name' => $file->file_name,
                                'file_path' => $file->path
                            ];
                        })
                    ];
                })
            ]);
        }
    }

    public function loadMore(): void
    {
        $this->perPage += 12;
    }

    public function pinFile($id): void
    {
        $pinned = PinnedFile::where('file_id', $id)
            ->first();
        if ($pinned) {
            $pinned->delete();
        } else {
            PinnedFile::create([
                'file_id' => $id,
                'user_id' => auth()->id(),
                'pinned_at' => now(),
            ]);
        }
    }
    public function editingPhoto($id): void
    {
        $this->editFile = true;
        $this->editingFile = Files::find($id);
    }

    public function showSpecificType(string $type = 'all'): void
    {
        $validTypes = ['all', 'images', 'videos', 'files', 'audio'];
        $this->showType = in_array($type, $validTypes) ? $type : 'all';
        unset($this->images);
    }

    public function editPhoto(): void
    {
        $this->validate();
        $this->editingFile->file_name = $this->file_name;
        $this->editingFile->save();
        $this->editFile = false;
        $this->file_name = null;
        $this->editingFile = null;
        $this->refreshData();
    }

    #[Computed]
    public function pageColumns()
    {
        if ($this->currentRoute == 'dashboard.posts.create_update_post') {
            return [
                ['name' => __('messages.posts.featured_image'), 'type' => 'images', 'isMultiple' => false, 'selectName' => 'image'],
                ['name' => __('messages.posts.more_images'), 'type' => 'images', 'isMultiple' => true, 'selectName' => 'morePictures'],
                ['name' => __('messages.posts.details'), 'type' => 'images', 'isMultiple' => true, 'selectName' => 'create_update_post_ck_editor']
            ];
        }
        return [];
    }

    public function selectImage($id, $extension = null): void
    {
        if (!$this->pureLoad) {
            if ($this->multipleSelection) {
                if (!in_array($id, $this->selectedFile)) {
                    $this->selectedFile[] = $id;
                } else {
                    $this->selectedFile = array_diff($this->selectedFile, [$id]);
                }
            } else {
                $this->dispatch('imageSelected', id: $id, column: $this->column);
                $this->reset('multipleSelection', 'selectedFile', 'column');
                $this->dispatch('hide_library_form');
            }
        } else {
            if ($this->multipleSelection) {
                if (!in_array($id, $this->selectedFile)) {
                    $this->selectedFile[] = $id;
                    $this->selectedFileType[] = $extension;
                } else {
                    $this->selectedFile = array_diff($this->selectedFile, [$id]);
                    $this->selectedFileType = array_diff($this->selectedFileType, [$extension]);
                }
            }
            $this->fileDetails = null;
        }
    }

    public function manualSelectImage($column): void
    {
        if (count($this->selectedFile) == 1) {
            $this->dispatch('imageSelected', id: $this->selectedFile[0], column: $column);
            $this->reset('multipleSelection', 'selectedFile', 'column');
            $this->dispatch('hide_library_form');
        } else {
            $this->dispatch('imageSelected', id: $this->selectedFile, column: $column);
            $this->reset('multipleSelection', 'selectedFile', 'column');
            $this->dispatch('hide_library_form');
        }
    }

    public function submitMultipleSelection()
    {
        $this->dispatch('imageSelected', id: $this->selectedFile, column: $this->column);
        $this->reset('multipleSelection', 'selectedFile', 'column');
        $this->dispatch('hide_library_form');

    }


    #[On('refreshData')]
    public function refreshData(): void
    {
        $this->refreshing = true;
        unset($this->images);

        // عمل تأخير وهمي بسيط لو بدك يظهر الـ loading بشكل مرئي أكثر:
        usleep(500 * 1000); // 0.5 ثانية مثلاً

        $this->refreshing = false;
    }

    #[On('columnDefined')]
    public function columnDefined($column): void
    {
        $this->reset(['selectedFile', 'column']);
        $this->pureLoad = false;
        $this->column = $column;
    }

    #[On('typeDefined')]
    public function typeDefined($type)
    {
        $this->pureLoad = false;
        $this->hideSideBarOptions = true;
        $this->showType = $type;
    }

    #[On('isMultiple')]
    public function isMultiple($active)
    {
        $this->multipleSelection = $active;
    }

    #[On('showAllTypes')]
    public function showAllTypes()
    {
        $this->pureLoad = false;
        $this->hideSideBarOptions = false;
        $this->showType = 'all';
    }

    #[On('openEditor')]
    public function openEditor($id): void
    {
        $file = Files::find($id);

        // Check if using S3 storage
        $disk = config('filesystems.default');
        $isS3 = $disk === 's3';
        
        // For S3, use proxy URL to avoid CORS issues with cropper
        if ($isS3) {
            $file->full_url = route('file-manger.proxy-image', ['fileId' => $file->id]);
        } else {
            $file->full_url = file_url($file->path);
        }
        
        $this->dispatch('editorOpened', file: $file);
    }

    #[On('pureLoad')]
    public function pureLoad()
    {
        $this->reset(['perPage', 'refreshing', 'file_name', 'showType', 'editingFile', 'fileDetails', 'column', 'search', 'selectedFile', 'multipleSelection', 'hideSideBarOptions', 'editFile','pureLoad']);
    }

    public function deletePhoto($id, $image): void
    {
        // حذف من قاعدة البيانات
        Files::where('id', $id)->delete();
        unset($this->images);

        // تحديد disk الحالي
        $storageDisk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');
        $storagePath = 'uploads/' . $image;

        // حذف الملف من التخزين الصحيح
        if (Storage::disk($storageDisk)->exists($storagePath)) {
            Storage::disk($storageDisk)->delete($storagePath);
            Log::info("File deleted from {$storageDisk}: {$storagePath}");
        } else {
            Log::warning("File not found for deletion on {$storageDisk}: {$storagePath}");
        }
    }

    public function bulkDeletePhoto(): void
    {
        foreach ($this->selectedFile as $id) {
            // حذف من قاعدة البيانات
            $file = Files::where('id', $id)->first();
            $file->delete();
            unset($this->images);

            // تحديد disk الحالي
            $storageDisk = config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');
            $storagePath = 'uploads/' . $file?->original_name;

            // حذف الملف من التخزين الصحيح
            if (Storage::disk($storageDisk)->exists($storagePath)) {
                Storage::disk($storageDisk)->delete($storagePath);
                Log::info("File deleted from {$storageDisk}: {$storagePath}");
            } else {
                Log::warning("File not found for deletion on {$storageDisk}: {$storagePath}");
            }
        }
        $this->reset(['selectedFile']);
    }

    #[Renderless]
    public function bulkExport(): void
    {
        $this->dispatch('exportBulk', selectedFile: $this->selectedFile);
        $this->reset(['selectedFile']);
    }

    #[Renderless]
    public function previewImage($file_name): void
    {
        $this->imageForPreview = $file_name;
        $this->dispatch('previewImage', file: $file_name);
    }


    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.file-manger.file-manger');
    }
}
