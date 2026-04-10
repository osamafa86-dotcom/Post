<?php

namespace App\Livewire\Dashboard;

use App\Enums\CategoryTypeEnum;
use App\Enums\DateTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\TagTypeEnum;
use App\Enums\UserStatusEnum;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventRelation;
use App\Models\Files;
use App\Models\Icon;
use App\Models\Languages;
use App\Models\Participant;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserDetailRelation;
use App\Models\UserDetails;

use App\Traits\Publishable;
use App\Traits\GeneralView;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListUserDetails extends Component
{
    use WithPagination, WhereIn , WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $UserDetails_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "id";
    public string $sortDirection = "asc";
    public $selected = [];
    public $selectAll = false;
    public $delete_text ;

    public array $social_media = [];

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-user-details');
    }

    public function mount($add = null): void
    {
        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }


    #[Computed]
    public function userDetails(): LengthAwarePaginator
    {
        return UserDetails::select('*')

            ->whereHas('user', function ($query) {
                $query->where('user_status', UserStatusEnum::VOICEOVER->value);
            })
            ->when(!empty($this->search), function (Builder $q) {
                $q->whereHas('user', function ($query) {
                    $query->where('first_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->with('user')
            ->paginate(10);

    }

    #[Computed]
    public function categories()
    {
        return Category::select('id', 'category_title')->where('category_type', CategoryTypeEnum::USERS->value)->get();
    }

    #[Computed]
    public function interests(): array
    {
        return Tag::where('tag_type', TagTypeEnum::INTEREST->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }

    #[Computed]
    public function languages(): array
    {
        return Tag::where('tag_type', TagTypeEnum::LANGUAGES->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }

    #[Computed]
    public function tags(): array
    {
        return Tag::where('tag_type', TagTypeEnum::USERS->value)->select('tag_name')->get()->pluck('tag_name')->toArray();
    }

    public function sortBy($field): void
    {
        $this->sortDirection = isset($this->sortDirection) && $this->sortDirection == 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->social_media = [];
        $this->dispatch('show_form');
    }


    public function clearColumn($column, $value = null): void
    {

        if (!isset($value)) {
            $this->state[$column] = null;
            $this->state[$column . '_name'] = null;
        } else {
            if (($key = array_search($value, $this->state[$column])) !== false) {
                unset($this->state[$column][$key]);
            }
            $this->state[$column] = array_values($this->state[$column]);
            unset($this->state[$column . '_name'][$value]);
        }
    }
    public function edit(UserDetails $userDetails): void
    {
        $this->state = [];
        $this->UserDetails_ = $userDetails;
        $this->state = $userDetails->toArray();

        $this->state['first_name'] =$this->UserDetails_->user?->first_name;
        $this->state['last_name'] =$this->UserDetails_->user?->last_name;
        $this->state['email'] =$this->UserDetails_->user?->email;

        $this->social_media = $userDetails->userDetails_social_media?->toArray();

        $tags = $this->UserDetails_->tags ? implode(',', $this->UserDetails_->tags->pluck('tag_name')->toArray()) : null;
        $this->state['tags'] = $tags;
        $interests = $this->UserDetails_->interests ? implode(',', $this->UserDetails_->interests->pluck('tag_name')->toArray()) : null;
        $this->state['interests'] = $interests;
        $languages = $this->UserDetails_->languages ? implode(',', $this->UserDetails_->languages->pluck('tag_name')->toArray()) : null;
        $this->state['languages'] = $languages;

        $this->state['category_id'] = $this->UserDetails_->categories->pluck('id')->toArray();

        $this->state['password'] ='';

        $this->showEdit = true;
        $this->dispatch('show_form');
    }

    /**
     * @throws ValidationException
     */

    public function editActive(UserDetails $userDetails)
    {
        $this->UserDetails_ = $userDetails;
        $this->dispatch('show_user_edit_active');
    }
    public function create(): void
    {

        $validationUser = Validator::make($this->state, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
//            'role_id' => 'required|in:' . $this->whereIn()['Role'],
//            'manager_id' => 'integer',
//            'team_id' => 'required|integer',
            'image' => 'required|image|max:2048',


        ])->validate();

        $validationUser['password'] = Hash::make($validationUser['password']);

        $validation = Validator::make($this->state, [
            'gender' =>  'nullable|in:' . $this->whereIn()['GenderEnum'],
            'birthday' => 'nullable',
            'country' => 'nullable',
            'tags' => 'nullable',
            'languages' => 'nullable',
            'interests' => 'nullable',
            'category_id' => 'nullable|array',
            'category_id.*' => 'nullable|exists:categories,id',

            'description' => 'nullable|string|max:700',
            'user_type' => 'required|in:' . $this->whereIn()['UserDetailsTypeEnum'],
            'file' => 'nullable',
            // 'file' => 'required|mimes:mp3,wav,m4a,ogg,flac,aac|max:10240',

        ])->validate();

        if(!empty($this->state['image']))
        {
            $image = $this->state['image']->store('userDetails', config('filesystems.default'));
        }


        $user = User::query()->create([
            'first_name' => $validationUser['first_name'],
            'last_name' => $validationUser['last_name'],
            'email' => $validationUser['email'],
            'password' => $validationUser['password'],
            'user_status' =>  UserStatusEnum::VOICEOVER->value,
            'avatar' => $image ?? null,

        ]);


        if(!empty($this->state['file']))
        {
            $file = $this->state['file']->store('userDetails', config('filesystems.default'));
        }

        $validation_social_media = Validator::make($this->social_media, [
            '*.social_media_name' => 'required|string|max:100',
            '*.social_media_link' => 'required|url',
            '*.social_media_icon' => 'required|integer|exists:icons,id',
        ], [
            '*.social_media_name.required' => __('validation.attributes.social_media_name'),
            '*.social_media_link.required' => __('validation.attributes.social_media_link'),
            '*.social_media_icon.required' => __('validation.attributes.social_media_icon'),
        ])->validate();

        $UserDetail = UserDetails::query()->create([

            'file' => $file ?? null,
            'gender' => $validation['gender'] ?? null,
            'birthday' => $validation['birthday'] ?? null,
            'country' =>$validation['country'] ?? null,
            'description' => $validation['description'] ?? null,
            'is_active' => 0,
            'user_id' => $user->id,
            'user_type' => $validation['user_type'] ?? null,
        ]);


        if (!empty($validation_social_media)) {
            $UserDetail->userDetails_social_media()->createMany($validation_social_media);
        }

        if (!empty($validation['languages'])) {
            $tags = explode(',', $validation['languages']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::LANGUAGES->value]);
                UserDetailRelation::create([
                    'user_detail_id' => $UserDetail->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
        if (!empty($validation['interests'])) {

            $tags = explode(',', $validation['interests']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::INTEREST->value]);
                UserDetailRelation::create([
                    'user_detail_id' => $UserDetail->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }

        if (!empty($validation['tags'])) {
            $tags = explode(',', $validation['tags']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::USERS->value]);
                UserDetailRelation::create([
                    'user_detail_id' => $UserDetail->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }

        if (!empty($validation['category_id'])) {
            foreach ($validation['category_id'] as $key => $row)
                UserDetailRelation::create([
                    'user_detail_id' => $UserDetail->id,
                    'relationable_id' => $row,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }


        $UserDetail->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->dispatch('hide_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {

        if (!empty($this->state['image']) && $this->UserDetails_?->image != $this->state['image']) {
            $rule_image = 'required|image|max:2048';
        } else {
            $rule_image = 'nullable|exists:user_details,image';
        }
        if (!empty($this->state['file']) && $this->UserDetails_?->file != $this->state['file']) {
            $rule_file = 'required|image|max:2048';
        } else {
            $rule_file = 'nullable|exists:user_details,file';
        }

        $validationUser = Validator::make($this->state, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->state['user_id']),
            ],
            'password' => 'nullable|password|min:8|confirmed',
            'image' => $rule_image ,
//            'role_id' => 'required|in:' . $this->whereIn()['Role'],
//            'team_id' => 'required|integer',
        ])->validate();

        $validation = Validator::make($this->state, [
            'gender' =>  'nullable|in:' . $this->whereIn()['GenderEnum'],
            'birthday' => 'nullable',
            'country' => 'nullable',
            'tags' => 'nullable',
            'languages' => 'nullable',
            'interests' => 'nullable',
            'category_id' => 'nullable|array',
            'category_id.*' => 'nullable|exists:categories,id',

            'description' => 'nullable|string|max:700',
            'user_type' => 'required|in:' . $this->whereIn()['UserDetailsTypeEnum'],
            'file' =>  $rule_file ,
        ])->validate();


        $validation_social_media = Validator::make($this->social_media, [
            '*.social_media_name' => 'required|string|max:100',
            '*.social_media_link' => 'required|url',
            '*.social_media_icon' => 'nullable|integer|exists:icons,id',
        ], [
            '*.social_media_name.required' => __('validation.attributes.social_media_name'),
            '*.social_media_link.required' => __('validation.attributes.social_media_link'),
            '*.social_media_icon.required' => __('validation.attributes.social_media_icon'),
        ])->validate();


        if(isset($validationUser['image']) && $validationUser['image'] != $this->UserDetails_->user?->avatar)
        {
            $image = $validationUser['image']->store('userDetails', config('filesystems.default'));
        }else{
            $image = $this->UserDetails_->user?->avatar;
        }


        if(isset($validation['file']) && $validation['file'] != $this->UserDetails_?->file)
        {
            $file = $validation['file']->store('userDetails', config('filesystems.default'));
        }else{
            $file = $this->UserDetails_->file;
        }

        if (isset($validationUser['password'])) {
            $validationUser['password'] = Hash::make($validationUser['password']);
            $this->UserDetails_->user->update([
                'first_name' => $validationUser['first_name'],
                'last_name' => $validationUser['last_name'],
                'email' => $validationUser['email'],
                'password' => $validationUser['password'],
                'avatar' =>  $image  ?? null,
            ]);
        } else {
            $this->UserDetails_->user?->update([
                'first_name' => $validationUser['first_name'],
                'last_name' => $validationUser['last_name'],
                'email' => $validationUser['email'],
                'avatar' =>   $image ?? null,
            ]);
        }

        $this->UserDetails_->update([
            'file' =>  $file?? null,
            'gender' => $validation['gender'] ?? null,
            'birthday' => $validation['birthday'] ?? null,
            'country' =>$validation['country'] ?? null,
            'description' => $validation['description'] ?? null,
            'user_type' => $validation['user_type'] ?? null,
        ]);

        $this->UserDetails_->userDetails_social_media()->forceDelete();
        if (!empty($validation_social_media)) {
            $this->UserDetails_->userDetails_social_media()->createMany($validation_social_media);
        }

        $this->UserDetails_->tags()->forceDelete();
        if (!empty($validation['tags'])) {
            $tags = explode(',', $validation['tags']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::USERS->value]);
                UserDetailRelation::create([
                    'user_detail_id' => $this->UserDetails_->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
        $this->UserDetails_->interests()->forceDelete();
        if (!empty($validation['interests'])) {
            $tags = explode(',', $validation['interests']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::INTEREST->value]);
                UserDetailRelation::create([
                    'user_detail_id' => $this->UserDetails_->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
        $this->UserDetails_->languages()->forceDelete();
        if (!empty($validation['languages'])) {
            $tags = explode(',', $validation['languages']);
            foreach ($tags as $key => $row) {
                $tag = Tag::firstOrCreate(['tag_name' => trim($row), 'tag_type' => TagTypeEnum::LANGUAGES->value]);
                UserDetailRelation::create([
                    'user_detail_id' => $this->UserDetails_->id,
                    'relationable_id' => $tag->id,
                    'relationable_type' => Tag::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
            }
        }
        $this->UserDetails_->categories()->forceDelete();
        if (!empty($validation['category_id'])) {
            foreach ($validation['category_id'] as $key => $row)
                UserDetailRelation::create([
                    'user_detail_id' => $this->UserDetails_->id,
                    'relationable_id' => $row,
                    'relationable_type' => Category::class,
                    'relationable_is_main' => $key == 0 ? 1 : 0,
                ]);
        }



        $this->UserDetails_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
        ]);

        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(UserDetails $userDetail): void
    {
        $this->UserDetails_ = $userDetail;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->UserDetails_->delete();
        $this->UserDetails_->userDetails_social_media()->forceDelete();
        $this->UserDetails_->user?->delete();

        $this->UserDetails_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::DELETE->value,
        ]);
        $this->dispatch('hide_delete');
    }


    public function userEditPendingConfirm(): void
    {

        if($this->UserDetails_->is_active == 0)
        {
            $this->UserDetails_->update([
                'is_active' => 1,
            ]);
        }elseif ($this->UserDetails_->is_active == 1)
        {
            $this->UserDetails_->update([
                'is_active' => 0,
            ]);
        }


        $this->UserDetails_->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => __('edit_account_status'),
        ]);

        $this->dispatch('hide_user_edit_active');

        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }




    #[Computed]
    public function icons()
    {
        return Icon::get();
    }

    public function iconSelect($key, Icon $icon): void
    {
        $this->social_media[$key]['social_media_icon'] = $icon->id;

    }

    public function addSocialMedia(): void
    {
        $this->social_media [] = [
            'social_media_name' => '',
            'social_media_link' => '',
            'social_media_icon' => ''
        ];
    }

    public function removeSocialMedia($index): void
    {
        unset($this->social_media[$index]);
    }

}
