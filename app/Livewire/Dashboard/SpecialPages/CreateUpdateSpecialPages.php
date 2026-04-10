<?php

namespace App\Livewire\Dashboard\SpecialPages;

use App\Models\Files;
use App\Models\SpecialPage;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateUpdateSpecialPages extends Component
{
    use WithFileUploads;

    public array $state = [];
    public ?object $SpecialPage;
    public bool $showEdit = false;
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.special-pages.create-update-special-pages');
    }

    public function mount($special_page_id = null): void
    {
        if ($special_page_id) {
            $this->SpecialPage = SpecialPage::where('id', $special_page_id)->with('files')->first();
            $this->state = [
                "id" => $this->SpecialPage->id ?? null,
                "page_title" => $this->SpecialPage->page_title ?? null,
                "page_image" => $this->SpecialPage->files->file_id ?? null,
                "page_image_name" => isset($this->SpecialPage->files->file_id) ? Files::where('id', $this->SpecialPage->files->file_id)->value('path') : null,
                "page_content" => $this->SpecialPage->page_content ?? null,
            ];
            $this->showEdit = true;
        }
    }

    /**
     * @throws ValidationException
     */
    public function createSpecialPage(): void
    {
        $validation = Validator::make($this->state, [
            'page_title' => 'required|string|max:255|unique:special_pages',
            'page_image' => 'nullable|integer|exists:files,id',
            'page_content' => 'nullable',
        ])->validate();

        $validation['page_content'] = $this->state['page_content'] ?? null;


        $SpecialPage = SpecialPage::create([
            'page_title' => $validation['page_title'] ?? null,
            'page_content' => $validation['page_content'] ?? null,
            'user_id' => Auth::id(),
        ]);
        if (!empty($validation['page_image']) )
        {
            $SpecialPage->files()->create([
                'file_id' => $validation['page_image'] ?? null,
                'model_type' => SpecialPage::class,
                'model_column' => 'page_image'
            ]);
        }


        $SpecialPage->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->state = [];
        $this->dispatch('special_pageCreated');
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['page_image'] = $id;
        $this->state['page_image_name'] = Files::where('id', $id)->value('path');
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
    public function updateSpecialPage(): void
    {
        $validation = Validator::make($this->state, [
            'page_title' => 'required|string|max:255|unique:special_pages,page_title,' . $this->SpecialPage->id,
            'page_image' => 'nullable|integer|exists:files,id',
            'page_content' => 'nullable',
        ])->validate();

        $validation['page_content'] = $this->state['page_content'] ?? null;

        $before_data = $this->SpecialPage->toArray();

        $this->SpecialPage->update([
            'page_title' => $validation['page_title'] ?? null,
            'page_content' => $validation['page_content'] ?? null,
        ]);
        $this->SpecialPage->files()->update([
            'file_id' => $validation['page_image'] ?? null,
        ]);

        $new_data = $this->SpecialPage->toArray();

        $this->SpecialPage->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->state = [];
        $this->dispatch('special_pageCreated');
    }
}
