<?php

namespace App\Livewire\Dashboard;

use App\Enums\MaterialTypeEnum;
use App\Enums\ParticipantTypeEnum;
use App\Enums\PublishEnum;
use App\Models\BreakingNews;
use App\Models\Event;
use App\Models\Files;
use App\Models\Material;
use App\Models\MaterialRelation;
use App\Models\Participant;
use App\Models\Post;
use App\Models\PostRelation;
use App\Models\UserLog;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

// ⬅️ مهم
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MainDashboard extends Component
{
    public $postNumber, $title, $url, $hide_date, $showCustomFilter = false;

    public $postsFilter = 'daily';
    public $totalVisitorsFilter = 'daily';
    public $draftPostsFilter = 'daily';
    public $fileUploadedFilter = 'daily';
    public $postViewsFilter = 'daily';
    public $participantsFilter = 'daily';
    public $platformUsageChartData = [];
    public $platformUsageFilter = 'daily'; // daily, weekly, monthly, yearly

    public $trafficSourcesChartFilter = 'daily';

    public $from_date, $to_date, $customFromDate, $customToDate;

    public $postViewsChartFilter = 'daily';
    public $visitorsChartFilter = 'daily';
    public $visitorsTypeChartFilter = 'daily';
    public $visitorsMapFilter = 'daily';

    #[Validate('date|before:postViewsChartDateEndFilter')] public $postViewsChartDateBeginFilter;
    #[Validate('date|after:postViewsChartDateBeginFilter')] public $postViewsChartDateEndFilter;
    #[Validate('date|before:visitorsChartDateEndFilter')] public $visitorsChartDateBeginFilter;
    #[Validate('date|after:visitorsChartDateBeginFilter')] public $visitorsChartDateEndFilter;
    #[Validate('date|before:trafficSourcesChartDateEndFilter')] public $trafficSourcesChartDateBeginFilter;
    #[Validate('date|after:trafficSourcesChartDateBeginFilter')] public $trafficSourcesChartDateEndFilter;
    #[Validate('date|before:visitorsTypeChartDateEndFilter')] public $visitorsTypeChartDateBeginFilter;
    #[Validate('date|after:visitorsTypeChartDateBeginFilter')] public $visitorsTypeChartDateEndFilter;
    #[Validate('date|before:visitorsMapDateEndFilter')] public $visitorsMapDateBeginFilter;
    #[Validate('date|after:visitorsMapDateBeginFilter')] public $visitorsMapDateEndFilter;

    public $changePercentage, $changePercentagedraft, $changePercentageviews,
        $changePercentageParticipants, $changePercentageVisitors, $changePercentageUploaded;

    public $chartsLoaded = false;

    // TTL للكاش (ثواني) – عدّله حسب رغبتك
    protected $cacheTtl = 600; // 10 minutes for better performance

    public function mount(): void
    {
        // KPI data loaded via computed properties when blade accesses them
        // Charts deferred to loadCharts() via wire:init for faster initial render
    }

    public function loadCharts(): void
    {
        $this->updatePlatformUsageChartData();
        $this->dispatch('updateVisitorsChart', $this->visitorsChart);
        $this->dispatch('updatePostViewsChart', $this->postViewsChart);
        $this->dispatch('updateTrafficSourcesChart', $this->trafficSourcesChart);
        $this->dispatch('updateDeviceUsageChart', $this->visitorsTypeChart);
        $this->dispatch('updateVisitorsMap', $this->visitorsMapData);
        $this->chartsLoaded = true;
    }

    public function updatedPlatformUsageFilter()
    {
        $this->updatePlatformUsageChartData();
        $this->dispatch('platformUsageChartUpdated', $this->platformUsageChartData);
    }

    public function updatePlatformUsageChartData()
    {
        $endDate = now();

        switch ($this->platformUsageFilter) {
            case 'daily':
                $startDate = now()->subDay();
                break;
            case 'weekly':
                $startDate = now()->subWeek();
                break;
            case 'monthly':
                $startDate = now()->subMonth();
                break;
            case 'yearly':
                $startDate = now()->subYear();
                break;
            default:
                $startDate = now()->subWeek();
        }

        // Get platform usage data from visitors table - cached
        $cacheKey = 'md:platformUsage:' . $this->platformUsageFilter . ':' . now()->format('Y-m-d');
        $platformData = Cache::remember($cacheKey, $this->cacheTtl, function () use ($startDate, $endDate) {
            return DB::table('visitor_logs')
                ->select('device_type', DB::raw('COUNT(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('device_type')
                ->pluck('count', 'device_type');
        });

        // Default platforms to track - ensure these always exist in the output
        $defaultPlatforms = ['Windows', 'Mac OS', 'Linux', 'Android', 'iOS', 'Unknown'];

        // Prepare datasets
        $labels = [];
        $data = [];
        $backgroundColor = [
            'rgba(54, 162, 235, 0.7)', // Windows (blue)
            'rgba(153, 102, 255, 0.7)', // Mac OS (purple)
            'rgba(75, 192, 192, 0.7)', // Linux (teal)
            'rgba(255, 205, 86, 0.7)', // Android (yellow)
            'rgba(255, 99, 132, 0.7)', // iOS (pink)
            'rgba(201, 203, 207, 0.7)' // Unknown (gray)
        ];

        // Fill in the data
        foreach ($defaultPlatforms as $index => $platform) {
            $labels[] = $platform;
            $data[] = (int)($platformData[$platform] ?? 0);
        }

        // Add any other platforms not in the default list
        foreach ($platformData as $platform => $count) {
            if (!in_array($platform, $defaultPlatforms, true)) {
                $labels[] = $platform;
                $data[] = (int)$count;
                $backgroundColor[] = 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ', 0.7)';
            }
        }

        // Format the chart data
        $this->platformUsageChartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('messages.platform_usage'),
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => 'rgba(255, 255, 255, 0.8)',
                    'borderWidth' => 1
                ]
            ]
        ];

        // Dispatch event to update the chart
        $this->dispatch('platformUsageChartUpdated', $this->platformUsageChartData);
    }

    public function updateShowCustomFilter()
    {
        $this->showCustomFilter = !$this->showCustomFilter;
    }

    #[Computed]
    public function mostUsedTags()
    {
        return Cache::remember('md:mostUsedTags', 1800, function () {
            return PostRelation::where('relationable_type', 'App\Models\Tag')
                ->whereNotNull('relationable_id')
                ->select('relationable_id', DB::raw('COUNT(*) as usage_count'))
                ->groupBy('relationable_id')
                ->orderByDesc('usage_count')
                ->with(['tags:id,tag_name'])
                ->take(20)
                ->get();
        });
    }
    #[Computed]
    public function materialMostUsedTags()
    {
        return Cache::remember('md:materialMostUsedTags', 1800, function () {
            return MaterialRelation::where('relationable_type', 'App\Models\Tag')
                ->whereNotNull('relationable_id')
                ->select('relationable_id', DB::raw('COUNT(*) as usage_count'))
                ->groupBy('relationable_id')
                ->orderByDesc('usage_count')
                ->with(['tags:id,tag_name'])
                ->take(20)
                ->get();
        });
    }

    #[Computed]
    public function mostUsedCategories()
    {
        return Cache::remember('md:mostUsedCategories', 1800, function () {
            return PostRelation::where('relationable_type', 'App\Models\Category')
                ->whereNotNull('relationable_id')
                ->select('relationable_id', DB::raw('COUNT(*) as usage_count'))
                ->groupBy('relationable_id')
                ->orderByDesc('usage_count')
                ->with(['categories:id,category_title'])
                ->take(20)
                ->get();
        });
    }

    #[Computed]
    public function matreialMostUsedCategories()
    {
        return Cache::remember('md:matreialMostUsedCategories', 1800, function () {
            return MaterialRelation::where('relationable_type', 'App\Models\Category')
                ->whereNotNull('relationable_id')
                ->select('relationable_id', DB::raw('COUNT(*) as usage_count'))
                ->groupBy('relationable_id')
                ->orderByDesc('usage_count')
                ->with(['categories:id,category_title'])
                ->take(20)
                ->get();
        });
    }

    public function quickFilter($type = 'daily')
    {
        // Batch update all filters
        $filters = [
            'postsFilter', 'draftPostsFilter', 'fileUploadedFilter',
            'postViewsFilter', 'totalVisitorsFilter', 'participantsFilter',
            'postViewsChartFilter', 'visitorsChartFilter', 'trafficSourcesChartFilter',
            'platformUsageFilter', 'visitorsTypeChartFilter', 'visitorsMapFilter'
        ];
        
        foreach ($filters as $filter) {
            $this->$filter = $type;
        }

        if ($type === 'custom') {
            // نطاق مخصص
            $this->from_date = $this->customFromDate;
            $this->to_date = $this->customToDate;

            // مرّر التواريخ للرسوم
            $this->postViewsChartDateBeginFilter = $this->visitorsChartDateBeginFilter =
            $this->trafficSourcesChartDateBeginFilter = $this->visitorsTypeChartDateBeginFilter = 
            $this->visitorsMapDateBeginFilter = $this->customFromDate;

            $this->postViewsChartDateEndFilter = $this->visitorsChartDateEndFilter =
            $this->trafficSourcesChartDateEndFilter = $this->visitorsTypeChartDateEndFilter = 
            $this->visitorsMapDateEndFilter = $this->customToDate;

            $this->showCustomFilter = true;
        } else {
            // Reset custom dates for non-custom filters
            $this->postViewsChartDateBeginFilter = $this->postViewsChartDateEndFilter =
            $this->visitorsChartDateBeginFilter = $this->visitorsChartDateEndFilter =
            $this->trafficSourcesChartDateBeginFilter = $this->trafficSourcesChartDateEndFilter =
            $this->visitorsTypeChartDateBeginFilter = $this->visitorsTypeChartDateEndFilter =
            $this->visitorsMapDateBeginFilter = $this->visitorsMapDateEndFilter = null;

            $this->from_date = $this->to_date = null;
            $this->showCustomFilter = false;
        }

        // Clear computed property cache (KPIs + Charts)
        unset(
            $this->postsCount,
            $this->totalVisitors,
            $this->draftPostsCount,
            $this->postViewsCount,
            $this->totalParticipants,
            $this->fileUploadedCount,
            $this->visitorsChart,
            $this->postViewsChart,
            $this->trafficSourcesChart,
            $this->visitorsTypeChart,
            $this->visitorsMapData
        );

        // Defer chart loading to a separate Livewire request
        // This way KPIs render first (fast), then charts load after
        $this->chartsLoaded = false;
        $this->js('setTimeout(() => { $wire.loadCharts() }, 50)');
    }


    protected function getDateRanges($filter): array
    {
        $now = Carbon::now();

        return match ($filter) {
            'daily' => (function () use ($now) {
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $pStart = $start->copy()->subDay();
                $pEnd = $end->copy()->subDay();
                return [$start, $end, $pStart, $pEnd];
            })(),

            'weekly' => (function () use ($now) {
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $pStart = $start->copy()->subWeek();
                $pEnd = $end->copy()->subWeek();
                return [$start, $end, $pStart, $pEnd];
            })(),

            'monthly' => (function () use ($now) {
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $pStart = $start->copy()->subMonthNoOverflow();
                $pEnd = $end->copy()->subMonthNoOverflow();
                return [$start, $end, $pStart, $pEnd];
            })(),

            'yearly' => (function () use ($now) {
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $pStart = $start->copy()->subYear();
                $pEnd = $end->copy()->subYear();
                return [$start, $end, $pStart, $pEnd];
            })(),

            'custom' => (function () use ($now) {
                $start = $this->from_date ? Carbon::parse($this->from_date)->startOfDay() : $now->copy()->startOfDay();
                $end = $this->to_date ? Carbon::parse($this->to_date)->endOfDay() : $now->copy()->endOfDay();

                // في حال المستخدم عكس التواريخ
                if ($end->lessThan($start)) {
                    [$start, $end] = [$end, $start];
                }

                // الفترة السابقة بنفس المدة تمامًا
                $seconds = $start->diffInSeconds($end) + 1; // شامل للطرفين
                $pStart = $start->copy()->subSeconds($seconds);
                $pEnd = $end->copy()->subSeconds($seconds);

                return [$start, $end, $pStart, $pEnd];
            })(),

            default => (function () use ($now) { // daily
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $pStart = $start->copy()->subDay();
                $pEnd = $end->copy()->subDay();
                return [$start, $end, $pStart, $pEnd];
            })(),
        };
    }


    protected function getPercentageChange(int $current, int $previous, int $max = 100): float
    {
        if ($previous === 0) return $current > 0 ? $max : 0;
        $percent = (($current - $previous) / $previous) * 100;
        return min($percent, $max);
    }

    #[Computed]
    public function postsCount()
    {
        [$sC, $eC, $sP, $eP] = $this->getDateRanges($this->postsFilter);
        $key = 'md:postsCount:' . $this->postsFilter . ':' . ($this->from_date ?? '-') . ':' . ($this->to_date ?? '-');

        $data = Cache::remember($key, $this->cacheTtl, function () use ($sC, $eC, $sP, $eP) {
            // Posts - optimized with single query
            $postCounts = DB::table('posts')
                ->select(DB::raw("
                    SUM(CASE WHEN publish_status = ? AND publish_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as current,
                    SUM(CASE WHEN publish_status = ? AND publish_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as previous
                "))
                ->whereNotNull('publish_date')
                ->addBinding([PublishEnum::PUBLISHED->value, $sC, $eC, PublishEnum::PUBLISHED->value, $sP, $eP], 'select')
                ->first();

            // Materials
            $currentMaterials = DB::table('materials')
                ->whereBetween('created_at', [$sC, $eC])
                ->selectRaw('type, COUNT(*) as cnt')
                ->groupBy('type')->pluck('cnt', 'type');

            $previous_material = DB::table('materials')->whereBetween('created_at', [$sP, $eP])->count();

            // Events
            $eventCounts = DB::table('events')
                ->selectRaw("
                    SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as current,
                    SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as previous
                ", [$sC, $eC, $sP, $eP])
                ->first();

            $podcasts = (int)($currentMaterials[MaterialTypeEnum::PODCAST->value] ?? 0);
            $videos = (int)($currentMaterials[MaterialTypeEnum::VIDEO->value] ?? 0);

            $current = (int)($postCounts->current ?? 0) + array_sum($currentMaterials->toArray()) + (int)($eventCounts->current ?? 0);
            $previous = (int)($postCounts->previous ?? 0) + (int)$previous_material + (int)($eventCounts->previous ?? 0);

            return [
                'current' => $current,
                'posts' => (int)($postCounts->current ?? 0),
                'podcasts' => $podcasts,
                'videos' => $videos,
                'events' => (int)($eventCounts->current ?? 0),
                'prev' => $previous,
            ];
        });

        $this->changePercentage = $this->getPercentageChange($data['current'], $data['prev']);

        return [
            'current' => $data['current'],
            'posts' => $data['posts'],
            'podcasts' => $data['podcasts'],
            'videos' => $data['videos'],
            'events' => $data['events'],
        ];
    }

    public function updatedPostsFilter(): void
    {
        $this->postsCount();
    }

    #[Computed]
    public function totalVisitors()
    {
        [$sC, $eC, $sP, $eP] = $this->getDateRanges($this->totalVisitorsFilter);
        $key = 'md:totalVisitors:' . $this->totalVisitorsFilter . ':' . ($this->from_date ?? '-') . ':' . ($this->to_date ?? '-');

        $res = Cache::remember($key, $this->cacheTtl, function () use ($sC, $eC, $sP, $eP) {
            $row = DB::table('visitor_logs')
                ->select(DB::raw("
                    COUNT(CASE WHEN visited_at BETWEEN ? AND ? THEN 1 END) as current,
                    COUNT(CASE WHEN visited_at BETWEEN ? AND ? THEN 1 END) as previous
                "))
                ->addBinding([$sC, $eC, $sP, $eP], 'select')
                ->first();
            return [(int)($row->current ?? 0), (int)($row->previous ?? 0)];
        });

        $this->changePercentageVisitors = $this->getPercentageChange($res[0], $res[1]);

        return $res[0];
    }

    public function updatedTotalVisitorsFilter(): void
    {
        $this->totalVisitors();
    }

    #[Computed]
    public function draftPostsCount()
    {
        [$sC, $eC, $sP, $eP] = $this->getDateRanges($this->draftPostsFilter);
        $key = 'md:drafts:' . $this->draftPostsFilter . ':' . ($this->from_date ?? '-') . ':' . ($this->to_date ?? '-');

        $res = Cache::remember($key, $this->cacheTtl, function () use ($sC, $eC, $sP, $eP) {
            $row = DB::table('posts')
                ->select(DB::raw("
                    COUNT(CASE WHEN publish_status = ? AND publish_date BETWEEN ? AND ? THEN 1 END) AS current,
                    COUNT(CASE WHEN publish_status = ? AND publish_date BETWEEN ? AND ? THEN 1 END) AS previous
                "))
                ->whereNotNull('publish_date')
                ->addBinding([PublishEnum::DRAFT->value, $sC, $eC, PublishEnum::DRAFT->value, $sP, $eP], 'select')
                ->first();
            return [(int)($row->current ?? 0), (int)($row->previous ?? 0)];
        });

        $this->changePercentagedraft = $this->getPercentageChange($res[0], $res[1]);

        return $res[0];
    }

    public function updatedDraftPostsFilter(): void
    {
        $this->draftPostsCount();
    }

    #[Computed]
    public function postViewsCount()
    {
        [$sC, $eC, $sP, $eP] = $this->getDateRanges($this->postViewsFilter);
        $key = 'md:postViewsCount:' . $this->postViewsFilter . ':' . ($this->from_date ?? '-') . ':' . ($this->to_date ?? '-');

        $data = Cache::remember($key, $this->cacheTtl, function () use ($sC, $eC, $sP, $eP) {
            $row = DB::table('views')
                ->select(DB::raw("
                    SUM(CASE WHEN viewable_type = ? AND created_at BETWEEN ? AND ? THEN views_number ELSE 0 END) AS current,
                    SUM(CASE WHEN viewable_type = ? AND created_at BETWEEN ? AND ? THEN views_number ELSE 0 END) AS previous
                "))
                ->addBinding([Post::class, $sC, $eC, Post::class, $sP, $eP], 'select')
                ->first();

            $postCurrent = (int)($row->current ?? 0);
            $postPrevious = (int)($row->previous ?? 0);

            // مكان إضافي لدمج بودكاست/فيديو/صورة لاحقاً (بنفس الآلية)
            $cPodcast = 0;
            $pPodcast = 0;
            $cVideo = 0;
            $pVideo = 0;
            $cImage = 0;
            $pImage = 0;

            return [
                'curr' => $postCurrent + $cPodcast + $cVideo + $cImage,
                'prev' => $postPrevious + $pPodcast + $pVideo + $pImage,
                'post' => $postCurrent, 'pod' => $cPodcast, 'vid' => $cVideo, 'img' => $cImage
            ];
        });

        $this->changePercentageviews = $this->getPercentageChange($data['curr'], $data['prev']);

        return [
            'mainCount' => $data['curr'],
            'postCount' => $data['post'],
            'podcastCount' => $data['pod'],
            'videoCount' => $data['vid'],
            'imageCount' => $data['img'],
        ];
    }

    public function updatedPostViewsFilter(): void
    {
        $this->postViewsCount();
    }

    #[Computed]
    public function totalParticipants()
    {
        [$sC, $eC, $sP, $eP] = $this->getDateRanges($this->participantsFilter);
        $key = 'md:participants:' . $this->participantsFilter . ':' . ($this->from_date ?? '-') . ':' . ($this->to_date ?? '-');

        $data = Cache::remember($key, $this->cacheTtl, function () use ($sC, $eC, $sP, $eP) {
            $prev = DB::table('participants')->whereBetween('created_at', [$sP, $eP])->count();

            $currGrouped = DB::table('participants')
                ->whereBetween('created_at', [$sC, $eC])
                ->selectRaw('type, COUNT(*) as cnt')
                ->groupBy('type')->pluck('cnt', 'type');

            return [
                'total' => array_sum($currGrouped->toArray()),
                'authors' => (int)($currGrouped[ParticipantTypeEnum::AUTHORS->value] ?? 0),
                'presenters' => (int)($currGrouped[ParticipantTypeEnum::PRESENTERS->value] ?? 0),
                'guests' => (int)($currGrouped[ParticipantTypeEnum::GUESTS->value] ?? 0),
                'clients' => (int)($currGrouped[ParticipantTypeEnum::CLIENTS->value] ?? 0),
                'supporters' => (int)($currGrouped[ParticipantTypeEnum::SUPPORTERS->value] ?? 0),
                'team' => (int)($currGrouped[ParticipantTypeEnum::TEAM->value] ?? 0),
                'partners' => (int)($currGrouped[ParticipantTypeEnum::PARTNERS->value] ?? 0),
                'prev' => (int)$prev,
            ];
        });

        $this->changePercentageParticipants = $this->getPercentageChange($data['total'], $data['prev']);

        return [
            'total' => $data['total'],
            'authors' => $data['authors'],
            'presenters' => $data['presenters'],
            'guests' => $data['guests'],
            'clients' => $data['clients'],
            'supporters' => $data['supporters'],
            'team' => $data['team'],
            'partners' => $data['partners'],
        ];
    }

    public function updatedParticipantsFilter(): void
    {
        $this->totalParticipants();
    }

    #[Computed]
    public function fileUploadedCount(): array
    {
        [$sC, $eC, $sP, $eP] = $this->getDateRanges($this->fileUploadedFilter);
        $key = 'md:fileUploaded:' . $this->fileUploadedFilter . ':' . ($this->from_date ?? '-') . ':' . ($this->to_date ?? '-');

        $data = Cache::remember($key, $this->cacheTtl, function () use ($sC, $eC, $sP, $eP) {
            $prev = DB::table('files')->whereBetween('created_at', [$sP, $eP])->count();

            $row = DB::table('files')
                ->whereBetween('created_at', [$sC, $eC])
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN extension IN ('jpg','jpeg','png','gif','webp') THEN 1 ELSE 0 END) as images,
                    SUM(CASE WHEN extension IN ('mp4','mkv','avi','mov','flv') THEN 1 ELSE 0 END) as videos,
                    SUM(CASE WHEN extension IN ('pdf','doc','docx','xls','xlsx','ppt','pptx') THEN 1 ELSE 0 END) as docs,
                    SUM(CASE WHEN extension IN ('mp3','wav','aac','flac') THEN 1 ELSE 0 END) as audios
                ")->first();

            return [
                'total' => (int)($row->total ?? 0),
                'images' => (int)($row->images ?? 0),
                'videos' => (int)($row->videos ?? 0),
                'docs' => (int)($row->docs ?? 0),
                'audios' => (int)($row->audios ?? 0),
                'prev' => (int)$prev,
            ];
        });

        $this->changePercentageUploaded = $this->getPercentageChange($data['total'], $data['prev']);

        return [
            'total' => $data['total'],
            'images' => $data['images'],
            'videos' => $data['videos'],
            'docs' => $data['docs'],
            'audios' => $data['audios'],
        ];
    }

    public function updatedFileUploadedFilter(): void
    {
        $this->fileUploadedCount();
    }

    #[Computed]
    public function postViewsChart(): array
    {
        $weekDaysEN = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $monthsEN = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // --- CUSTOM RANGE ---
        if ($this->postViewsChartDateBeginFilter && $this->postViewsChartDateEndFilter) {
            $start = Carbon::parse($this->postViewsChartDateBeginFilter)->startOfDay();
            $end = Carbon::parse($this->postViewsChartDateEndFilter)->endOfDay();
            $key = 'md:pvChart:c:' . $start->toDateString() . ':' . $end->toDateString();

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                $rows = DB::table('views')
                    ->select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(views_number) as c'))
                    ->where('viewable_type', Post::class)
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('d')
                    ->orderBy('d')
                    ->pluck('c', 'd')->toArray();

                // labels = كل الأيام بين البداية والنهاية
                $labels = [];
                for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                    $labels[] = $d->toDateString();
                }
                $data = array_map(fn($date) => (int)($rows[$date] ?? 0), $labels);
                return ['labels' => $labels, 'data' => $data];
            });
        }

        // --- DAILY ---
        if ($this->postViewsChartFilter === 'daily') {
            $now   = now();
            $start = $now->copy()->startOfDay();
            $end   = $now->copy()->endOfDay();
            $key   = 'md:pvChart:d:' . $now->toDateString(); // كان w:oW

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                $rows = DB::table('views')
                    ->select(DB::raw('HOUR(created_at) as h'), DB::raw('SUM(views_number) as c'))
                    ->where('viewable_type', Post::class)
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('h')
                    ->pluck('c', 'h')->toArray();

                $hours  = range(0, 23);
                $labels = array_map(fn($h) => str_pad((string)$h, 2, '0', STR_PAD_LEFT) . ':00', $hours);
                $data   = array_map(fn($h) => (int)($rows[$h] ?? 0), $hours);

                return ['labels' => $labels, 'data' => $data];
            });
        }
        // --- WEEKLY ---
        if ($this->postViewsChartFilter === 'weekly') {
            $now = now();
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
            $key = 'md:pvChart:w:' . $now->format('oW');

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end, $weekDaysEN) {
                $rows = DB::table('views')
                    ->select(DB::raw('DAYNAME(created_at) as d'), DB::raw('SUM(views_number) as c'))
                    ->where('viewable_type', Post::class)
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('d')
                    ->pluck('c', 'd')->toArray();

                return [
                    'labels' => [
                        __('messages.index.Sunday'),
                        __('messages.index.Monday'),
                        __('messages.index.Tuesday'),
                        __('messages.index.Wednesday'),
                        __('messages.index.Thursday'),
                        __('messages.index.Friday'),
                        __('messages.index.Saturday'),
                    ],
                    'data' => array_map(fn($d) => (int)($rows[$d] ?? 0), $weekDaysEN),
                ];
            });
        }

        // --- MONTHLY ---
        if ($this->postViewsChartFilter === 'monthly') {
            $now = now();
            $key = 'md:pvChart:m:' . $now->format('Y-m');

            return Cache::remember($key, $this->cacheTtl, function () use ($now) {
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();

                $rows = DB::table('views')
                    ->select(DB::raw('DAY(created_at) as d'), DB::raw('SUM(views_number) as c'))
                    ->where('viewable_type', Post::class)
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('d')
                    ->pluck('c', 'd')->toArray();

                $days = range(1, $now->daysInMonth);
                return [
                    'labels' => $days,
                    'data' => array_map(fn($d) => (int)($rows[$d] ?? 0), $days),
                ];
            });
        }

        // --- YEARLY ---
        if ($this->postViewsChartFilter === 'yearly') {
            $now = now();
            $key = 'md:pvChart:y:' . $now->format('Y');

            return Cache::remember($key, $this->cacheTtl, function () use ($now) {
                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfYear();

                $rows = DB::table('views')
                    ->select(DB::raw('MONTH(created_at) as m'), DB::raw('SUM(views_number) as c'))
                    ->where('viewable_type', Post::class)
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('m')
                    ->pluck('c', 'm')->toArray();

                $labels = [
                    __('messages.index.Jan'), __('messages.index.Feb'), __('messages.index.Mar'),
                    __('messages.index.Apr'), __('messages.index.May'), __('messages.index.Jun'),
                    __('messages.index.Jul'), __('messages.index.Aug'), __('messages.index.Sep'),
                    __('messages.index.Oct'), __('messages.index.Nov'), __('messages.index.Dec'),
                ];

                return [
                    'labels' => $labels,
                    'data'   => array_map(fn($m) => (int)($rows[$m] ?? 0), range(1, 12)),
                ];
            });
        }

        // --- DAILY (fallback) ---
        return ['labels' => [], 'data' => []];
    }


    #[Computed]
    public function visitorsChart(): array
    {
        // --- CUSTOM RANGE ---
        if ($this->visitorsChartDateBeginFilter && $this->visitorsChartDateEndFilter) {
            $start = Carbon::parse($this->visitorsChartDateBeginFilter)->startOfDay();
            $end   = Carbon::parse($this->visitorsChartDateEndFilter)->endOfDay();
            $key   = 'md:vChart:c:' . $start->toDateString() . ':' . $end->toDateString();

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                $rows = DB::table('visitor_logs')
                    ->select(DB::raw('DATE(visited_at) as d'), DB::raw('COUNT(*) AS c'))
                    ->whereBetween('visited_at', [$start, $end])
                    ->groupBy('d')
                    ->orderBy('d')
                    ->pluck('c', 'd')->toArray();

                return ['labels' => array_keys($rows), 'data' => array_values($rows)];
            });
        }

        // --- DAILY (by hour) ---
        if ($this->visitorsChartFilter === 'daily') {
            $start = now()->startOfDay();
            $end   = now()->endOfDay();
            $key   = 'md:vChart:d:' . now()->toDateString();

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                $rows = DB::table('visitor_logs')
                    ->select(DB::raw('HOUR(visited_at) as h'), DB::raw('COUNT(*) AS c'))
                    ->whereBetween('visited_at', [$start, $end])
                    ->groupBy('h')
                    ->pluck('c', 'h')->toArray();

                $hours  = range(0, 23);
                $labels = array_map(fn($h) => str_pad((string)$h, 2, '0', STR_PAD_LEFT) . ':00', $hours);
                $data   = array_map(fn($h) => (int)($rows[$h] ?? 0), $hours);

                return ['labels' => $labels, 'data' => $data];
            });
        }

        // --- WEEKLY (by weekday) ---
        if ($this->visitorsChartFilter === 'weekly') {
            $start = now()->startOfWeek();
            $end   = now()->endOfWeek();
            $key   = 'md:vChart:w:' . now()->format('oW');

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                $rows = DB::table('visitor_logs')
                    ->select(DB::raw('DAYOFWEEK(visited_at) as dw'), DB::raw('COUNT(*) AS c')) // 1=Sunday .. 7=Saturday (MySQL)
                    ->whereBetween('visited_at', [$start, $end])
                    ->groupBy('dw')
                    ->pluck('c', 'dw')->toArray();

                // ترتيب/ترجمة
                $order  = [1,2,3,4,5,6,7];
                $labels = [
                    __('messages.index.Sunday'),
                    __('messages.index.Monday'),
                    __('messages.index.Tuesday'),
                    __('messages.index.Wednesday'),
                    __('messages.index.Thursday'),
                    __('messages.index.Friday'),
                    __('messages.index.Saturday'),
                ];
                $data = array_map(fn($i) => (int)($rows[$i] ?? 0), $order);

                return ['labels' => $labels, 'data' => $data];
            });
        }

        // --- MONTHLY (by day number) ---
        if ($this->visitorsChartFilter === 'monthly') {
            $key = 'md:vChart:m:' . now()->format('Y-m');

            return Cache::remember($key, $this->cacheTtl, function () {
                $rows = DB::table('visitor_logs')
                    ->select(DB::raw('DAY(visited_at) as d'), DB::raw('COUNT(*) AS c'))
                    ->whereMonth('visited_at', now()->month)
                    ->whereYear('visited_at', now()->year)
                    ->groupBy('d')
                    ->pluck('c', 'd')->toArray();

                $days = range(1, now()->daysInMonth);
                return ['labels' => $days, 'data' => array_map(fn($d) => (int)($rows[$d] ?? 0), $days)];
            });
        }

        // --- YEARLY (by month) ---
        if ($this->visitorsChartFilter === 'yearly') {
            $key = 'md:vChart:y:' . now()->format('Y');

            return Cache::remember($key, $this->cacheTtl, function () {
                $rows = DB::table('visitor_logs')
                    ->select(DB::raw('MONTH(visited_at) as m'), DB::raw('COUNT(*) AS c'))
                    ->whereYear('visited_at', now()->year)
                    ->groupBy('m')
                    ->pluck('c', 'm')->toArray();

                $monthsOrder = range(1, 12);
                $labels = [
                    __('messages.index.Jan'), __('messages.index.Feb'), __('messages.index.Mar'),
                    __('messages.index.Apr'), __('messages.index.May'), __('messages.index.Jun'),
                    __('messages.index.Jul'), __('messages.index.Aug'), __('messages.index.Sep'),
                    __('messages.index.Oct'), __('messages.index.Nov'), __('messages.index.Dec'),
                ];
                $data = array_map(fn($m) => (int)($rows[$m] ?? 0), $monthsOrder);

                return ['labels' => $labels, 'data' => $data];
            });
        }

        return ['labels' => [], 'data' => []];
    }

    #[Computed]
    public function visitorsTypeChart(): array
    {
        // custom
        if ($this->visitorsTypeChartDateBeginFilter && $this->visitorsTypeChartDateEndFilter) {
            $start = Carbon::parse($this->visitorsTypeChartDateBeginFilter)->startOfDay();
            $end = Carbon::parse($this->visitorsTypeChartDateEndFilter)->endOfDay();
            $key = 'md:vType:c:' . $start->toDateString() . ':' . $end->toDateString();

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                $data = DB::table('visitor_logs')
                    ->select('device_type', DB::raw('COUNT(*) as total'))
                    ->whereBetween('visited_at', [$start, $end])
                    ->groupBy('device_type')->get();

                return ['labels' => $data->pluck('device_type'), 'data' => $data->pluck('total')];
            });
        }

        $filter = $this->visitorsTypeChartFilter;
        $key = 'md:vType:' . $filter . ':' . now()->format('Y-m-d');

        $data = Cache::remember($key, $this->cacheTtl, function () use ($filter) {
            $q = DB::table('visitor_logs')
                ->select('device_type', DB::raw('COUNT(*) as total'));

            if ($filter === 'daily') {
                $q->whereDate('visited_at', now()->toDateString());
            } elseif ($filter === 'weekly') {
                $q->whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filter === 'monthly') {
                $q->whereMonth('visited_at', now()->month)->whereYear('visited_at', now()->year);
            } elseif ($filter === 'yearly') {
                $q->whereYear('visited_at', now()->year);
            }

            return $q->groupBy('device_type')->get();
        });

        return ['labels' => $data->pluck('device_type'), 'data' => $data->pluck('total')];
    }

    public function updatedVisitorsTypeChartFilter(): void
    {
        $this->dispatch("updateDeviceUsageChart", $this->visitorsTypeChart);
    }

    public function updatedVisitorsTypeChartDateBeginFilter(): void
    {
        $this->dispatch("updateDeviceUsageChart", $this->visitorsTypeChart);
    }

    public function updatedVisitorsTypeChartDateEndFilter(): void
    {
        $this->dispatch("updateDeviceUsageChart", $this->visitorsTypeChart);
    }

    #[Computed]
    public function trafficSourcesChart(): array
    {
        // --- CUSTOM RANGE ---
        if ($this->trafficSourcesChartDateBeginFilter && $this->trafficSourcesChartDateEndFilter) {
            $start = Carbon::parse($this->trafficSourcesChartDateBeginFilter)->startOfDay();
            $end = Carbon::parse($this->trafficSourcesChartDateEndFilter)->endOfDay();
            $key = 'md:trafficSources:c:' . $start->toDateString() . ':' . $end->toDateString();

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getTrafficSourcesData($start, $end);
            });
        }

        // --- DAILY ---
        if ($this->trafficSourcesChartFilter === 'daily') {
            $start = now()->startOfDay();
            $end = now()->endOfDay();
            $key = 'md:trafficSources:d:' . now()->toDateString();

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getTrafficSourcesData($start, $end);
            });
        }

        // --- WEEKLY ---
        if ($this->trafficSourcesChartFilter === 'weekly') {
            $start = now()->startOfWeek();
            $end = now()->endOfWeek();
            $key = 'md:trafficSources:w:' . now()->format('oW');

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getTrafficSourcesData($start, $end);
            });
        }

        // --- MONTHLY ---
        if ($this->trafficSourcesChartFilter === 'monthly') {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $key = 'md:trafficSources:m:' . now()->format('Y-m');

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getTrafficSourcesData($start, $end);
            });
        }

        // --- YEARLY ---
        if ($this->trafficSourcesChartFilter === 'yearly') {
            $start = now()->startOfYear();
            $end = now()->endOfYear();
            $key = 'md:trafficSources:y:' . now()->format('Y');

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getTrafficSourcesData($start, $end);
            });
        }

        return ['labels' => [], 'data' => []];
    }

    protected function getTrafficSourcesData($start, $end): array
    {
        // Single query: group ALL referrers including NULL/empty (merged from 2 queries)
        $rows = DB::table('visitor_logs')
            ->select(DB::raw("COALESCE(NULLIF(referrer, ''), '__direct__') as ref_key"), DB::raw('COUNT(*) as total'))
            ->whereBetween('visited_at', [$start, $end])
            ->groupBy('ref_key')
            ->orderByDesc('total')
            ->limit(100)
            ->get();

        $sources = [];
        $colors = [
            'rgba(59, 89, 152, 0.8)',   // Facebook blue
            'rgba(29, 161, 242, 0.8)',  // Twitter blue
            'rgba(0, 119, 181, 0.8)',   // LinkedIn blue
            'rgba(193, 53, 132, 0.8)',  // Instagram gradient
            'rgba(255, 0, 0, 0.8)',     // YouTube red
            'rgba(37, 211, 102, 0.8)',  // WhatsApp green
            'rgba(0, 123, 255, 0.8)',   // Generic blue
            'rgba(255, 193, 7, 0.8)',   // Generic yellow
            'rgba(220, 53, 69, 0.8)',   // Generic red
            'rgba(108, 117, 125, 0.8)', // Generic gray
        ];

        // Get APP_URL domain
        $appUrl = config('app.url');
        $appDomain = parse_url($appUrl, PHP_URL_HOST);
        $appDomain = preg_replace('/^www\./', '', strtolower($appDomain ?? ''));

        $directTrafficCount = 0;

        foreach ($rows as $row) {
            if ($row->ref_key === '__direct__') {
                $directTrafficCount += (int)$row->total;
                continue;
            }

            // Check if referrer is from the same domain as APP_URL
            $referrerDomain = parse_url(strtolower($row->ref_key), PHP_URL_HOST);
            $referrerDomain = preg_replace('/^www\./', '', $referrerDomain ?? '');

            if ($referrerDomain === $appDomain) {
                $directTrafficCount += (int)$row->total;
            } else {
                $source = $this->parseReferrer($row->ref_key);
                if (isset($sources[$source])) {
                    $sources[$source] += (int)$row->total;
                } else {
                    $sources[$source] = (int)$row->total;
                }
            }
        }

        if ($directTrafficCount > 0) {
            $sources[__('messages.index.direct_traffic')] = $directTrafficCount;
        }

        // Sort by value descending
        arsort($sources);

        // Take top 10
        $sources = array_slice($sources, 0, 10, true);

        $labels = array_keys($sources);
        $data = array_values($sources);
        $backgroundColor = array_slice($colors, 0, count($labels));

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => __('messages.index.traffic_sources'),
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => 'rgba(255, 255, 255, 0.8)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    protected function parseReferrer(string $referrer): string
    {
        $referrer = strtolower($referrer);
        
        // Social media platforms
        if (str_contains($referrer, 'facebook.com') || str_contains($referrer, 'fb.com')) {
            return 'Facebook';
        }
        if (str_contains($referrer, 'twitter.com') || str_contains($referrer, 't.co') || str_contains($referrer, 'x.com')) {
            return 'Twitter/X';
        }
        if (str_contains($referrer, 'instagram.com')) {
            return 'Instagram';
        }
        if (str_contains($referrer, 'linkedin.com')) {
            return 'LinkedIn';
        }
        if (str_contains($referrer, 'youtube.com') || str_contains($referrer, 'youtu.be')) {
            return 'YouTube';
        }
        if (str_contains($referrer, 'tiktok.com')) {
            return 'TikTok';
        }
        if (str_contains($referrer, 'pinterest.com')) {
            return 'Pinterest';
        }
        if (str_contains($referrer, 'reddit.com')) {
            return 'Reddit';
        }
        if (str_contains($referrer, 'whatsapp.com') || str_contains($referrer, 'wa.me')) {
            return 'WhatsApp';
        }
        if (str_contains($referrer, 'telegram.org') || str_contains($referrer, 't.me')) {
            return 'Telegram';
        }
        
        // Search engines
        if (str_contains($referrer, 'google.com') || str_contains($referrer, 'google.')) {
            return 'Google';
        }
        if (str_contains($referrer, 'bing.com')) {
            return 'Bing';
        }
        if (str_contains($referrer, 'yahoo.com')) {
            return 'Yahoo';
        }
        if (str_contains($referrer, 'duckduckgo.com')) {
            return 'DuckDuckGo';
        }
        
        // Extract domain
        $parsed = parse_url($referrer);
        $host = $parsed['host'] ?? $referrer;
        $host = preg_replace('/^www\./', '', $host);
        
        return ucfirst($host);
    }

    public function updatedPostViewsChartFilter(): void
    {
        $this->dispatch('updatePostViewsChart', $this->postViewsChart);
    }

    public function updatedPostViewsChartDateBeginFilter(): void
    {
        $this->dispatch('updatePostViewsChart', $this->postViewsChart);
    }

    public function updatedPostViewsChartDateEndFilter(): void
    {
        $this->dispatch('updatePostViewsChart', $this->postViewsChart);
    }

    public function updatedVisitorsChartFilter(): void
    {
        $this->dispatch('updateVisitorsChart', $this->visitorsChart);
    }

    public function updatedVisitorsChartDateBeginFilter()
    {
        $this->dispatch('updateVisitorsChart', $this->visitorsChart);
    }

    public function updatedVisitorsChartDateEndFilter()
    {
        $this->dispatch('updateVisitorsChart', $this->visitorsChart);
    }

    public function updatedTrafficSourcesChartFilter(): void
    {
        $this->dispatch('updateTrafficSourcesChart', $this->trafficSourcesChart);
    }

    public function updatedTrafficSourcesChartDateBeginFilter(): void
    {
        $this->dispatch('updateTrafficSourcesChart', $this->trafficSourcesChart);
    }

    public function updatedTrafficSourcesChartDateEndFilter(): void
    {
        $this->dispatch('updateTrafficSourcesChart', $this->trafficSourcesChart);
    }

    #[Computed]
    public function visitorsMapData(): array
    {
        // --- CUSTOM RANGE ---
        if ($this->visitorsMapDateBeginFilter && $this->visitorsMapDateEndFilter) {
            $start = Carbon::parse($this->visitorsMapDateBeginFilter)->startOfDay();
            $end = Carbon::parse($this->visitorsMapDateEndFilter)->endOfDay();
            $key = 'md:vMap:c:' . $start->toDateString() . ':' . $end->toDateString();

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getVisitorsMapData($start, $end);
            });
        }

        // --- DAILY ---
        if ($this->visitorsMapFilter === 'daily') {
            $start = now()->startOfDay();
            $end = now()->endOfDay();
            $key = 'md:vMap:d:' . now()->toDateString();

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getVisitorsMapData($start, $end);
            });
        }

        // --- WEEKLY ---
        if ($this->visitorsMapFilter === 'weekly') {
            $start = now()->startOfWeek();
            $end = now()->endOfWeek();
            $key = 'md:vMap:w:' . now()->format('oW');

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getVisitorsMapData($start, $end);
            });
        }

        // --- MONTHLY ---
        if ($this->visitorsMapFilter === 'monthly') {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $key = 'md:vMap:m:' . now()->format('Y-m');

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getVisitorsMapData($start, $end);
            });
        }

        // --- YEARLY ---
        if ($this->visitorsMapFilter === 'yearly') {
            $start = now()->startOfYear();
            $end = now()->endOfYear();
            $key = 'md:vMap:y:' . now()->format('Y');

            return Cache::remember($key, $this->cacheTtl, function () use ($start, $end) {
                return $this->getVisitorsMapData($start, $end);
            });
        }

        return [];
    }

    protected function getVisitorsMapData($start, $end): array
    {
        $rows = DB::table('visitor_logs')
            ->select('country', DB::raw('COUNT(*) as total'))
            ->whereBetween('visited_at', [$start, $end])
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(200)
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $data[$row->country] = (int)$row->total;
        }

        return $data;
    }

    public function updatedVisitorsMapFilter(): void
    {
        $this->dispatch('updateVisitorsMap', $this->visitorsMapData);
    }

    public function updatedVisitorsMapDateBeginFilter(): void
    {
        $this->dispatch('updateVisitorsMap', $this->visitorsMapData);
    }

    public function updatedVisitorsMapDateEndFilter(): void
    {
        $this->dispatch('updateVisitorsMap', $this->visitorsMapData);
    }

    public function editPost()
    {
        $this->validate(['postNumber' => 'required|integer|exists:posts,id']);
        return redirect()->route('dashboard.posts.create_update_post', $this->postNumber);
    }

    public function addBreakingNews()
    {
        $validation = $this->validate([
            'title' => 'required|string',
            'url' => 'required|url',
            'hide_date' => 'required|integer|min:1',
        ]);
        $BreakingNews = BreakingNews::query()->create($validation);
        $BreakingNews->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->reset(['title', 'url', 'hide_date']);
    }
}
