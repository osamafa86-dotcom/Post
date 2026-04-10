<?php

namespace App\Livewire\Dashboard;

use App\Models\Team;
use App\Traits\GeneralView;
use App\Traits\WhereIn;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ListTeams extends Component
{
    use WithPagination, WhereIn;

    use GeneralView;

    public string $model_name = 'teams';

    protected $columns = [
        'name',
    ];

    // this function to collect model_form dropdown contents
    protected function initListsForFields(): void
    {
    }

    // this function to collect model_form tags contents
    protected function collectTags(): void
    {
    }
    // end general view

    #[Computed]
    public function items(): LengthAwarePaginator
    {
        return Team::orderBy($this->sortField, $this->sortDirection)->paginate(10);
    }

    /**
     * @throws ValidationException
     */
    public function create(): void
    {
        $validation = Validator::make($this->state, [
            'name' => 'required|string|max:30',
        ])->validate();

        Team::create($validation);

        $this->dispatch('hide_form', ['message' => __('validation.saved_success')]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        $validation = Validator::make($this->state, [
            'name' => 'required|string|max:30',
        ])->validate();

        $this->Item_->update($validation);

        $this->dispatch('hide_form', ['message' => __('validation.edit_success')]);
    }

}
