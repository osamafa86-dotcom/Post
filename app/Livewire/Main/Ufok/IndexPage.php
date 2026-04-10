<?php

namespace App\Livewire\Main\Ufok;
use App\Enums\CategoryTypeEnum;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventRelation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
class IndexPage extends Component
{

    public  $events;
    public $selectedWeek = 0;
    #[Layout('components.layouts.main.ufok.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.main.ufok.index-page');
    }


    public function mount()
    {
        $this->getEvents(0);

    }

    public function selectDate($week)
    {
        $this->selectedWeek = $week;
        $this->getEvents($week);
    }
    public function getEvents($week)
    {
        $daysRange = [
            [0, 7],    // الأسبوع الأول
            [8, 14],   // الأسبوع الثاني
            [15, 21],  // الأسبوع الثالث
            [22, 28]   // الأسبوع الرابع
        ];

        if (!isset($daysRange[$week])) {
            return;
        }

        [$start, $end] = $daysRange[$week];

        $this->events = Event::whereBetween('created_at', [
            Carbon::now()->addDays($start)->startOfDay(),
            Carbon::now()->addDays($end)->endOfDay()
        ])->with(['files', 'event_dates'])->latest()->get();

        // إرسال إشارة إلى JavaScript لإعادة تحميل `Swiper`,`Owl Carousel`
        $this->dispatch('reloadOwlCarousel');
        $this->dispatch('reloadSwiper');
    }

    #[Computed]
    public function categories_event()
    {


        return Category::with(['event_relation.event'])
            ->get()
            ->map(function ($category) {
                return $category->event_relation->first()?->event;
            })
            ->filter();


    }


}
