<?php

namespace App\Livewire\Main\Hongora;

use App\Enums\CategoryTypeEnum;
use App\Enums\DateTypeEnum;
use App\Enums\MaterialTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\UserDetailsTypeEnum;
use App\Models\Category;
use App\Models\Event;
use App\Models\Material;
use App\Models\MaterialAlbum;
use App\Models\NewsLetterEmails;
use App\Models\Participant;
use App\Models\Post;
use App\Models\Service;
use App\Models\SocialMedia;
use App\Models\UserDetails;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class IndexPage extends Component
{

    public int $listenersCount = 0 ,
               $contentProviders = 0,
               $likes = 0,
               $materialCount =0;

    public function mount()
    {
        $podcastType = MaterialTypeEnum::PODCAST->value;
        $podcastStats = Material::where('materials.type', $podcastType)
            ->leftJoin('views', function ($join) {
                $join->on('materials.id', '=', 'views.viewable_id')
                    ->where('views.viewable_type', Material::class);
            })
            ->selectRaw('COUNT(DISTINCT materials.id) as total_count')
            ->selectRaw('COUNT(views.id) as total_views')
            ->selectRaw('SUM(materials.like) as total_likes')
            ->first();
        $this->listenersCount = (int) $podcastStats->total_views ?? 0;
        $this->likes = (int) $podcastStats->total_likes ?? 0;
        $this->materialCount = (int) $podcastStats->total_count ?? 0;
        $this->contentProviders = UserDetails::where('user_type', UserDetailsTypeEnum::CONTENT_CREATOR->value)
            ->count();
    }

    #[Computed]
    public function services()
    {
        return Service::where('is_show', 1)
            ->orderByDesc('created_at', 'desc')
            ->with(['files.file'])
            ->take(3)
            ->get();
    }

    #[Computed]
    public function podcasts()
    {
        return Material::where('type', MaterialTypeEnum::PODCAST->value)
            ->orderBy('like', 'desc')
            ->with(['files.file','category.relationable','presenter.relationable'])
            ->take(8)
            ->get();
    }

//    #[Computed]
//    public function events()
//    {
//         return Event::query()
//            //->where('date_type', DateTypeEnum::TIME->value)
////            ->whereHas('event_dates', function ($q) {
////                $q->where('day', Carbon::now()->locale('ar')->translatedFormat('l'));
////            })
//            ->orderByDesc('created_at', 'desc')
//            ->with('presenter', 'event_dates')
//            ->get();
//    }

    #[Computed]
    public function partners()
    {
        return Participant::with(['files.file'])->where('type', ParticipantTypeEnum::PARTNERS->value)->limit(15)->get();
    }

    #[Computed]
    public function team()
    {
        return UserDetails::where('user_type', UserDetailsTypeEnum::HONGORA_TEAM->value)->with(['user','userDetails_social_media'])->get();
    }

    #[Computed]
    public function clients()
    {
        return Participant::with(['files.file'])->where('type', ParticipantTypeEnum::CLIENTS->value)->limit(5)->get();
    }



    #[Layout('components.layouts.main.hongora.main')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {

        return view('livewire.main.hongora.index-page');
    }
}
