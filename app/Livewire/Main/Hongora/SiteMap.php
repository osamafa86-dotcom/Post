<?php

namespace App\Livewire\Main\Hongora;

use App\Enums\CategoryTypeEnum;
use App\Models\Author;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SiteMap extends Component
{
    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $tags = Tag::all()->select('id', 'tag_name');
        $categories = Category::where('category_type', CategoryTypeEnum::NEWS->value)->select('id', 'category_title')->get();
        $authors = Author::all()->select('id', 'author_name');
        return view('livewire.main.hongora.site-map', [
            'tags' => $tags,
            'categories' => $categories,
            'authors' => $authors
        ]);
    }
}
