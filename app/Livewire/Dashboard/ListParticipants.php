<?php

namespace App\Livewire\Dashboard;

use App\Enums\ParticipantTypeEnum;
use App\Models\Files;
use App\Models\Icon;
use App\Models\MaterialRelation;
use App\Models\Participant;
use App\Models\PostRelation;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListParticipants extends Component
{
    use WithPagination, WhereIn, WithFileUploads;

    protected string $paginationTheme = 'bootstrap';

    public ?string $search = "";
    public object $Participant_;
    public array $state = [];
    public array $social_media = [];
    public bool $showEdit = false;
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public $selected = [];
    public $urls = [];
    public $selectAll = false;
    public $delete_text;


    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-participants');
    }

    public function mount($add = null): void
    {

        /*        $participant = Participant::all();
                foreach ($participant as $p) {
                    if (!empty($p->image)){
                        $headers = get_headers(file_url($p->image), 1);
                        $size = isset($headers['content-length']) ? (int)$headers['content-length'] : null;
                        $array = [
                            'path' => $p->image,
                            'original_name' => explode('/', $p->image)[count(explode('/', $p->image)) - 1],
                            'file_name' => explode('/', $p->image)[count(explode('/', $p->image)) - 1],
                            'size' => $size,
                            'extension' => pathinfo($p->image, PATHINFO_EXTENSION),
                        ];
                        $file = Files::query()->create($array);
                        $p->files()->create([
                            'file_id' => $file->id,
                            'model_type' => Participant::class,
                            'model_column' => 'image',
                        ]);
                        $p->update([
                            'image' => null,
                        ]);
                    }
                }
*/

        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
    }

    #[Computed]
    public function participants(): LengthAwarePaginator
    {
        return Participant::when(isset($this->search), function (Builder $query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('work', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })
            ->with(['files.file'])->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
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

    public function edit(Participant $participant): void
    {
        $participant->load(['participant_social_media', 'files.file']);
        $this->state = [];
        $this->Participant_ = $participant;
        $this->state = $participant->toArray();
        $this->state['url'] = $participant->participants_data['url'] ?? null;
        $this->social_media = $participant->participant_social_media->toArray();
        if (isset($this->Participant_?->files?->file_id)) {
            $this->state['image'] = $this->Participant_?->files?->file_id;
            $this->state['image_name'] = $this->Participant_?->files?->file?->path;
        }
        $this->showEdit = true;
        $this->dispatch('show_form');
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['image'] = $id;
        $this->state['image_name'] = Files::where('id', $id)->value('path');

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

    /**
     * @throws ValidationException
     */
    public function create(): void
    {
        $validationRules = [
            'name' => 'required|string|max:100',
            'image' => 'nullable|integer|exists:files,id',
            'work' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:' . $this->whereIn()['ParticipantTypeEnum'],
        ];

        $validation = Validator::make($this->state, $validationRules)->validate();

        $validation_social_media = Validator::make($this->social_media, [
            '*.social_media_name' => 'required|string|max:100',
            '*.social_media_link' => 'required|url',
            '*.social_media_icon' => 'required|integer|exists:icons,id',
        ], [
            '*.social_media_name.required' => __('validation.attributes.social_media_name'),
            '*.social_media_link.required' => __('validation.attributes.social_media_link'),
            '*.social_media_icon.required' => __('validation.attributes.social_media_icon'),
        ])->validate();

        if (in_array($this->state['type'] ?? '', [
            \App\Enums\ParticipantTypeEnum::PUBLISHERS->value,
            \App\Enums\ParticipantTypeEnum::RESOURCES->value,
        ])) {
            $urlValidation = Validator::make($this->state, [
                'url' => 'required|url|max:255',
            ])->validate();

            $validation['participants_data'] = [
                'url' => $urlValidation['url'],
            ];
        }

        $image = $validation['image'] ?? null;
        unset($validation['image']);

        $participant = Participant::query()->create($validation);
        if (!empty($validation_social_media)) {
            $participant->participant_social_media()->createMany($validation_social_media);
        }
        if (!empty($image)) {
            $participant->files()->create([
                'file_id' => $image ?? null,
                'model_type' => Participant::class,
                'model_column' => 'image',
            ]);
        }

        $this->dispatch('hide_form', ['message' => __('validation.saved_success')]);
    }


    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        $validation = Validator::make($this->state, [
            'name' => 'required|string|max:100',
            'image' => 'required|integer|exists:files,id',
            'work' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:' . $this->whereIn()['ParticipantTypeEnum'],
            'url' => 'nullable|url',
        ])->validate();

        $validation_social_media = Validator::make($this->social_media, [
            '*.social_media_name' => 'required|string|max:100',
            '*.social_media_link' => 'required|url',
            '*.social_media_icon' => 'required|integer|exists:icons,id',
        ], [
            '*.social_media_name.required' => __('validation.attributes.social_media_name'),
            '*.social_media_link.required' => __('validation.attributes.social_media_link'),
            '*.social_media_icon.required' => __('validation.attributes.social_media_icon'),
        ])->validate();

        $image = $validation['image'] ?? null;
        $url = $validation['url'] ?? null;
        unset($validation['image']);
        unset($validation['url']);
        $this->Participant_->update($validation);
        if (!empty($url)) {
            $this->Participant_->update([
                'participants_data' => ['url' => $url]
            ]);
        }
        $this->Participant_->participant_social_media()->forceDelete();
        if (!empty($validation_social_media)) {
            $this->Participant_->participant_social_media()->createMany($validation_social_media);
        }

        if (!empty($image)) {
            $this->Participant_->files()->updateOrCreate(
                ['model_type' => Participant::class, 'model_column' => 'image'],
                ['file_id' => $image ?? null,]
            );
        }
        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

    public function delete(Participant $participant): void
    {
        $this->Participant_ = $participant;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        PostRelation::where([['relationable_id', $this->Participant_->id], ['relationable_type', Participant::class]])->delete();
        MaterialRelation::where([['relationable_id', $this->Participant_->id], ['relationable_type', Participant::class]])->delete();
        $this->Participant_->participant_social_media()->forceDelete();
        $this->Participant_->delete();

        $this->dispatch('hide_delete');
        $this->resetSelection();

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


    public function updatedSelectAll($value): void
    {
        $Ids = collect($this->participants->items())->pluck('id')->all();
        if ($value) {
            $this->selected = array_unique(array_merge($this->selected, $Ids));
        } else {
            $this->selected = array_diff($this->selected, $Ids);
        }
        $this->checkSelectAll();
    }


    public function updatedSelected(): void
    {
        $this->checkSelectAll();
    }


    public function updatedPage(): void
    {
        $this->selectAll = false;
        $this->checkSelectAll();
    }

    private function checkSelectAll(): void
    {
        $Ids = collect($this->participants->items())->pluck('id')->all();
        $this->selectAll = !array_diff($Ids, $this->selected);
    }

    public function deleteSelected(): void
    {
        if (!empty($this->selected)) {
            $this->dispatch('show_delete_selected_modal');
        } else {
            $this->dispatch('no-data');
        }
    }

    public function confirmDeleteSelected(): void
    {
        if ($this->delete_text) {
            if ($this->delete_text == 'Delete') {
                $items = Participant::whereIn('id', $this->selected)->get();

                $this->selected = [];
                $this->selectAll = false;
                $this->delete_text = '';

                foreach ($items as $item) {
                    PostRelation::where([['relationable_id', $item->id], ['relationable_type', Participant::class]])->delete();
                    MaterialRelation::where([['relationable_id', $item->id], ['relationable_type', Participant::class]])->delete();
                    $item->delete();


                }


                $this->dispatch('hide_delete_selected_modal');
                $this->resetPage();
                $this->resetSelection();

            } else {
                session()->flash('error', __('messages.error_delete_text'));
            }
        } else {
            session()->flash('error', __('messages.empty_delete_text'));
        }
    }

    private function resetSelection()
    {
        $this->selectAll = false;
        $this->selected = [];
    }
}
