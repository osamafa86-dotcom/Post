<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\UserDetails;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;

trait GeneralView
{

    protected string $paginationTheme = 'bootstrap';
    public ?string $search = "";
    public object $Item_;
    public array $state = [];
    public bool $showEdit = false;
    public string $sortField = "id";
    public string $sortDirection = "asc";
    public array $listsForFields = [];
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.general-list');
    }

    public function mount($add = null): void
    {
        $this->initListsForFields();
        if(isset($this->publishable)){
            $this->check_table($this->table_name);
        }

        if (isset($add) && $add == 'addNew') {
            $this->addNew();
        }
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


    public function edit(Team $item): void
    {
        $this->state = [];
        $this->Item_ = $item;
        $this->state = $item->toArray();
        $this->collectTags();
        $this->showEdit = true;
        $this->dispatch('show_form');
    }

    public function addNew(): void
    {
        $this->showEdit = false;
        $this->state = [];
        $this->dispatch('show_form');
    }

    public function delete(UserDetails $item): void
    {
        $this->Item_ = $item;
        $this->dispatch('show_delete');
    }

    public function deleteConfirm(): void
    {
        $this->Item_->delete();
        $this->dispatch('hide_delete');
    }

}
