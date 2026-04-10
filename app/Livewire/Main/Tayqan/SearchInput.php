<?php

namespace App\Livewire\Main\Tayqan;

use App\Models\Category;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SearchInput extends Component
{

    public $search_text;
    public $search_category;
    public $search_type;
    public $from_date;
    public $to_date;

    #[Layout('components.layouts.main.tayqan.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tayqan.search-input');
    }


    public function getSearchPage()
    {
        return redirect()->route('main.tayqan.search_page', [
            'search' => $this->search_text,
            'search_category' => $this->search_category,
            'search_type' => $this->search_type,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
        ]);

    }
    #[Computed]
    public function categories()
    {
        return Category::whereNotNull('category_title')
            ->pluck('category_title')
            ->unique()
            ->filter()
            ->values();
    }
    #[Computed]
    public function types()
    {

        return \App\Models\Type::whereNotNull('type_name')
            ->where('show_index',true)
            ->orderBy('order')
            ->pluck('type_name')
            ->unique()
            ->filter()
            ->values();
    }
}
