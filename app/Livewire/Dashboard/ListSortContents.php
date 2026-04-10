<?php

namespace App\Livewire\Dashboard;

use App\Models\Material;
use App\Models\Post;
use App\Models\Quote;
use App\Models\SortData;
use App\Models\SpecialFile;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use function Laravel\Prompts\select;

//use Livewire\WithSortable;

class ListSortContents extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';
    public array $state = [];

    public function updateUserOrder($sortedItems): void
    {
        // احصل على أعلى قيمة order_number من الجدول
        $maxOrderNumber = SortData::max('order_number');

        foreach ($sortedItems as $index => $item) {
            SortData::find($item['value'])
                ->update(['order_number' => $maxOrderNumber - $index]);
        }

        // تحديث الحالة الظاهرة للمستخدم
        foreach ($this->sort_data as $sort) {
            $this->state[$sort['id']] = $sort['order_number'];
        }

        $this->dispatch('navbar_links_updated');
    }

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        //        $fix = SortData::query()
        //            ->orderBy('created_at', 'asc')
        //            ->with('sortable')
        //            ->get();
        //        $total = 1;
        //        foreach ($fix as $row) {
        //            $row->update(['order_number' => $total]);
        //            $total++;
        //        }
        return view('livewire.dashboard.list-sort-contents');
    }

    public function mount(): void
    {
        foreach ($this->sort_data as $sort) {
            $this->state[$sort['id']] = $sort['order_number'];
        }
    }

    #[Computed]
    public function sort_data()
    {
        return SortData::query()
            ->orderBy('order_number', 'desc')
            ->with('sortable')
            ->take(50)
            ->get()
            ->map(function ($sort) {
                return [
                    'id' => $sort->id,
                    'order_number' => $sort->order_number,
                    'display_text' => $sort->sortable_type === 'App\Models\Quote' ? Str::limit($sort->sortable?->quote_text, 50) : Str::limit($sort->sortable?->title, 50),
                    'type' => $this->getType($sort->sortable_type),
                    'created_at' => Carbon::parse($sort->created_at)->format('Y-m-d H:i'),
                ];
            });
    }

    public function getType($type): string
    {
        return match ($type) {
            Quote::class => __('messages.models.Quote'),
            Post::class => __('messages.models.Post'),
            Material::class => __('messages.models.Material'),
            default => __('messages.default'),
        };
    }
}
