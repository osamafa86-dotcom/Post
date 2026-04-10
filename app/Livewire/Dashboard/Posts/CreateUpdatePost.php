<?php

namespace App\Livewire\Dashboard\Posts;

use App\Enums\CategoryTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\Files;
use App\Models\Participant;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\PostSpecialContent;
use App\Models\SortData;
use App\Models\SpecialFile;
use App\Models\Tag;
use App\Models\Type;
use App\Traits\Notifiable;
use App\Traits\Publishable;
use App\Traits\WhereIn;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateUpdatePost extends Component
{
    use WithFileUploads, WhereIn, Notifiable;
    use Publishable;

    public array $state = [];
    public ?object $Post = null;
    public bool $showEdit = false;
    public $publishers_list = [];
    public $resources_list = [];
    public $categories_list = [];
    public $types_list = [];
    public $authors_list = [];
    public bool $advancedImage = false;
    public $advancedImagesFile;
    public $generatedTags;
    public $is_embed;
    public $token;
    public ?string $translatingFromTitle = null;
    public ?string $translatingToLang = null;

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.posts.create-update-post');
    }

    public function addPublisher()
    {
        $this->publishers_list[] = [
            'name' => '',
            'url' => '',
        ];
    }

    public function removePublisher($index)
    {
        unset($this->publishers_list[$index]);
        $this->publishers_list = array_values($this->publishers_list);
    }

    public function addResource()
    {
        $this->resources_list[] = [
            'name' => '',
            'url' => '',
        ];
    }

    public function removeResource($index)
    {
        unset($this->resources_list[$index]);
        $this->resources_list = array_values($this->resources_list);
    }

    public function addCategory()
    {
        $this->categories_list[] = [
            'title' => '',
        ];
    }

    public function removeCategory($index)
    {
        unset($this->categories_list[$index]);
        $this->categories_list = array_values($this->categories_list);
    }

    public function addType()
    {
        $this->types_list[] = [
            'name' => '',
        ];
    }

    public function removeType($index)
    {
        unset($this->types_list[$index]);
        $this->types_list = array_values($this->types_list);
    }

    public function addAuthor()
    {
        $this->authors_list[] = [
            'name' => '',
        ];
    }

    public function removeAuthor($index)
    {
        unset($this->authors_list[$index]);
        $this->authors_list = array_values($this->authors_list);
    }

    public function generateContent(string $type = 'tag')
    {
        if (empty($this->state['title']) || empty($this->state['description'])) {
            $this->addError($type === 'tag' ? 'tags' : 'body', 'Please fill in title and description first.');
            return;
        }

        $url = 'https://mahmoudha.app.n8n.cloud/webhook/generate-tags';

        $payload = [
            'title' => $this->state['title'],
            'description' => $this->state['description'],
            'type' => $type,
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        if ($response->successful()) {
            $data = $response->json()[0] ?? [];

            if ($type === 'tag') {
                $tags = $data['tags'] ?? null;
                if ($tags) {
                    $this->state['tags'] = $tags;
                } else {
                    $this->addError(__('validation.tags'), __('tag_generation_failed_no_tags_returned'));
                }
            } elseif ($type === 'body') {
                $body = $data['body'] ?? null;
                if ($body) {
                    $this->state['body'] = $body;
                } else {
                    $this->addError(__('validation.body'), __('post_body_generation_failed_no_body_returned'));
                }
            }
        } else {
            $this->addError($type, 'validation.content_generation_failed' . $response->body());
        }
    }


    public function clearColumn(string $column, $index = null): void
    {
        if (!isset($this->state[$column])) {
            return;
        }
        if (is_null($index)) {
            $this->state[$column] = null;
            $this->state[$column . '_name'] = null;
            return;
        }
        if (is_array($this->state[$column]) && isset($this->state[$column][$index])) {
            unset($this->state[$column][$index]);
            $this->state[$column] = array_values($this->state[$column]);
        }
        if (is_array($this->state[$column . '_name']) && isset($this->state[$column . '_name'][$index])) {
            unset($this->state[$column . '_name'][$index]);
            $this->state[$column . '_name'] = array_values($this->state[$column . '_name']);
        }
    }

    private function handleMediaFile($file_id)
    {
        $file = Files::find($file_id);
        if (!$file) {
            \Log::warning("File not found: {$file_id}");
            return;
        }
        $path = $file->path;
        $extension = strtolower($file->extension);
        $extensionMap = [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'videos' => ['mp4', 'mov', 'mkv', 'webm'],
            'files' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            'audio' => ['mp3', 'ogg', 'wav'],
        ];
        $type = 'files';
        foreach ($extensionMap as $key => $extensions) {
            if (in_array($extension, $extensions)) {
                $type = $key;
                break;
            }
        }
        $url = file_url($path);

        // Debug logging
        \Log::info("Inserting media", [
            'file_id' => $file->id,
            'type' => $type,
            'extension' => $extension,
            'url' => $url,
            'path' => $path
        ]);

        $this->dispatch('insert-media-into-editor', [
            'type' => $type,
            'url' => $url,
            'extension' => $extension,
            'id' => $file->id,
        ]);
    }

    #[On('imageSelected')]
    public function imageSelected($id, $column = null): void
    {
        if ($column === 'create_update_post_ck_editor') {
            if (is_array($id)) {
                foreach ($id as $file_id) {
                    $this->handleMediaFile($file_id);
                }
            } else {
                $this->handleMediaFile($id);
            }
            return;
        }
        if (isset($column)) {
            if (is_array($id)) {
                $this->state[$column] = $id;
                foreach ($id as $file_id) {
                    $this->state[$column . '_name'][$file_id] = Files::where('id', $file_id)->value('path');
                }
            } else {
                $this->state[$column] = $id;
                $this->state[$column . '_name'] = Files::where('id', $id)->value('path');
            }
        }
    }


    public function mount($post_id = null): void
    {
        if ($post_id) {
            $this->edit($post_id);
        } else {
            $this->state['publish_date'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        // Handle translation from existing post
        if (request()->has('translate_from') && !$post_id) {
            $sourcePost = Post::withoutGlobalScopes()->find(request('translate_from'));
            if ($sourcePost) {
                $this->state['translate_from'] = $sourcePost->id;
                $this->state['translate_lang'] = request('lang', config('app.locale'));
                $this->state['translation_group'] = $sourcePost->translation_group ?? Str::uuid()->toString();

                // Pre-fill fields from source post
                $this->state['publish_date'] = $sourcePost->publish_date;
                $this->state['news_type'] = $sourcePost->news_type;
                $this->state['image_size'] = $sourcePost->image_size;
                $this->state['order_number'] = $sourcePost->order_number;
                $this->state['video_url'] = $sourcePost->video_url;
                $this->state['pdf_url'] = $sourcePost->pdf_url;
                $this->state['image'] = $sourcePost->image;

                // Copy categories, tags, authors from source
                $this->state['selectedCategories'] = $sourcePost->categories()->pluck('relationable_id')->toArray();
                $this->state['selectedTags'] = $sourcePost->tags()->pluck('relationable_id')->toArray();
                $this->state['selectedAuthors'] = $sourcePost->authors()->pluck('relationable_id')->toArray();
                $this->state['selectedTypes'] = $sourcePost->types()->pluck('relationable_id')->toArray();

                $this->translatingFromTitle = $sourcePost->title;
                $this->translatingToLang = config('app.languages.' . $this->state['translate_lang'] . '.name');
            }
        }
    }


    #[Computed]
    public function tags(): array
    {
        return Tag::where('tag_type', TagTypeEnum::NEWS->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }

    #[Computed]
    public function types(): array
    {
        return Type::select('id', 'type_name')->get()->toArray();
    }

    #[Computed]
    public function categories(): array
    {
        return Category::select('id', 'category_title')
            ->where('category_type', CategoryTypeEnum::NEWS->value)
            ->orderBy('category_title')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function authors(): array
    {
        return Participant::where('type', ParticipantTypeEnum::AUTHORS->value)->select('id', 'name')->get()->toArray();
    }

    #[Computed]
    public function publishers(): array
    {
        return Participant::where('type', ParticipantTypeEnum::PUBLISHERS->value)->select('id', 'name')->get()->toArray();
    }

    #[Computed]
    public function resources(): array
    {
        return Participant::where('type', ParticipantTypeEnum::RESOURCES->value)->select('id', 'name')->get()->toArray();
    }

    #[Computed]
    public function special_files(): array
    {
        return SpecialFile::select('id', 'file_name')->get()->toArray();
    }

    public function updatedState($value, $key): void
    {
        if ($key == "title") {
            $slug = strtolower($value);
            // إزالة الرموز الخاصة مع السماح بالحروف العربية
            $slug = preg_replace('/[^\p{Arabic}a-z0-9\s-]/u', '', $slug);
            // استبدال الفراغات والـ hyphens المتكررة بـ hyphen واحدة
            $slug = preg_replace('/[\s-]+/', '-', $slug);
            // إزالة أي hyphen في البداية أو النهاية
            $slug = trim($slug, '-');

            $this->state['slug'] = $slug;
//
//            //
//
//            $response = Http::post('https://n8n.taktek.co/webhook/generate-tags', [
//                'title' => $this->state['title'],
//                'description' => $this->state['description'],
//            ]);
//
//            $this->generatedTags = $response->json('tags'); // ["laravel","livewire","laravel-livewire","شرح","متقدم","تعلم","بناء","واجهات"]
        }
//        if ($key == "description") {
//            $response = Http::post('https://n8n.taktek.co/webhook/generate-tags', [
//                'title' => $this->state['title'],
//                'description' => $this->state['description'],
//            ]);
//
//            $this->generatedTags = $response->json('tags'); // ["laravel","livewire","laravel-livewire","شرح","متقدم","تعلم","بناء","واجهات"]
//        }
    }

//    public function generateTags(): void
//    {
//        if (isset($this->state['title']) && isset($this->state['description'])) {
//            $response = Http::withHeaders([
//                'Content-Type' => 'application/json',
//                'x-goog-api-key' => config('app.google_api_key')
//            ])->post(
//                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
//                [
//                    'contents' => [[
//                        'parts' => [[
//                            'text' => 'Generate descriptive tags for: ' . $this->state['title'] . ' ' . $this->state['description'] . '. Return JSON array only.'
//                        ]]
//                    ]]
//                ]
//            );
//            $text = $response->json('candidates.0.content.parts.0.text') ?? '[]';
//            $text = preg_replace('/^```(?:json)?|```$/m', '', trim($text));
//            $tags = json_decode($text, true);
//            if (!is_array($tags)) {
//                $tags = [];
//            }
//            $this->generatedTags = $tags;
//        }
//    }

    public function addTag($tag): void
    {
        $this->dispatch('addTagToInput', $tag);
        unset($this->generatedTags[array_search($tag, $this->generatedTags)]);
    }

    /**
     * Temporary save with minimal validation - only title required
     * Used for quds launch to quickly save posts
     */
    public function temporarySavePost()
    {
        try {
            // Minimal validation - only title required
            $validation = Validator::make($this->state, [
                'title' => 'required|string|max:255',
            ])->validate();

            // Set defaults for missing fields
            $validation['slug'] = $this->state['slug'] ?? \Illuminate\Support\Str::slug($validation['title']);
            $validation['publish_date'] = $this->state['publish_date'] ?? now();
            $validation['publish_status'] = PublishEnum::DRAFT->value;
            $validation['description'] = $this->state['description'] ?? '';
            $validation['body'] = $this->state['body'] ?? '';

            // Generate order number
            $orderNumber = Post::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
            $validation['order_number'] = $orderNumber ? $orderNumber + 1 : 1;

            DB::beginTransaction();

            try {
                $post = Post::create([
                    'publish_date' => $validation['publish_date'],
                    'title' => $validation['title'],
                    'slug' => $validation['slug'],
                    'description' => $validation['description'],
                    'body' => $validation['body'],
                    'order_number' => $validation['order_number'],
                    'user_id' => Auth::id(),
                    'publish_status' => $validation['publish_status'],
                ]);

                $lastOrderNumber = SortData::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
                $post->sortable()->create([
                    'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
                ]);

                DB::commit();

                $this->dispatch('post-create', [
                    'status' => 'success',
                    'title' => __('messages.alert_message.success'),
                    'message' => 'Post temporarily saved as draft successfully'
                ]);

                // Reset form
                $this->state = [];
                $this->state['publish_date'] = Carbon::now()->format('Y-m-d H:i:s');

                return $post;

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = $message;
                }
            }

            $this->dispatch('post-create', [
                'status' => 'error',
                'title' => __('messages.alert_message.validation_error'),
                'message' => implode('<br>', $errorMessages)
            ]);

        } catch (\Exception $e) {
            Log::error('Temporary post save failed: ' . $e->getMessage());

            $this->dispatch('post-create', [
                'status' => 'error',
                'title' => __('messages.alert_message.error'),
                'message' => 'Failed to save post: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function createPost($state, $redirect = true, $stayOnPage = false)
    {
        try {
            // Validate main state
            $validation = Validator::make($this->state, [
                'publish_date' => 'required|date',
                'available_date' => 'nullable|date|after:' . now(),
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255',
                'tags' => config('features.input_validation.post.tags') ?? 'required|string|max:255',
                'image' => 'required|integer|exists:files,id',
                'portrait_image' => 'nullable|sometimes|integer|exists:files,id',
                'slider_image' => 'nullable|sometimes|integer|exists:files,id',
                'social_media_image' => 'nullable|sometimes|integer|exists:files,id',
                'video_url' => 'nullable|url',
                'pdf_url' => 'nullable|url',
                'video_url_id' => 'nullable|exists:files,id',
                'pdf_url_id' => 'nullable|exists:files,id',
                'image_alt' => 'nullable|string|max:100',
                'description' => config('features.input_validation.post.description') ?? 'required|string|max:255',
                'body' => 'nullable',
                'author_id' => 'nullable|array',
                'author_id.*' => 'nullable|exists:participants,id',
                'publisher_id' => 'nullable|array',
                'publisher_id.*' => 'nullable|exists:participants,id',
                'resource_id' => 'nullable|array',
                'resource_id.*' => 'nullable|exists:participants,id',
                'type_id' => 'nullable|array',
                'type_id.*' => 'nullable|exists:types,id',
                'special_file_id' => 'nullable|exists:special_files,id',
                'category_id' => (empty($this->categories_list) && empty($this->state['category_id'])) ? 'required|array|min:1' : 'nullable|array',
                'category_id.*' => 'nullable|exists:categories,id',
                'news_type' => config('features.input_validation.post.news_type'),
                'image_size' => config('features.input_validation.post.image_size'),
                'morePictures' => 'nullable|array|min:1',
                'morePictures.*' => 'required|integer|exists:files,id',
            ])->validate();
            Validator::make($this->all(), [
                'publishers_list' => 'nullable|array',
                'publishers_list.*.name' => 'required|string|max:255',
                'publishers_list.*.url' => 'nullable|url',
                'resources_list' => 'nullable|array',
                'resources_list.*.name' => 'required|string|max:255',
                'resources_list.*.url' => 'nullable|url',
                'categories_list' => 'nullable|array',
                'categories_list.*.title' => 'required|string|max:255',
                'types_list' => 'nullable|array',
                'types_list.*.name' => 'required|string|max:255',
                'authors_list' => 'nullable|array',
                'authors_list.*.name' => 'required|string|max:255',
            ])->validate();

            // Ensure array fields are never null
            $validation['publisher_id'] = $validation['publisher_id'] ?? [];
            $validation['resource_id'] = $validation['resource_id'] ?? [];
            $validation['author_id'] = $validation['author_id'] ?? [];
            $validation['type_id'] = $validation['type_id'] ?? [];
            $validation['category_id'] = $validation['category_id'] ?? [];
            $validation['morePictures'] = $validation['morePictures'] ?? [];

            $publishStatus = $state == PublishEnum::PUBLISHED->value ? PublishEnum::PUBLISHED->value : PublishEnum::DRAFT->value;
            $validation['publish_status'] = $publishStatus;

            // Generate a new order number
            $orderNumber = Post::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
            $validation['order_number'] = $orderNumber ? $orderNumber + 1 : 1;

            // Ensure 'body' field is correctly populated
            $validation['body'] = $this->state['body'] ?? null;

            if (!empty($this->state['body'])) {
                $validation['body'] = $this->convertTagsToLinks($validation['body'], $validation['tags']);
            }

            // Use database transaction to ensure data consistency
            DB::beginTransaction();

            try {
                // Create the post
                $post = Post::create([
                    'publish_date' => $validation['publish_date'] ?? null,
                    'title' => $validation['title'] ?? null,
                    'slug' => $validation['slug'] ?? null,
                    'video_url' => $validation['video_url'] ?? null,
                    'pdf_url' => $validation['pdf_url'] ?? null,
                    'image' => $validation['image'] ?? null,
                    'image_alt' => $validation['image_alt'] ?? null,
                    'description' => $validation['description'] ?? null,
                    'body' => $validation['body'] ?? null,
                    'news_type' => $validation['news_type'] ?? null,
                    'image_size' => $validation['image_size'] ?? null,
                    'order_number' => $validation['order_number'] ?? null,
                    'user_id' => Auth::id(),
                    'publish_status' => $validation['publish_status'] ?? null,
                ]);

                // Link translation group
                if (!empty($this->state['translation_group'])) {
                    $post->update([
                        'translation_group' => $this->state['translation_group'],
                        'lang' => $this->state['translate_lang'] ?? config('app.locale'),
                    ]);

                    // Update source post's translation_group if it doesn't have one
                    if (!empty($this->state['translate_from'])) {
                        Post::withoutGlobalScopes()
                            ->where('id', $this->state['translate_from'])
                            ->whereNull('translation_group')
                            ->update(['translation_group' => $this->state['translation_group']]);
                    }
                }

                $processedPublisherIds = [];
                Log::info('Creating Post - Processing Publishers', [
                    'dynamic_list' => $this->publishers_list,
                    'selected_ids' => $validation['publisher_id'] ?? [],
                ]);

                // Process dynamic publishers list FIRST
                if (!empty($this->publishers_list) && is_array($this->publishers_list)) {
                    foreach ($this->publishers_list as $key => $publisherData) {
                        // Check if publisher already exists to avoid duplicates
                        $existingPublisher = Participant::where('name', $publisherData['name'])
                            ->where('type', \App\Enums\ParticipantTypeEnum::PUBLISHERS->value)
                            ->first();

                        if ($existingPublisher) {
                            $publisher = $existingPublisher;
                        } else {
                            $publisher = Participant::create([
                                'name' => $publisherData['name'],
                                'type' => \App\Enums\ParticipantTypeEnum::PUBLISHERS->value,
                                'work' => null,
                                'image' => null,
                                'description' => null,
                                'participants_data' => [
                                    'url' => $publisherData['url'] ?? null,
                                ],
                            ]);
                        }

                        $processedPublisherIds[] = $publisher->id;

                        PostRelation::create([
                            'post_id' => $post->id,
                            'relationable_id' => $publisher->id,
                            'relationable_type' => Participant::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }
                }

                // Process existing publisher selections from dropdown
                if (!empty($validation['publisher_id']) && is_array($validation['publisher_id'])) {
                    foreach ($validation['publisher_id'] as $key => $publisherId) {
                        // Only process if not already processed from dynamic list
                        if (!in_array($publisherId, $processedPublisherIds)) {
                            PostRelation::create([
                                'post_id' => $post->id,
                                'relationable_id' => $publisherId,
                                'relationable_type' => Participant::class,
                                'relationable_is_main' => $key == 0 && empty($this->publishers_list) ? 1 : 0,
                            ]);
                        }
                    }
                }

                // Handle resources
                $processedResourceIds = [];
                Log::info('Creating Post - Processing Resources', [
                    'dynamic_list' => $this->resources_list,
                    'selected_ids' => $validation['resource_id'] ?? [],
                ]);

                // Process dynamic resources list FIRST
                if (!empty($this->resources_list) && is_array($this->resources_list)) {
                    foreach ($this->resources_list as $key => $resourceData) {
                        // Check if resource already exists to avoid duplicates
                        $existingResource = Participant::where('name', $resourceData['name'])
                            ->where('type', \App\Enums\ParticipantTypeEnum::RESOURCES->value)
                            ->first();

                        if ($existingResource) {
                            $resource = $existingResource;
                        } else {
                            $resource = Participant::create([
                                'name' => $resourceData['name'],
                                'type' => \App\Enums\ParticipantTypeEnum::RESOURCES->value,
                                'work' => null,
                                'image' => null,
                                'description' => null,
                                'participants_data' => [
                                    'url' => $resourceData['url'] ?? null,
                                ],
                            ]);
                        }

                        $processedResourceIds[] = $resource->id;

                        PostRelation::create([
                            'post_id' => $post->id,
                            'relationable_id' => $resource->id,
                            'relationable_type' => Participant::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }
                }

                // Process existing resource selections from dropdown
                if (!empty($validation['resource_id']) && is_array($validation['resource_id'])) {
                    foreach ($validation['resource_id'] as $key => $resourceId) {
                        // Only process if not already processed from dynamic list
                        if (!in_array($resourceId, $processedResourceIds)) {
                            PostRelation::create([
                                'post_id' => $post->id,
                                'relationable_id' => $resourceId,
                                'relationable_type' => Participant::class,
                                'relationable_is_main' => $key == 0 && empty($this->resources_list) ? 1 : 0,
                            ]);
                        }
                    }
                }

                // Handle dynamic categories creation and relations
                $processedCategoryIds = [];
                if (!empty($this->categories_list) && is_array($this->categories_list)) {
                    foreach ($this->categories_list as $key => $categoryData) {
                        if (empty($categoryData['title'])) continue;

                        // Check if category already exists to avoid duplicates
                        $existingCategory = Category::where('category_title', $categoryData['title'])
                            ->where('category_type', CategoryTypeEnum::NEWS->value)
                            ->first();

                        if ($existingCategory) {
                            $category = $existingCategory;
                        } else {
                            $category = Category::create([
                                'category_title' => $categoryData['title'],
                                'category_type' => CategoryTypeEnum::NEWS->value,
                            ]);
                        }

                        $processedCategoryIds[] = $category->id;

                        // Create post relation
                        PostRelation::create([
                            'post_id' => $post->id,
                            'relationable_id' => $category->id,
                            'relationable_type' => Category::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }
                }

                // Process existing category selections from dropdown
                if (!empty($validation['category_id']) && is_array($validation['category_id'])) {
                    foreach ($validation['category_id'] as $key => $categoryId) {
                        if (!in_array($categoryId, $processedCategoryIds)) {
                            PostRelation::create([
                                'post_id' => $post->id,
                                'relationable_id' => $categoryId,
                                'relationable_type' => Category::class,
                                'relationable_is_main' => $key == 0 && empty($this->categories_list) ? 1 : 0,
                            ]);
                        }
                    }
                }

                // Handle dynamic types creation and relations
                $processedTypeIds = [];
                if (!empty($this->types_list) && is_array($this->types_list)) {
                    foreach ($this->types_list as $key => $typeData) {
                        if (empty($typeData['name'])) continue;

                        // Check if type already exists to avoid duplicates
                        $existingType = \App\Models\Type::where('type_name', $typeData['name'])
                            ->first();

                        if ($existingType) {
                            $type = $existingType;
                        } else {
                            $type = \App\Models\Type::create([
                                'type_name' => $typeData['name'],
                            ]);
                        }

                        $processedTypeIds[] = $type->id;

                        // Create post relation
                        PostRelation::create([
                            'post_id' => $post->id,
                            'relationable_id' => $type->id,
                            'relationable_type' => \App\Models\Type::class,
                            'relationable_is_main' => 0,
                        ]);
                    }
                }

                // Process existing type selections from dropdown
                if (!empty($validation['type_id']) && is_array($validation['type_id'])) {
                    foreach ($validation['type_id'] as $key => $typeId) {
                        if (!in_array($typeId, $processedTypeIds)) {
                            PostRelation::create([
                                'post_id' => $post->id,
                                'relationable_id' => $typeId,
                                'relationable_type' => Type::class,
                                'relationable_is_main' => $key == 0 && empty($this->types_list) ? 1 : 0,
                            ]);
                        }
                    }
                }

                // Handle dynamic authors creation and relations
                $processedAuthorIds = [];
                if (!empty($this->authors_list) && is_array($this->authors_list)) {
                    foreach ($this->authors_list as $key => $authorData) {
                        if (empty($authorData['name'])) continue;

                        // Check if author already exists to avoid duplicates
                        $existingAuthor = Participant::where('name', $authorData['name'])
                            ->where('type', ParticipantTypeEnum::AUTHORS->value)
                            ->first();

                        if ($existingAuthor) {
                            $author = $existingAuthor;
                        } else {
                            $author = Participant::create([
                                'name' => $authorData['name'],
                                'type' => ParticipantTypeEnum::AUTHORS->value,
                                'work' => null,
                                'image' => null,
                                'description' => null,
                            ]);
                        }

                        $processedAuthorIds[] = $author->id;

                        // Create post relation
                        PostRelation::create([
                            'post_id' => $post->id,
                            'relationable_id' => $author->id,
                            'relationable_type' => Participant::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }
                }

                // Process existing author selections from dropdown
                if (!empty($validation['author_id']) && is_array($validation['author_id'])) {
                    foreach ($validation['author_id'] as $key => $authorId) {
                        if (!in_array($authorId, $processedAuthorIds)) {
                            PostRelation::create([
                                'post_id' => $post->id,
                                'relationable_id' => $authorId,
                                'relationable_type' => Participant::class,
                                'relationable_is_main' => $key == 0 && empty($this->authors_list) ? 1 : 0,
                            ]);
                        }
                    }
                }

                // Rest of your existing relations handling...
                if (!empty($validation['available_date']) && config('features.general.has_post_available_date')) {
                    PostSpecialContent::updateOrCreate(['post_id' => $post->id], [
                        'available_date' => $validation['available_date'],
                    ]);
                }

                if (!empty($validation['tags'])) {
                    $tags = explode(',', $validation['tags']);
                    foreach ($tags as $key => $row) {
                        $tagName = trim($row);
                        if (!empty($tagName)) {
                            $tag = Tag::firstOrCreate([
                                'tag_name' => $tagName,
                                'tag_type' => TagTypeEnum::NEWS->value
                            ]);
                            PostRelation::create([
                                'post_id' => $post->id,
                                'relationable_id' => $tag->id,
                                'relationable_type' => Tag::class,
                                'relationable_is_main' => $key == 0 ? 1 : 0,
                            ]);
                        }
                    }
                }

                // Relations already handled above for dynamic and dropdown selections


                if (!empty($validation['special_file_id'])) {
                    PostRelation::create([
                        'post_id' => $post->id,
                        'relationable_id' => $validation['special_file_id'],
                        'relationable_type' => SpecialFile::class,
                        'relationable_is_main' => 1,
                    ]);
                }

                // File handling (keep your existing file logic)
                $post->files()->create([
                    'file_id' => $validation['image'] ?? null,
                    'model_type' => Post::class,
                    'model_column' => 'image',
                ]);

                if (isset($validation['morePictures']) && is_array($validation['morePictures'])) {
                    foreach ($validation['morePictures'] as $picture) {
                        $post->files()->create([
                            'file_id' => $picture,
                            'model_type' => Post::class,
                            'model_column' => 'morePictures',
                        ]);
                    }
                }

                if (isset($validation['portrait_image'])) {
                    $post->files()->create([
                        'file_id' => $validation['portrait_image'],
                        'model_type' => Post::class,
                        'model_column' => 'portrait_image',
                    ]);
                }

                if (isset($validation['slider_image'])) {
                    $post->files()->create([
                        'file_id' => $validation['slider_image'],
                        'model_type' => Post::class,
                        'model_column' => 'slider_image',
                    ]);
                }

                if (isset($validation['social_media_image'])) {
                    $post->files()->create([
                        'file_id' => $validation['social_media_image'],
                        'model_type' => Post::class,
                        'model_column' => 'social_media_image',
                    ]);
                }

                if (isset($validation['video_url_id'])) {
                    $post->files()->create([
                        'file_id' => $validation['video_url_id'],
                        'model_type' => Post::class,
                        'model_column' => 'video_url_id',
                    ]);
                }

                if (isset($validation['pdf_url_id'])) {
                    $post->files()->create([
                        'file_id' => $validation['pdf_url_id'],
                        'model_type' => Post::class,
                        'model_column' => 'pdf_url_id',
                    ]);
                }

                $lastOrderNumber = SortData::select('order_number')->orderBy('order_number', 'desc')->first()?->order_number;
                $post->sortable()->create([
                    'order_number' => $lastOrderNumber ? $lastOrderNumber + 1 : 1,
                ]);

                // Commit the transaction
                DB::commit();

                // Reset dynamic lists
                $this->publishers_list = [];
                $this->resources_list = [];
                $this->categories_list = [];
                $this->types_list = [];
                $this->authors_list = [];
                $this->state = [];

                $this->dispatch('postCreated', $state);

                if ($redirect) {
                    $this->dispatch('post-create', [
                        'status' => 'success',
                        'title' => __('messages.alert_message.success'),
                        'message' => __('messages.posts.created_successfully')
                    ]);
                }
                if ($stayOnPage) {
                    return redirect()->route('dashboard.posts.create_update_post');
                }
                return $post;

            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            // Simply re-throw - Livewire will handle displaying inline errors
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Post creation failed: ' . $e->getMessage());

            // For unexpected errors, you can still show a simple alert or log them
            // But no SweetAlert - errors will be shown inline via validation
            session()->flash('error', __('messages.posts.created_failed'));
        }

    }

    /**
     * Extract readable SQL error from database exception
     */
    private function extractSqlError($errorDetail)
    {
        // Try to extract the most relevant part of the SQL error
        if (preg_match('/SQLSTATE\[[^\]]+\]:\s*(.+)/', $errorDetail, $matches)) {
            return $matches[1];
        }

        return $errorDetail;
    }

    /**
     * Handle dynamic publishers creation and relations
     */
    private function handleDynamicPublishers(Post $post)
    {
        if (!empty($this->resources_list) && is_array($this->resources_list)) {
            foreach ($this->resources_list as $key => $resourceData) {

                // Validate resource data
                if (empty($resourceData['name'])) {
                    throw new \Exception("Resource name is required for resource at index {$key}");
                }

                // Check if resource already exists to avoid duplicates
                $existingResource = Participant::where('name', $resourceData['name'])
                    ->where('type', \App\Enums\ParticipantTypeEnum::RESOURCES->value)
                    ->first();

                if ($existingResource) {
                    $resource = $existingResource;
                } else {
                    $resource = Participant::create([
                        'name' => $resourceData['name'],
                        'type' => \App\Enums\ParticipantTypeEnum::RESOURCES->value,
                        'work' => null,
                        'image' => null,
                        'description' => null,
                        'participants_data' => [
                            'url' => $resourceData['url'] ?? null,
                        ],
                    ]);
                }

                PostRelation::create([
                    'post_id' => $post->id,
                    'relationable_id' => $resource->id,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
    }

    /**
     * Handle dynamic resources creation and relations
     */
    private function handleDynamicResources(Post $post)
    {
        if (!empty($this->publishers_list) && is_array($this->publishers_list)) {
            foreach ($this->publishers_list as $key => $publisherData) {

                // Validate publisher data
                if (empty($publisherData['name'])) {
                    throw new \Exception("Publisher name is required for publisher at index {$key}");
                }

                // Check if publisher already exists to avoid duplicates
                $existingPublisher = Participant::where('name', $publisherData['name'])
                    ->where('type', \App\Enums\ParticipantTypeEnum::PUBLISHERS->value)
                    ->first();

                if ($existingPublisher) {
                    $publisher = $existingPublisher;
                } else {
                    $publisher = Participant::create([
                        'name' => $publisherData['name'],
                        'type' => \App\Enums\ParticipantTypeEnum::PUBLISHERS->value,
                        'work' => null,
                        'image' => null,
                        'description' => null,
                        'participants_data' => [
                            'url' => $publisherData['url'] ?? null,
                        ],
                    ]);
                }

                PostRelation::create([
                    'post_id' => $post->id,
                    'relationable_id' => $publisher->id,
                    'relationable_type' => Participant::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }

        }
    }

    public function edit($post_id): void
    {
        $this->Post = Post::withoutGlobalScopes()->where('id', $post_id)->with(['files.file', 'authors.relationable', 'publishers.relationable', 'resources.relationable', 'categories.relationable', 'tags.relationable'])->first();
        $morePictures = [
            'morePictures' => null,
            'morePictures_name' => null,
        ];
        $morePictures['query'] = $this->Post?->files()?->where('model_column', 'morePictures')->get();
        if (!empty($morePictures['query'])) {
            foreach ($morePictures['query'] as $morePicture) {
                $morePictures['morePictures'][] = $morePicture->file_id;
                $morePictures['morePictures_name'][$morePicture->file_id] = Files::where('id', $morePicture->file_id)->value('path');
            }
        }
        $video_url = [
            'video_url_id' => null,
            'video_url_id_name' => null,
        ];
        $video_url['query'] = $this->Post?->files()?->where('model_column', 'video_url_id')->first();
        if (!empty($video_url['query'])) {
            $video_url['video_url_id'] = $video_url['query']->file_id;
            $video_url['video_url_id_name'] = Files::where('id', $video_url['query']->file_id)->value('path');
        }
        $pdf_url = [
            'pdf_url_id' => null,
        ];
        $pdf_url['query'] = $this->Post?->files()?->where('model_column', 'pdf_url_id')->first();
        if (!empty($pdf_url['query'])) {
            $pdf_url['pdf_url_id'] = $pdf_url['query']->file_id;
        }
        $tags = $this->Post?->tags ? implode(',', $this->Post?->tags->pluck('relationable')->pluck('tag_name')->toArray()) : null;
        $publisherIds = $this->Post?->publishers?->pluck('relationable_id')->toArray() ?? [];
        foreach ($this->Post?->publishers ?? [] as $pub) {
            if ($pub->relationable) {
                $this->publishers_list[] = [
                    'name' => $pub->relationable->name,
                    'url' => $pub->relationable->participants_data['url'] ?? null,
                ];
            }
        }

        $resourceIds = $this->Post?->resources?->pluck('relationable_id')->toArray() ?? [];
        foreach ($this->Post?->resources ?? [] as $res) {
            if ($res->relationable) {
                $this->resources_list[] = [
                    'name' => $res->relationable->name,
                    'url' => $res->relationable->participants_data['url'] ?? null,
                ];
            }
        }
        $this->state = [
            "id" => $this->Post->id ?? null,
            "publish_date" => $this->Post?->publish_date
                ? Carbon::parse($this->Post?->publish_date)->format('Y-m-d\TH:i')
                : Carbon::now()->format('Y-m-d\TH:i'), "order_number" => $this->Post->order_number ?? null,
            "available_date" => $this->Post?->post_special_content?->available_date
                ? Carbon::parse($this->Post?->post_special_content?->available_date)->format('Y-m-d\TH:i')
                : null,
            'tags' => $tags ?? null,
            'title' => $this->Post->title ?? null,
            "slug" => $this->Post->slug ?? null,
            "image" => $this->Post?->files()?->where('model_column', 'image')?->first()?->file_id ?? null,
            "image_name" => $this->Post?->files()?->where('model_column', 'image')?->first()?->file?->path ?? null,
//            "landscape_image" => $this->Post?->files()?->where('model_column', 'landscape_image')?->first()?->file_id ?? null,
//            "landscape_image_name" => $this->Post?->files()?->where('model_column', 'landscape_image')?->first()?->file?->path ?? null,
            "portrait_image" => $this->Post?->files()?->where('model_column', 'portrait_image')?->first()?->file_id ?? null,
            "portrait_image_name" => $this->Post?->files()?->where('model_column', 'portrait_image')?->first()?->file?->path ?? null,
            "slider_image" => $this->Post?->files()?->where('model_column', 'slider_image')?->first()?->file_id ?? null,
            "slider_image_name" => $this->Post?->files()?->where('model_column', 'slider_image')?->first()?->file?->path ?? null,
            "social_media_image" => $this->Post?->files()?->where('model_column', 'social_media_image')?->first()?->file_id ?? null,
            "social_media_image_name" => $this->Post?->files()?->where('model_column', 'social_media_image')?->first()?->file?->path ?? null,
            "video_url" => $this->Post->video_url ?? null,
            "video_url_id" => $video_url['video_url_id'] ?? null,
            "video_url_id_name" => $video_url['video_url_id_name'] ?? null,
            "pdf_url" => $this->Post->pdf_url ?? null,
            "pdf_url_id" => $pdf_url['pdf_url_id'] ?? null,
            "image_alt" => $this->Post->image_alt ?? null,
            "description" => $this->Post->description ?? null,
            "body" => $this->Post->body ?? null,
            "user_id" => $this->Post->user_id ?? null,
            "author_id" => $this->Post?->authors?->pluck('relationable_id')->toArray() ?? null,
            "publisher_id" => $publisherIds,
            "publishers_list" => $this->publishers_list,
            "resource_id" => $resourceIds,
            "resources_list" => $this->resources_list,
            "type_id" => $this->Post?->types?->pluck('relationable_id')->toArray() ?? null,
            "category_id" => $this->Post?->categories?->pluck('relationable_id')->toArray() ?? null,
            "special_file_id" => $this->Post?->special_file?->relationable?->id ?? null,
            "views" => $this->Post?->views ?? null,
            "updates" => $this->Post?->updates ?? null,
            'news_type' => $this->Post?->news_type ?? null,
            'image_size' => $this->Post?->image_size ?? null,
            'cover_type' => $this->Post?->cover_type ?? null,
            "publish_status" => $this->Post?->publish_status ?? null,
            "morePictures" => $morePictures['morePictures'] ?? null,
            "morePictures_name" => $morePictures['morePictures_name'] ?? null,
            'translation_group' => $this->Post?->translation_group ?? null,
        ];
        if (isset($this->state['portrait_image']) || isset($this->state['slider_image']) || isset($this->state['social_media_image'])) {
            $this->advancedImage = true;
        }
        $this->showEdit = true;
    }

    /**
     * @throws ValidationException
     */
    public function updatePost($state, $stayOnPage = false)
    {
        try {
            // Validate main state
            $validation = Validator::make($this->state, [
                'publish_date' => 'required|date',
                'available_date' => 'nullable|date|after:' . now(),
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255',
                'tags' => config('features.input_validation.post.tags') ?? 'required|string|max:255',
                'image' => 'required|integer|exists:files,id',
                'portrait_image' => 'nullable|sometimes|integer|exists:files,id',
                'slider_image' => 'nullable|sometimes|integer|exists:files,id',
                'social_media_image' => 'nullable|sometimes|integer|exists:files,id',
                'video_url' => 'nullable|url',
                'pdf_url' => 'nullable|url',
                'video_url_id' => 'nullable|exists:files,id',
                'pdf_url_id' => 'nullable|exists:files,id',
                'image_alt' => 'nullable|string|max:100',
                'description' => config('features.input_validation.post.description') ?? 'required|string|max:255',
                'body' => 'nullable',
                'author_id' => 'nullable|array',
                'author_id.*' => 'nullable|exists:participants,id',
                'publisher_id' => 'nullable|array',
                'publisher_id.*' => 'nullable|exists:participants,id',
                'resource_id' => 'nullable|array',
                'resource_id.*' => 'nullable|exists:participants,id',
                'type_id' => 'nullable|array',
                'type_id.*' => 'nullable|exists:types,id',
                'special_file_id' => 'nullable|exists:special_files,id',
                'category_id' => (empty($this->categories_list) && empty($this->state['category_id'])) ? 'required|array|min:1' : 'nullable|array',
                'category_id.*' => 'nullable|exists:categories,id',
                'news_type' => config('features.input_validation.post.news_type'),
                'image_size' => config('features.input_validation.post.image_size'),
                'morePictures' => 'nullable|array|min:1',
                'morePictures.*' => 'required|integer|exists:files,id',
            ])->validate();

            // Add validation for dynamic publishers and resources
            Validator::make($this->all(), [
                'publishers_list' => 'nullable|array',
                'publishers_list.*.name' => 'required|string|max:255',
                'publishers_list.*.url' => 'nullable|url',
                'resources_list' => 'nullable|array',
                'resources_list.*.name' => 'required|string|max:255',
                'resources_list.*.url' => 'nullable|url',
                'categories_list' => 'nullable|array',
                'categories_list.*.title' => 'required|string|max:255',
                'types_list' => 'nullable|array',
                'types_list.*.name' => 'required|string|max:255',
                'authors_list' => 'nullable|array',
                'authors_list.*.name' => 'required|string|max:255',
            ])->validate();

            // Use database transaction to ensure data consistency
            DB::beginTransaction();

            try {
                // Ensure array fields are never null
                $validation['publisher_id'] = $validation['publisher_id'] ?? [];
                $validation['resource_id'] = $validation['resource_id'] ?? [];
                $validation['author_id'] = $validation['author_id'] ?? [];
                $validation['type_id'] = $validation['type_id'] ?? [];
                $validation['category_id'] = $validation['category_id'] ?? [];
                $validation['morePictures'] = $validation['morePictures'] ?? [];

                // Assign body and publish status
                $validation['body'] = $this->state['body'] ?? null;
                $validation['publish_status'] = $state;

                if (!empty($this->state['body'])) {
                    $validation['body'] = $this->convertTagsToLinks($validation['body'], $validation['tags']);
                }

                // ------- update main post -------
                $this->Post->update([
                    'publish_date' => $validation['publish_date'] ?? null,
                    'title' => $validation['title'] ?? null,
                    'slug' => $validation['slug'] ?? null,
                    'video_url' => $validation['video_url'] ?? null,
                    'pdf_url' => $validation['pdf_url'] ?? null,
                    'image' => $validation['image'] ?? null, // Add image to post update
                    'image_alt' => $validation['image_alt'] ?? null,
                    'description' => $validation['description'] ?? null,
                    'body' => $validation['body'] ?? null,
                    'news_type' => $validation['news_type'] ?? null,
                    'image_size' => $validation['image_size'] ?? null,
                    'order_number' => $validation['order_number'] ?? null,
                    'user_id' => Auth::id(),
                    'publish_status' => $validation['publish_status'] ?? null,
                ]);

                $this->Post->update(['updates' => $this->Post->updates + 1]);

                // Handle publishers (both dynamic lists and existing selections)
                $processedPublisherIds = [];
                Log::info('Updating Post - Processing Publishers', [
                    'post_id' => $this->Post->id,
                    'dynamic_list' => $this->publishers_list,
                    'selected_ids' => $validation['publisher_id'] ?? [],
                ]);
                // Process dynamic publishers list FIRST
                $this->Post->publishers()->forceDelete();
                if (!empty($this->publishers_list) && is_array($this->publishers_list)) {
                    foreach ($this->publishers_list as $key => $publisherData) {
                        // Check if publisher already exists to avoid duplicates
                        $existingPublisher = Participant::where('name', $publisherData['name'])
                            ->where('type', \App\Enums\ParticipantTypeEnum::PUBLISHERS->value)
                            ->first();

                        if ($existingPublisher) {
                            $publisher = $existingPublisher;
                        } else {
                            $publisher = Participant::create([
                                'name' => $publisherData['name'],
                                'type' => \App\Enums\ParticipantTypeEnum::PUBLISHERS->value,
                                'work' => null,
                                'image' => null,
                                'description' => null,
                                'participants_data' => [
                                    'url' => $publisherData['url'] ?? null,
                                ],
                            ]);
                        }

                        // Track processed IDs
                        $processedPublisherIds[] = $publisher->id;

                        // Update or create post relation
                        PostRelation::create([
                            'post_id' => $this->Post->id,
                            'relationable_id' => $publisher->id,
                            'relationable_type' => Participant::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }
                }

                // Process existing publisher selections from dropdown - ONLY if not already processed
//                if (!empty($validation['publisher_id'])) {
//                    foreach ($validation['publisher_id'] as $key => $publisherId) {
//                        // Only process if not already processed from dynamic list
//                        if (!in_array($publisherId, $processedPublisherIds)) {
//                            $processedPublisherIds[] = $publisherId;
//
//                            PostRelation::updateOrCreate([
//                                'post_id' => $this->Post->id,
//                                'relationable_id' => $publisherId,
//                                'relationable_type' => Participant::class,
//                            ], [
//                                'relationable_is_main' => $key == 0 && empty($this->publishers_list) ? 1 : 0,
//                            ]);
//                        }
//                    }
//                }

                // Remove publishers that are no longer associated
//                $this->Post->publishers()
//                    ->whereNotIn('relationable_id', $processedPublisherIds)
//                    ->forceDelete();

                // Handle resources (both dynamic lists and existing selections)
                $processedResourceIds = [];

                Log::info('Updating Post - Processing Resources', [
                    'post_id' => $this->Post->id,
                    'dynamic_list' => $this->resources_list,
                    'selected_ids' => $validation['resource_id'] ?? [],
                ]);
                $this->Post->resources()->delete();
                // Process dynamic resources list FIRST
                if (!empty($this->resources_list) && is_array($this->resources_list)) {
                    foreach ($this->resources_list as $key => $resourceData) {
                        // Check if resource already exists to avoid duplicates
                        $existingResource = Participant::where('name', $resourceData['name'])
                            ->where('type', \App\Enums\ParticipantTypeEnum::RESOURCES->value)
                            ->first();

                        if ($existingResource) {
                            $resource = $existingResource;
                        } else {
                            $resource = Participant::create([
                                'name' => $resourceData['name'],
                                'type' => \App\Enums\ParticipantTypeEnum::RESOURCES->value,
                                'work' => null,
                                'image' => null,
                                'description' => null,
                                'participants_data' => [
                                    'url' => $resourceData['url'] ?? null,
                                ],
                            ]);
                        }

                        // Track processed IDs
                        $processedResourceIds[] = $resource->id;

                        // Update or create post relation
                        PostRelation::create([
                            'post_id' => $this->Post->id,
                            'relationable_id' => $resource->id,
                            'relationable_type' => Participant::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }
                }

                // Process existing resource selections from dropdown - ONLY if not already processed
//                if (!empty($validation['resource_id'])) {
//                    foreach ($validation['resource_id'] as $key => $resourceId) {
//                        // Only process if not already processed from dynamic list
//                        if (!in_array($resourceId, $processedResourceIds)) {
//                            $processedResourceIds[] = $resourceId;
//
//                            PostRelation::updateOrCreate([
//                                'post_id' => $this->Post->id,
//                                'relationable_id' => $resourceId,
//                                'relationable_type' => Participant::class,
//                            ], [
//                                'relationable_is_main' => $key == 0 && empty($this->resources_list) ? 1 : 0,
//                            ]);
//                        }
//                    }
//                }

                // Remove resources that are no longer associated
//                $this->Post->resources()
//                    ->whereNotIn('relationable_id', $processedResourceIds)
//                    ->forceDelete();

                // Rest of your existing relations handling...
                if (!empty($validation['available_date']) && config('features.general.has_post_available_date')) {
                    PostSpecialContent::updateOrCreate(['post_id' => $this->Post->id], [
                        'available_date' => $validation['available_date'],
                    ]);
                }

                $this->Post->tags()->forceDelete();
                if (!empty($validation['tags'])) {
                    $tags = explode(',', $validation['tags']);
                    foreach ($tags as $key => $row) {
                        $tagName = trim($row);
                        if (!empty($tagName)) {
                            $tag = Tag::firstOrCreate([
                                'tag_name' => $tagName,
                                'tag_type' => TagTypeEnum::NEWS->value
                            ]);
                            PostRelation::create([
                                'post_id' => $this->Post->id,
                                'relationable_id' => $tag->id,
                                'relationable_type' => Tag::class,
                                'relationable_is_main' => $key == 0 ? 1 : 0,
                            ]);
                        }
                    }
                }

                // Relations already handled in similar patterns as createPost (needs implementation in updatePost too)
                $this->Post->categories()->forceDelete();
                $processedCategoryIds = [];
                if (!empty($this->categories_list) && is_array($this->categories_list)) {
                    foreach ($this->categories_list as $key => $categoryData) {
                        if (empty($categoryData['title'])) continue;
                        $existingCategory = Category::where('category_title', $categoryData['title'])
                            ->where('category_type', CategoryTypeEnum::NEWS->value)
                            ->first();

                        if ($existingCategory) {
                            $category = $existingCategory;
                        } else {
                            $category = Category::create([
                                'category_title' => $categoryData['title'],
                                'category_type' => CategoryTypeEnum::NEWS->value,
                            ]);
                        }
                        $processedCategoryIds[] = $category->id;
                        PostRelation::create([
                            'post_id' => $this->Post->id,
                            'relationable_id' => $category->id,
                            'relationable_type' => Category::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }
                }
                if (!empty($validation['category_id']) && is_array($validation['category_id'])) {
                    foreach ($validation['category_id'] as $key => $row) {
                        if (!in_array($row, $processedCategoryIds)) {
                            PostRelation::create([
                                'post_id' => $this->Post->id,
                                'relationable_id' => $row,
                                'relationable_type' => Category::class,
                                'relationable_is_main' => $key == 0 && empty($this->categories_list) ? 1 : 0,
                            ]);
                        }
                    }
                }

                $this->Post->authors()->forceDelete();
                $processedAuthorIds = [];
                if (!empty($this->authors_list) && is_array($this->authors_list)) {
                    foreach ($this->authors_list as $key => $authorData) {
                        if (empty($authorData['name'])) continue;
                        $existingAuthor = Participant::where('name', $authorData['name'])
                            ->where('type', ParticipantTypeEnum::AUTHORS->value)
                            ->first();

                        if ($existingAuthor) {
                            $author = $existingAuthor;
                        } else {
                            $author = Participant::create([
                                'name' => $authorData['name'],
                                'type' => ParticipantTypeEnum::AUTHORS->value,
                            ]);
                        }
                        $processedAuthorIds[] = $author->id;
                        PostRelation::create([
                            'post_id' => $this->Post->id,
                            'relationable_id' => $author->id,
                            'relationable_type' => Participant::class,
                            'relationable_is_main' => $key == 0 ? 1 : 0,
                        ]);
                    }
                }
                if (!empty($validation['author_id']) && is_array($validation['author_id'])) {
                    foreach ($validation['author_id'] as $key => $row) {
                        if (!in_array($row, $processedAuthorIds)) {
                            PostRelation::create([
                                'post_id' => $this->Post->id,
                                'relationable_id' => $row,
                                'relationable_type' => Participant::class,
                                'relationable_is_main' => $key == 0 && empty($this->authors_list) ? 1 : 0,
                            ]);
                        }
                    }
                }

                $this->Post->types()->forceDelete();
                $processedTypeIds = [];
                if (!empty($this->types_list) && is_array($this->types_list)) {
                    foreach ($this->types_list as $key => $typeData) {
                        if (empty($typeData['name'])) continue;
                        $existingType = \App\Models\Type::where('type_name', $typeData['name'])->first();

                        if ($existingType) {
                            $type = $existingType;
                        } else {
                            $type = \App\Models\Type::create(['type_name' => $typeData['name']]);
                        }
                        $processedTypeIds[] = $type->id;
                        PostRelation::create([
                            'post_id' => $this->Post->id,
                            'relationable_id' => $type->id,
                            'relationable_type' => \App\Models\Type::class,
                            'relationable_is_main' => 0,
                        ]);
                    }
                }
                if (!empty($validation['type_id']) && is_array($validation['type_id'])) {
                    foreach ($validation['type_id'] as $key => $row) {
                        if (!in_array($row, $processedTypeIds)) {
                            PostRelation::create([
                                'post_id' => $this->Post->id,
                                'relationable_id' => $row,
                                'relationable_type' => Type::class,
                                'relationable_is_main' => $key == 0 && empty($this->types_list) ? 1 : 0,
                            ]);
                        }
                    }
                }

                $this->Post->special_files()->forceDelete();
                if (!empty($validation['special_file_id'])) {
                    PostRelation::create([
                        'post_id' => $this->Post->id,
                        'relationable_id' => $validation['special_file_id'],
                        'relationable_type' => SpecialFile::class,
                        'relationable_is_main' => 1,
                    ]);
                }

                // File handling
                $this->Post->files()->updateOrCreate(
                    ['model_type' => Post::class, 'model_column' => 'image'],
                    ['file_id' => $validation['image'] ?? null,]
                );

                $this->Post?->files()?->where('model_column', 'morePictures')->delete();
                if (isset($validation['morePictures']) && is_array($validation['morePictures'])) {
                    foreach ($validation['morePictures'] as $picture) {
                        $this->Post->files()->create([
                            'file_id' => $picture,
                            'model_type' => Post::class,
                            'model_column' => 'morePictures',
                        ]);
                    }
                }

                $this->Post?->files()?->where('model_column', 'portrait_image')->delete();
                if (isset($validation['portrait_image'])) {
                    $this->Post->files()->create([
                        'file_id' => $validation['portrait_image'],
                        'model_type' => Post::class,
                        'model_column' => 'portrait_image',
                    ]);
                }

                $this->Post?->files()?->where('model_column', 'slider_image')->delete();
                if (isset($validation['slider_image'])) {
                    $this->Post->files()->create([
                        'file_id' => $validation['slider_image'],
                        'model_type' => Post::class,
                        'model_column' => 'slider_image',
                    ]);
                }

                $this->Post?->files()?->where('model_column', 'social_media_image')->delete();
                if (isset($validation['social_media_image'])) {
                    $this->Post->files()->create([
                        'file_id' => $validation['social_media_image'],
                        'model_type' => Post::class,
                        'model_column' => 'social_media_image',
                    ]);
                }

                $this->Post?->files()?->where('model_column', 'video_url_id')->delete();
                if (isset($validation['video_url_id'])) {
                    $this->Post->files()->create([
                        'file_id' => $validation['video_url_id'],
                        'model_type' => Post::class,
                        'model_column' => 'video_url_id',
                    ]);
                }

                $this->Post?->files()?->where('model_column', 'pdf_url_id')->delete();
                if (isset($validation['pdf_url_id'])) {
                    $this->Post->files()->create([
                        'file_id' => $validation['pdf_url_id'],
                        'model_type' => Post::class,
                        'model_column' => 'pdf_url_id',
                    ]);
                }

                // Commit the transaction
                DB::commit();

                // Reset dynamic lists
                $this->publishers_list = [];
                $this->resources_list = [];
                $this->categories_list = [];
                $this->types_list = [];
                $this->authors_list = [];
                $this->state = [];

                $this->dispatch('post-edit', [
                    'status' => 'success',
                    'title' => __('messages.alert_message.success'),
                    'message' => __('messages.posts.updated_successfully')
                ]);

                if ($stayOnPage) {
                    return redirect()->route('dashboard.posts.create_update_post');
                }
            } catch (\Exception $e) {
                // Rollback the transaction on error
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            // Simply re-throw - Livewire will handle displaying inline errors
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Post update failed: ' . $e->getMessage());

            // For unexpected errors
            session()->flash('error', __('messages.posts.updated_failed'));
        }
    }
    /**
     * @throws ValidationException
     */
    /**
     * @throws ValidationException
     */
    public function generatePreview($postId = null): void
    {

        try {
            $validation = Validator::make($this->state, [
                'publish_date' => 'required|date',
                'available_date' => 'nullable|date|after:' . now(),
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255',
                'tags' => config('features.input_validation.post.tags'),
                'image' => 'required|integer|exists:files,id',
                'portrait_image' => 'nullable|sometimes|integer|exists:files,id',
                'slider_image' => 'nullable|sometimes|integer|exists:files,id',
                'social_media_image' => 'nullable|sometimes|integer|exists:files,id',
                'video_url' => 'nullable|url',
                'pdf_url' => 'nullable|url',
                'video_url_id' => 'nullable|exists:files,id',
                'pdf_url_id' => 'nullable|exists:files,id',
                'image_alt' => 'nullable|string|max:100',
                'description' => config('features.input_validation.post.description'),
                'body' => 'nullable',
                'author_id' => 'nullable|array',
                'author_id.*' => 'nullable|exists:participants,id',
                'publisher_id' => 'nullable|array',
                'publisher_id.*' => 'nullable|exists:participants,id',
                'resource_id' => 'nullable|array',
                'resource_id.*' => 'nullable|exists:participants,id',
                'type_id' => 'nullable|array',
                'type_id.*' => 'nullable|exists:types,id',
                'special_file_id' => 'nullable|exists:special_files,id',
                'category_id' => 'required|array|min:1',
                'category_id.*' => 'required|exists:categories,id',
                'news_type' => config('features.input_validation.post.news_type'),
                'morePictures' => 'nullable|array|min:1',
                'morePictures.*' => 'required|integer|exists:files,id',
            ])->validate();
            $isAvailable = true;
            $availableDate = $validation['available_date'] ?? null;

            if ($availableDate && Carbon::parse($availableDate)->isFuture()) {
                $isAvailable = false;
            }

            $this->token = Str::random(40);
            $authorData = $this->getAuthorData($validation['author_id'] ?? null);

            $categoryName = $this->getCategoryName($validation['category_id'] ?? null);

            $imagePath = $this->getImagePreviewPath($validation['image'] ?? null);

            if (is_null($postId)) {

                $previewData = [
                    'post_id' => null,
                    'title' => $validation['title'] ?? '',
                    'body' => $validation['body'] ?? '',
                    'description' => $validation['description'] ?? '',
                    'image_name' => $imagePath,
                    'image_alt' => $validation['image_alt'] ?? $validation['title'] ?? '',
                    'publish_date' => $validation['publish_date'] ?? now(),
                    'category' => $categoryName,
                    'category_id' => $validation['category_id'] ?? [],
                    'tags' => is_string($validation['tags'] ?? '') ? explode(',', $validation['tags']) : ($validation['tags'] ?? []),
                    'author' => $authorData['names'] ?? null,
                    'author_id' => $validation['author_id'] ?? [],
                    'author_work' => $authorData['work'] ?? null,
                    'author_image' => $authorData['images'] ?? null,
                    'author_description' => $authorData['description'] ?? null,
                    'slug' => $validation['slug'] ?? '',
                    'video_url' => $validation['video_url'] ?? null,
                    'pdf_url' => $validation['pdf_url'] ?? null,
                    'news_type' => $validation['news_type'] ?? null,
                    'is_preview' => true, // Mark as preview
                    'created_at' => now(),
                    'available_date' => $availableDate,
                    'is_available' => $isAvailable,
                ];


            } else {
                // For existing posts
                $post = Post::with([
                    'categories.relationable',
                    'tags.relationable',
                    'authors.relationable',
                    'files.file',
                ])->findOrFail($postId);
                $postAvailableDate = $post->post_special_content?->available_date ?? $availableDate;
                $isAvailable = true;
                if ($postAvailableDate && Carbon::parse($postAvailableDate)->isFuture()) {
                    $isAvailable = false;
                }

                $previewData = [
                    'post_id' => $post->id,
                    'title' => $validation['title'] ?? $post->title,
                    'body' => $validation['body'] ?? $post->body,
                    'description' => $validation['description'] ?? $post->description,
                    'image_name' => $imagePath ?? $post->files->first()?->file?->path,
                    'image_alt' => $validation['image_alt'] ?? $post->image_alt ?? $validation['title'] ?? '',
                    'publish_date' => $validation['publish_date'] ?? $post->publish_date,
                    'category' => $categoryName ?? $post->categories->first()?->relationable?->category_title,
                    'category_id' => $validation['category_id'] ?? [$post->categories->first()?->relationable?->id],
                    'tags' => is_string($validation['tags'] ?? '') ? explode(',', $validation['tags']) : ($validation['tags'] ?? $post->tags->pluck('relationable.tag_name')->toArray()),
                    'author' => $authorData['names'] ?? $post->authors->first()?->relationable?->name,
                    'author_id' => $validation['author_id'] ?? [$post->authors->first()?->relationable?->id],
                    'author_work' => $authorData['work'] ?? $post->authors->first()?->relationable?->work,
                    'author_image' => $authorData['images'] ?? $post->authors->first()?->relationable?->image,
                    'author_description' => $authorData['description'] ?? $post->authors->first()?->relationable?->description,
                    'is_preview' => true,
                    'available_date' => $postAvailableDate,
                    'is_available' => $isAvailable,
                ];

            }

            $cacheKey = "post-preview:$this->token";
            $cacheSuccess = Cache::put($cacheKey, $previewData, now()->addHours(24));

            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                throw new \Exception('Failed to store preview data in cache');
            }
            $previewUrl = route('posts.preview', ['token' => $this->token]);
            $this->dispatch('open-preview-tab', url: $previewUrl);


        } catch (ValidationException $e) {

            throw $e;
        } catch (\Exception $e) {

            throw $e;
        }
    }

    private function getCategoryName($categoryId)
    {
        if (!$categoryId) return null;

        $categoryNames = [];

        if (is_array($categoryId) && !empty($categoryId)) {
            foreach ($categoryId as $id) {
                $category = Category::find($id);
                if ($category && $category->category_title) {
                    $categoryNames[] = $category->category_title;
                }
            }
            return implode(', ', $categoryNames);
        }

        $category = Category::find($categoryId);
        return $category?->category_title;
    }

    private function getAuthorData($authorId)
    {

        if (!$authorId) {
            return [
                'names' => null,
                'work' => null,
                'description' => null,
                'images' => null,
            ];
        }

        $authorNames = [];
        $authorWorks = [];
        $authorDescriptions = [];
        $authorImages = [];

        if (is_array($authorId) && !empty($authorId)) {

            foreach ($authorId as $index => $id) {
                $author = Participant::find($id);

                if ($author) {
                    if ($author->name) {
                        $authorNames[] = $author->name;
                    }
                    if ($author->work) {
                        $authorWorks[] = $author->work;
                    }
                    if ($author->description) {
                        $authorDescriptions[] = $author->description;
                    }
                    // Get author image - check both direct image and files relation
                    $authorImage = $author->image;
                    if (!$authorImage && $author->files()->exists()) {
                        $authorImage = $author->files()->first()?->file?->path;
                    }
                    if ($authorImage) {
                        $authorImages[] = $authorImage;
                    }
                }
            }

            $result = [
                'names' => !empty($authorNames) ? implode(', ', $authorNames) : null,
                'work' => !empty($authorWorks) ? implode(', ', $authorWorks) : null,
                'description' => !empty($authorDescriptions) ? implode(', ', $authorDescriptions) : null,
                'images' => !empty($authorImages) ? $authorImages[0] : null,
            ];

            return $result;
        }

        // If it's a single ID
        $author = Participant::find($authorId);
        $result = [
            'names' => $author?->name,
            'work' => $author?->work,
            'description' => $author?->description,
            'images' => $author?->image,
        ];

        return $result;
    }

    protected function generatePreviewData(): array

    {
        return [
            'post_id' => null,
            'title' => $this->title,
            'body' => $this->body,
            'description' => $this->description,
            'image_name' => $this->uploadedFiles[0]['path'] ?? null, // Use uploaded file path
            'image_alt' => $this->image_alt ?? $this->title,
            'publish_date' => now()->format('Y-m-d H:i:s'),
            'category' => Category::find($this->category_id)?->category_title ?? 'غير مصنف',
            'category_id' => [$this->category_id],
            'tags' => $this->tags ? explode(',', $this->tags) : [],
            'author' => auth()->user()->name, // Current user as author
            'author_id' => [auth()->id()],
            'author_work' => auth()->user()->work ?? '',
            'author_description' => auth()->user()->description ?? '',
        ];
    }

    private function getImagePath($imageId)
    {
        if (!$imageId) return null;

        $file = Files::find($imageId);
        return $file?->path;
    }

    private function getImagePreviewPath($imageId)
    {
        if (!$imageId) {
            return null;
        }

        // First try to get from state (for newly selected images)
        if (isset($this->state['image_name']) && $this->state['image_name']) {
            return $this->state['image_name'];
        }

        // Fallback to database lookup
        $file = Files::find($imageId);
        $path = $file?->path;

        return $path;
    }

    protected function formatPostForPreview(Post $post): array
    {
        return [
            'post_id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'description' => $post->description,
            'image_name' => $post->files->first()?->file?->path ?? null,
            'image_alt' => $post->image_alt ?? $post->title,
            'publish_date' => $post->publish_date,
            'category' => $post->category?->relationable?->category_title,
            'category_id' => [$post->category?->relationable?->id],
            'tags' => $post->tags->pluck('relationable.tag_name')->toArray(),
            'author' => $post->author?->relationable?->name,
            'author_id' => [$post->author?->relationable?->id],
            'author_work' => $post->author?->relationable?->work,
            'author_description' => $post->author?->relationable?->description,
        ];
    }


    public function convertTagsToLinks($body, $tags): false|string
    {
        return $body;
    }

}
