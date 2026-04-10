<?php

namespace App\Livewire\Main\Tamkeen;

use App\Enums\CategoryTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Gallery extends Component
{

    public  $materials;
    public  $category_id;

    #[Layout('components.layouts.main.tamkeen.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.tamkeen.gallery');
    }


    #[Computed]
    public function categories(): Collection|array
    {

        return Category::query()
            ->whereIn('category_type', [CategoryTypeEnum::PODCAST->value,CategoryTypeEnum::VIDEO->value ,CategoryTypeEnum::IMAGE->value ])
            ->get();
    }

    public function mount(): void
    {
        $this->materials = Material::query()
            ->where('type', MaterialTypeEnum::IMAGE->value)
            ->with('files','category' , 'material_album')
            ->get();
        $this->category_id = 'all';

    }

    public function getMaterials( $category_id): void
    {
        $this->category_id = $category_id;

        if($category_id != 'all')
        {
            $this->materials = Material::query()->whereHas('category', function ($q)  use ( $category_id){
                $q->where('relationable_id', $category_id);

            })->with(['category.relationable' , 'files.file'])->get();

        }else
        {
            $this->materials = Material::query()
                ->where('type', MaterialTypeEnum::IMAGE->value)
                ->with('files','category' , 'material_album')
                ->get();
        }


    }
}
