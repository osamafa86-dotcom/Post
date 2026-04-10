<?php

namespace App\Livewire\Dashboard;

use App\Enums\CategoryStyleEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\LinkPosition;
use App\Enums\LinkStatusEnum;
use App\Enums\LinkUrlTargetEnum;
use App\Enums\TagTypeEnum;
use App\Models\Category;
use App\Models\FooterLink;
use App\Models\Icon;
use App\Models\NavbarLink;
use App\Models\SocialMedia;
use App\Models\SpecialPage;
use App\Models\Tag;
use App\Models\Type;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ListNavbarLinks extends Component
{
    use WhereIn;
    public array $state = [];
    public array $link = [];
    public array $footer_link = [];
    public array $sub_link = [];
    public array $link_order = [];
    public array $footer_link_order = [];
    public  $social_medias;
    public array $social_media_order = [];
    public array $sub_link_order = [];
    public array $category_order = [];
    public bool $showEdit = false;
    public int $parent = 0;
    public $SocialMedia_;
    public $selectedCategories = [];
    public $selectedTypes = [];
    public $link_selected;
    public array $routes = [
        'tag_news',
        'category_news',
    ];
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-navbar-links');
    }


    public function selectUrl()
    {
        if ($this->link_selected && $this->link_selected != 'custom') {
            $this->state['link_url'] = "/special_pages/" . $this->link_selected;
        } else {
            $this->state['link_url'] = '';
        }
    }

    public function addNew(): void
    {
        $this->state = [];
        $this->dispatch('show_navbar_link_modal');
    }
    public function addNewFooter(): void
    {
        $this->state = [];
        $this->dispatch('show_footer_link_modal');
    }

    public function addNewSub($parent): void
    {
        if (isset($parent)) {
            $this->parent = $parent;
            $this->state = [];
            $this->state['parent_id'] = $parent;
            $this->dispatch('show_navbar_link_modal');
        }
    }

    public function showSubLinks($parent): void
    {
        if (isset($parent)) {

            $this->parent = $parent;
            $this->state['parent_id'] = $parent;
            $this->sub_link = $this->navbar_sub_links();
            foreach ($this->sub_link as $key => $value) {
                $this->sub_link_order[$key] = $value['link_order'];
            }
        }
    }

    public function mount(): void
    {
        $this->selectedCategories = $this->categories
            ->mapWithKeys(function ($category) {
                return [
                    $category->id => [
                        'value' => (bool)$category->show_index,
                        'type' => $category->category_style,
                    ],
                ];
            })->toArray();
        $this->selectedTypes = $this->types
            ->mapWithKeys(function ($type) {
                return [
                    $type->id => [
                        'value' => (bool)$type->show_index,
                    ],
                ];
            })->toArray();


        $this->link = $this->navbar_links();
        foreach ($this->link as $key => $value) {
            $this->link_order[$key] = $value['link_order'];
        }
        $this->footer_link = $this->footer_links();
        foreach ($this->footer_link as $key => $value) {
            $this->footer_link_order[$key] = $value['link_order'];
        }

        $this->sub_link = $this->navbar_sub_links();
        foreach ($this->sub_link as $key => $subLink) {
            $this->sub_link_order[$key] = $subLink['link_order'];
        }

        $this->social_medias = SocialMedia::orderBy('order')->get();
        foreach ($this->social_medias as $key => $item) {
            $this->social_media_order[$key] = $item['social_media_order'];
        }

        $pages = SpecialPage::query()->pluck('page_title')->toArray();
        $this->routes = array_merge($this->routes, $pages);
    }

    public function updateSubLinkOrder($orderedItems): void
    {

        foreach ($orderedItems as $itemId) {
            NavbarLink::find($itemId['value'])->update(['link_order' => $itemId['order']]);
        }
        $this->link = $this->navbar_sub_links();

        foreach ($this->link as $key => $value) {
            $this->link_order[$key] = $value['link_order'];
        }

        $this->dispatch('navbar_links_updated');
    }

    public function updateLinkOrder($orderedItems): void
    {
        foreach ($orderedItems as $itemId) {
            NavbarLink::find($itemId['value'])->update(['link_order' => $itemId['order']]);
        }
        $this->link = $this->navbar_links();
        foreach ($this->link as $key => $value) {
            $this->link_order[$key] = $value['link_order'];
        }
        // Optionally, dispatch an event for frontend update
        $this->dispatch('navbar_links_updated');
    }

    public function updateFooterLinkOrder($orderedItems): void
    {
        foreach ($orderedItems as $itemId) {
            FooterLink::find($itemId['value'])->update(['link_order' => $itemId['order']]);
        }
        $this->footer_link = $this->footer_links();
        foreach ($this->footer_link as $key => $value) {
            $this->footer_link_order[$key] = $value['link_order'];
        }
        // Optionally, dispatch an event for frontend update
        $this->dispatch('footer_links_updated');
    }

    public function updateCategoryOrder($orderedItems): void
    {

        foreach ($orderedItems as $itemId) {


            Category::find($itemId['value'])->update(['order' => $itemId['order']]);
        }


        $this->dispatch('categories_order_updated');
    }

    public function updateTypeOrder($orderedItems): void
    {
        foreach ($orderedItems as $itemId) {
            Type::find($itemId['value'])->update(['order' => $itemId['order']]);
        }
        $this->dispatch('types_order_updated');
    }
    public function updateSocialMediaOrder($orderedItems): void
    {
        foreach ($orderedItems as $itemId) {
            SocialMedia::find($itemId['value'])->update(['order' => $itemId['order']]);
        }
        $this->social_medias = SocialMedia::orderBy('order')->get();

        $this->dispatch('social_media_order_updated');
    }

    public function reorderSocialMedia(): void
    {
        $order = 0;
        foreach (SocialMedia::orderBy('created_at')->get() as $item) {
            $item->update(['order' => $order++]);
        }

        $this->social_media_order = $this->social_medias
            ->pluck('order', 'id')
            ->toArray();

        $this->dispatch('social_media_order_updated');
    }
    #[Computed]
    public function navbar_links(): array
    {
        $links = NavbarLink::with('children')->whereNull('parent_id')->orderBy('link_order', 'asc')->get();
        $data = [];
        foreach ($links as $link) {
            $data[$link->link_name] = [];
            $data[$link->link_name] += [
                'id' => $link->id,
                'link_order' => $link->link_order,
                'link_name' => $link->link_name,
                'link_url' => $link->link_url,
                'icon_id' => $link->icon_id,
                'link_status' => $link->link_status,
                'link_open' => $link->link_open,
                'parent_id' => $link->parent_id,
                'children' => $link->children->toArray(),
            ];
        }
        return $data;
    }
    #[Computed]
    public function footer_links(): array
    {
        $links = FooterLink::with('children')->whereNull('parent_id')->orderBy('link_order', 'asc')->get();
        $data = [];
        foreach ($links as $link) {
            $data[$link->link_name] = [];
            $data[$link->link_name] += [
                'id' => $link->id,
                'link_order' => $link->link_order,
                'link_name' => $link->link_name,
                'link_url' => $link->link_url,
                'icon_id' => $link->icon_id,
                'link_status' => $link->link_status,
                'link_open' => $link->link_open,
                'parent_id' => $link->parent_id,
                'children' => $link->children->toArray(),
            ];
        }
        return $data;
    }
    #[Computed]
    public function navbar_sub_links(): array
    {

        $links = NavbarLink::with('parent')->where('parent_id', $this->parent)->orderBy('link_order', 'asc')->get();
        $data = [];

        foreach ($links as $link) {
            $data[$link->link_name] = [];
            $data[$link->link_name] += [
                'id' => $link->id,
                'link_order' => $link->link_order,
                'link_name' => $link->link_name,
                'link_url' => $link->link_url,
                'icon_id' => $link->icon_id,
                'link_status' => $link->link_status,
                'link_open' => $link->link_open,
                'parent_id' => $link->parent_id,
                'parent' => $link?->parent?->first()?->toArray(),
            ];
        }
        return $data;
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('order')->get();
    }
    #[Computed]
    public function types()
    {
        return Type::orderBy('order')->get();
    }

    #[Computed]
    public function category_podcasts()
    {
        return Category::where('category_type', CategoryTypeEnum::PODCAST->value)->orderBy('order')->get();
    }

    #[Computed]
    public function category_videos()
    {
        return Category::where('category_type', CategoryTypeEnum::VIDEO->value)->orderBy('order')->get();
    }

    #[Computed]
    public function tags()
    {
        return Tag::where('tag_type', TagTypeEnum::NEWS->value)->get();
    }

    #[Computed]
    public function specialPages()
    {
        return SpecialPage::get();
    }

    /**
     * @throws ValidationException
     */
    public function createNavbarLink(): void
    {
        if (!empty($this->state['link_status'])) {
            $value = $this->state['link_status'];

            // يحاول يجيب Enum من القيمة
            $enum = LinkStatusEnum::tryFromName($value);

            if ($enum) {
                $this->state['link_status'] = $enum->value;
            }
        }
        $validated = Validator::make($this->state, [
            'link_name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value) {
                    $existingLink = NavbarLink::where('link_name', $value)->first();
                    $currentId = $this->state['id'] ?? null;

                    if ($existingLink && $existingLink->id != $currentId) {
                        throw ValidationException::withMessages([
                            $attribute => [__('validation.name_found')],
                        ]);
                    }
                }
            ],
            'link_url' => 'nullable|string',
            'icon_id' => 'nullable|exists:icons,id',
            'link_status' => 'required|in:' . $this->whereIn()['LinkStatusEnum'],
            'link_open' => 'required|in:' . $this->whereIn()['LinkUrlTargetEnum'],
            'parent_id' => 'nullable|exists:navbar_links,id',
        ])->validate();

        //  $last = NavbarLink::orderBy('link_order', 'desc')->when(isset($this->parent), fn($q) => $q->where('parent_id', $this->parent))->first();
        $last = NavbarLink::orderBy('link_order', 'desc')->first();

        $link = NavbarLink::create([
            'link_name' => $validated['link_name'],
            'link_url' => $validated['link_url'] ?? null,
            'icon_id' => $validated['icon_id'] ?? null,
            'link_order' => $last ? $last->link_order + 1 : 1,
            'link_status' => $validated['link_status'] ?? null,
            'link_open' => $validated['link_open'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);
        if (!isset($validated['parent_id'])) {
            $this->link[$link->link_name] = $link->toArray();
            $this->link_order[$link->link_name] = $link->link_order;
        } else {
            $this->sub_link[$link->link_name] = $link->toArray();
            $this->sub_link_order[$link->link_name] = $link->link_order;
        }
        $this->dispatch('hide_navbar_link_modal');
        $this->dispatch('navbar_links_updated');
    }

    /**
     * @throws ValidationException
     */
    public function updateNavbarLink($key): void
    {
        try {
            $link = $this->link[$key];
            $currentId = $link['id'];
            $validated = Validator::make($link, [
                'link_name' => [
                    'required','string','max:100',
                    function ($attribute, $value, $fail) use ($currentId) {
                        $exists = NavbarLink::where('link_name', $value)
                            ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                            ->exists();

                        if ($exists) {
                            $fail(__('validation.name_found'));
                        }
                    },
                ],
                'link_url' => 'nullable|string',
                'icon_id' => 'nullable|exists:icons,id',
                'link_open' => 'required|in:' . $this->whereIn()['LinkUrlTargetEnum'],
            ], [
                'link_name.required' => __('validation.name_required'),
                'link_name.unique' => __('validation.name_found'),
            ])->validate();
            $link = NavbarLink::where('link_name', $key)->first();


            $link->update([
                'link_name' => $validated['link_name'],
                'link_url' => $validated['link_url'],
                'icon_id' => $this->state['icon_id'] ?? null,
                'link_open' => $validated['link_open'],
            ]);
            $this->link_order[$link->link_name] = $link->link_order;


            $this->dispatch('navbar_links_updated');

        } catch (ValidationException $e) {
            //
        }
    }


    /**
     * @throws ValidationException
     */
    public function createFooterLink(): void
    {
        if (!empty($this->state['link_status'])) {
            $value = $this->state['link_status'];

            // يحاول يجيب Enum من القيمة
            $enum = LinkStatusEnum::tryFromName($value);

            if ($enum) {
                $this->state['link_status'] = $enum->value;
            }
        }
        $validated = Validator::make($this->state, [
            'link_name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value) {
                    $existingLink = FooterLink::where('link_name', $value)->first();
                    $currentId = $this->state['id'] ?? null;

                    if ($existingLink && $existingLink->id != $currentId) {
                        throw ValidationException::withMessages([
                            $attribute => [__('validation.name_found')],
                        ]);
                    }
                }
            ],
            'link_url' => 'nullable|string',
            'icon_id' => 'nullable|exists:icons,id',
            'link_status' => 'required|in:' . $this->whereIn()['LinkStatusEnum'],
            'link_open' => 'required|in:' . $this->whereIn()['LinkUrlTargetEnum'],
            'parent_id' => 'nullable|exists:footer_links,id',
        ])->validate();

        $last = FooterLink::orderBy('link_order', 'desc')->first();

        $link = FooterLink::create([
            'link_name' => $validated['link_name'],
            'link_url' => $validated['link_url'] ?? null,
            'icon_id' => $validated['icon_id'] ?? null,
            'link_order' => $last ? $last->link_order + 1 : 1,
            'link_status' => $validated['link_status'] ?? null,
            'link_open' => $validated['link_open'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);
        if (!isset($validated['parent_id'])) {
            $this->footer_link[$link->link_name] = $link->toArray();
            $this->footer_link_order[$link->link_name] = $link->link_order;
        } else {
            $this->sub_link[$link->link_name] = $link->toArray();
            $this->sub_link_order[$link->link_name] = $link->link_order;
        }
        $this->dispatch('hide_footer_link_modal');
        $this->dispatch('footer_links_updated');
    }

    /**
     * @throws ValidationException
     */
    public function updateFooterLink($key): void
    {
        try {
            $link = $this->footer_link[$key];
            $currentId = $link['id'];
            $validated = Validator::make($link, [
                'link_name' => [
                    'required','string','max:100',
                    function ($attribute, $value, $fail) use ($currentId) {
                        $exists = FooterLink::where('link_name', $value)
                            ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
                            ->exists();

                        if ($exists) {
                            $fail(__('validation.name_found'));
                        }
                    },
                ],
                'link_url' => 'nullable|string',
                'icon_id' => 'nullable|exists:icons,id',
                'link_open' => 'required|in:' . $this->whereIn()['LinkUrlTargetEnum'],
            ], [
                'link_name.required' => __('validation.name_required'),
                'link_name.unique' => __('validation.name_found'),
            ])->validate();
            $link = FooterLink::where('link_name', $key)->first();


            $link->update([
                'link_name' => $validated['link_name'],
                'link_url' => $validated['link_url'],
                'icon_id' => $this->state['icon_id'] ?? null,
                'link_open' => $validated['link_open'],
            ]);
            $this->footer_link_order[$link->link_name] = $link->link_order;


            $this->dispatch('footer_links_updated');

        } catch (ValidationException $e) {
            //
        }
    }
    /**
     * @throws ValidationException
     */
    public function updateSubNavbarLink(): void
    {
        $validated = Validator::make($this->sub_link, [
            '*.link_name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value) {
                    $sub_link = NavbarLink::where('link_name', $value)->first();
                    $name = explode('.', $attribute)[0];
                    if ($sub_link && $sub_link->id != $this->sub_link[$name]['id']) {
                        throw ValidationException::withMessages([
                            $attribute => [__('validation.name_found')],
                        ]);
                    }
                }
            ],
            '*.link_url' => 'nullable|string',
            '*.icon_id' => 'nullable|exists:icons,id',
            '*.link_status' => 'required|in:' . $this->whereIn()['LinkStatusEnum'],
            '*.link_open' => 'required|in:' . $this->whereIn()['LinkUrlTargetEnum'],
        ], [
            '*.link_name.required' => __('validation.name_required'),
            '*.link_name.unique' => __('validation.name_found'),
        ])->validate();
        foreach ($validated as $key => $value) {
            $sub_link = NavbarLink::where('link_name', $key)->first();
            $sub_link->update([
                'link_name' => $value['link_name'] ?? null,
                'link_url' => $value['link_url'] ?? null,
                'icon_id' => $value['icon_id'] ?? null,
                'link_status' => $value['link_status'] ?? null,
                'link_open' => $value['link_open'] ?? null,
            ]);
            $this->sub_link_order[$sub_link->link_name] = $sub_link->link_order;
        }

        $this->dispatch('navbar_links_updated');
    }

    public function deleteNavbarLink($data): void
    {
        $link = NavbarLink::where('link_name', $data)->first();
        $link->forceDelete();
    }
    public function deleteFooterLink($data): void
    {
        $link = FooterLink::where('link_name', $data)->first();
        $link->forceDelete();
    }

    /**
     * @throws ValidationException
     */
    public function updatedSubLinkOrder($value, $key): void
    {
        if (is_numeric($value)) {
            NavbarLink::where('link_name', $key)->where('parent_id', $this->parent)->update(['link_order' => $value]);
            $where = NavbarLink::where('link_order', '>=', $value)->where('parent_id', $this->parent)->whereNot('link_name', $key)->orderBy('link_order', 'asc')->get();
            $order = $value;
            foreach ($where as $row) {
                $row->update(['link_order' => ++$order]);
                $this->sub_link_order[$row->link_name] = $row->link_order;
            }
            throw  ValidationException::withMessages([
                'sub_link_order_success' => [__('validation.edit_success')],
            ]);
        } else {
            throw  ValidationException::withMessages([
                'sub_link_order_error' => [__('validation.number_required')],
            ]);
        }
    }

    public function reorderSubLinks(): void
    {
        $order = 0;
        $sub_links = NavbarLink::orderBy('created_at')->where('parent_id', $this->parent)->get();
        foreach ($sub_links as $value) {
            $value->update(['link_order' => ++$order]);
            $this->sub_link_order[$value->link_name] = $value->link_order;
        }
        $this->dispatch('navbar_links_updated');
    }


    /**
     * @throws ValidationException
     */
    public function updatedLinkOrder($value, $key): void
    {
        if (is_numeric($value)) {
            NavbarLink::where('link_name', $key)->update(['link_order' => $value]);
            $where = NavbarLink::where('link_order', '>=', $value)->whereNull('parent_id')->whereNot('link_name', $key)->orderBy('link_order', 'asc')->get();
            $order = $value;
            foreach ($where as $row) {
                $row->update(['link_order' => ++$order]);
                $this->link_order[$row->link_name] = $row->link_order;
            }
            throw  ValidationException::withMessages([
                'link_order_success' => [__('validation.edit_success')],
            ]);
        } else {
            throw  ValidationException::withMessages([
                'link_order_error' => [__('validation.number_required')],
            ]);
        }
    }

    public function reorderLinks(): void
    {
        $order = 0;
        $links = NavbarLink::orderBy('created_at')->whereNull('parent_id')->get();
        foreach ($links as $value) {
            $value->update(['link_order' => ++$order]);
            $this->link_order[$value->link_name] = $value->link_order;
        }
        //        $this->dispatch('navbar_links_updated');
    }
    /**
     * @throws ValidationException
     */
    public function updatedFooterLinkOrder($value, $key): void
    {
        if (is_numeric($value)) {
            FooterLink::where('link_name', $key)->update(['link_order' => $value]);
            $where = FooterLink::where('link_order', '>=', $value)->whereNull('parent_id')->whereNot('link_name', $key)->orderBy('link_order', 'asc')->get();
            $order = $value;
            foreach ($where as $row) {
                $row->update(['link_order' => ++$order]);
                $this->link_order[$row->link_name] = $row->link_order;
            }
            throw  ValidationException::withMessages([
                'link_order_success' => [__('validation.edit_success')],
            ]);
        } else {
            throw  ValidationException::withMessages([
                'link_order_error' => [__('validation.number_required')],
            ]);
        }
    }

    public function reorderFooterLinks(): void
    {
        $order = 0;
        $links = FooterLink::orderBy('created_at')->whereNull('parent_id')->get();
        foreach ($links as $value) {
            $value->update(['link_order' => ++$order]);
            $this->footer_link_order[$value->link_name] = $value->link_order;
        }
        //        $this->dispatch('navbar_links_updated');
    }

    public function showReorderModal(): void
    {
        // Dispatch event to show modal
        $this->dispatch('show-reorder-modal');
    }
    public function showFooterReorderModal(): void
    {
        // Dispatch event to show modal
        $this->dispatch('show-footer-reorder-modal');
    }

    // Method to handle reordering logic
    public function reorderLink(): void
    {
        $order = 0;
        $links = NavbarLink::orderBy('created_at')->get();
        foreach ($links as $value) {
            $value->update(['link_order' => ++$order]);
            $this->link_order[$value->link_name] = $value->link_order;
        }
        // Your reorder logic here (update link orders, etc.)

        // Dispatch event to hide the modal after action
        $this->dispatch('hide-reorder-modal');
        $this->dispatch('navbar_links_updated');
    }
    // Method to handle reordering logic
    public function reorderFooterLink(): void
    {
        $order = 0;
        $links = FooterLink::orderBy('created_at')->get();
        foreach ($links as $value) {
            $value->update(['link_order' => ++$order]);
            $this->link_order[$value->link_name] = $value->link_order;
        }
        // Your reorder logic here (update link orders, etc.)

        // Dispatch event to hide the modal after action
        $this->dispatch('hide-footer-reorder-modal');
        $this->dispatch('footer_links_updated');
    }

    public function updatedState($value, $key): void
    {
        $features = config('features');
        $routeMap = $features['routes_map'] ?? [];
        $defaultPages = $features['link_status']['default_pages'] ?? [];

        $pureKey = Str::after($key, '.'); // مثلاً: state.category_id → category_id

        // 🟢 الحالة: link_status = صفحة ثابتة
        if ($pureKey === 'link_status') {
            if (array_key_exists($value, $defaultPages)) {
                $this->setStateLink($value, $defaultPages[$value], LinkUrlTargetEnum::SAME_WINDOW->value);
                return;
            }

            if (isset($routeMap[$value])) {
                $url = $this->parseUrlTemplate($routeMap[$value], []);
                $this->setStateLink('', $url, LinkUrlTargetEnum::SAME_WINDOW->value);
                return;
            }
        }

        // 🟠 روابط مرتبطة بموديل معين
        $modelMap = [
            'category_id' => \App\Models\Category::class,
            'tag_id' => \App\Models\Tag::class,
            'type_id' => \App\Models\Type::class,
            'podcast_id' => \App\Models\Category::class,
            'video_id' => \App\Models\Category::class,
            'special_page_id' => \App\Models\SpecialPage::class,
        ];

        $routeKeyMap = [
            'category_id' => LinkStatusEnum::CATEGORY->name,
            'tag_id' => LinkStatusEnum::TAG->name,
            'type_id' => LinkStatusEnum::TYPE->name,
            'podcast_id' => LinkStatusEnum::PODCAST->name,
            'video_id' => LinkStatusEnum::VIDEO->name,
            'special_page_id' => LinkStatusEnum::SPECIAL_PAGE->name,
        ];

        if (isset($modelMap[$pureKey]) && isset($routeKeyMap[$pureKey]) && isset($routeMap[$routeKeyMap[$pureKey]])) {
            $modelClass = $modelMap[$pureKey];
            $modelInstance = $modelClass::find($value);
            if ($modelInstance) {
                $routeTemplate = $routeMap[$routeKeyMap[$pureKey]];
                $url = $this->parseUrlTemplateWithModel($routeTemplate, $modelInstance);
                $name = $modelInstance->title ?? $modelInstance->name ?? $modelInstance->category_title ?? $modelInstance->page_title ?? $modelInstance->type_name ?? $modelInstance->podcast_title ?? $modelInstance->video_title ?? $modelInstance->special_page_title ?? '';
                $this->setStateLink($name, $url, LinkUrlTargetEnum::SAME_WINDOW->value);
            }
        }
    }

    private function setStateLink($name, $url, $open): void
    {
        $this->state['link_name'] = $name;
        $this->state['link_url'] = $url;
        $this->state['link_open'] = $open;
    }

    private function parseUrlTemplate(string $template, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }

    private function parseUrlTemplateWithModel(string $template, $model): string
    {
        preg_match_all('/\{(\w+)\}/', $template, $matches);
        $placeholders = $matches[1] ?? [];

        $replacements = [];

        foreach ($placeholders as $key) {
            $replacements[$key] = $model->{$key} ?? '';
        }

        return $this->parseUrlTemplate($template, $replacements);
    }

    #[Computed]
    public function LinkStatusOptions(): array
    {
        $options = [];

        // Default pages from config
        $defaultPages = config('features.link_status.default_pages', []);
        foreach ($defaultPages as $label => $url) {
            $options[] = [
                'value' => $label,
                'label' => $label
            ];
        }

        // Enum-based link statuses
        foreach (LinkStatusEnum::available() as $enum) {
            $options[] = [
                'value' => $enum->name,
                'label' => $enum->label()
            ];
        }

        return $options;
    }


    public function addNewSocialMedia(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->dispatch('show_social_media_modal');
    }

    public function createSocialMedia(): void
    {
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:255',
            'url' => 'required|string|url',
            'icon_id' => 'nullable|exists:icons,id',
            'position' => ['required', Rule::in(array_column(LinkPosition::cases(), 'value'))],


        ])->validate();


       $socialMedia = SocialMedia::query()->create($validation);
        $this->refreshSocialMedias();

        $this->dispatch('hide_social_media_modal', ['message' => __('validation.saved_success')]);

    }
    public function refreshSocialMedias(): void
    {
        $this->social_medias = SocialMedia::orderBy('order')->get();
    }

    #[Computed]
    public function social_medias(): Collection|array
    {
        return SocialMedia::query()->get();
    }

    #[Computed]
    public function icons(): Collection|array
    {

        return Icon::query()->select('id', 'icon_path')->get();
    }

    public function edit(SocialMedia $socialMedia): void
    {
        $this->state = [];
        $this->SocialMedia_ = $socialMedia;
        $this->state = $socialMedia->toArray();
        $this->showEdit = true;
        $this->dispatch('show_social_media_modal');
    }

    /**
     * @throws ValidationException
     */
    public function updateSocialMedia(): void
    {

        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:255',
            'url' => 'required|string|url',
            'icon_id' => 'nullable|exists:icons,id',
            'position' => ['required', Rule::in(array_column(LinkPosition::cases(), 'value'))],
        ])->validate();

        $this->SocialMedia_->update($validation);
        $this->refreshSocialMedias();
        $this->dispatch('hide_social_media_modal', ['message' => __('validation.edit_success')]);
    }

    public function delete(SocialMedia $socialMedia): void
    {
        $this->SocialMedia_ = $socialMedia;
        $this->dispatch('show_social_media_delete');
    }

    public function socialMediaDeleteConfirm(): void
    {
        $this->SocialMedia_->delete();
        $this->social_medias = SocialMedia::orderBy('order')->get();
        $this->dispatch('hide_social_media_delete');
    }

    public function updateSelectedCategories(): void
    {
        foreach ($this->categories as $item) {
            if (isset($this->selectedCategories[$item->id])) {
                if (isset($this->selectedCategories[$item->id]['value'])) {
                    $item->update([
                        'show_index' => $this->selectedCategories[$item->id]['value'],
                    ]);
                }
                if (isset($this->selectedCategories[$item->id]['type'])) {
                    $item->update([
                        'category_style' => $this->selectedCategories[$item->id]['type']
                    ]);
                }
            }
        }
        $this->dispatch('selected_categories_updated');
    }
    public function updateSelectedTypes(): void
    {
        foreach ($this->types as $item) {
            if (isset($this->selectedTypes[$item->id])) {
                if (isset($this->selectedTypes[$item->id]['value'])) {
                    $item->update([
                        'show_index' => $this->selectedTypes[$item->id]['value'],
                    ]);
                }
            }
        }
        $this->dispatch('selected_types_updated');
    }

    public function iconSelect(Icon $icon): void
    {
        $this->state['icon_id'] = $icon->id;
    }
}
