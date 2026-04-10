<?php

namespace App\Livewire\Dashboard;

use App\Enums\LoggableModelsEnum;
use App\Models\UserLog;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserLogsExport;
use Carbon\Carbon;

class ListUserLogs extends Component
{
    use WithPagination;

    public ?string $search = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $list = null;
    #[Url]
    public ?string $actionable_type = null;

    #[Url]
    public ?string $actionable_id = null;
    protected string $paginationTheme = 'bootstrap';
    public string $sortField = "id";
    public string $sortDirection = "asc";
    public $modelType;
    public UserLog $log_item;
    public $modelAfterData;
    public $modelBeforeData;
    public array $model_title_ar , $model_title_en, $model_titles;
    public  string $export_lang = 'ar';
    public $changes;
    public $details;
    public bool $showUserStats = false;

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-user-logs');
    }

    public function mount(): void
    {
        $this->list = "all";
        if ($this->actionable_type) {
            $this->list = $this->actionable_type;
        }
        $this->model_title_ar = [
            'App\Models\Category' => [
                'category_title' => 'عنوان التصنيف',
                'category_description' => 'وصف التصنيف',
            ],
            'App\Models\Author' => [
                'author_name' => 'اسم الكاتب',
                'author_work' => '	عمل الكاتب',
                'author_facebook' => 'فيسبوك الكاتب',
                'author_twitter' => 'تويتر الكاتب',
                'author_google_plus' => 'قوقل بلس الكاتب',
                'author_description' => 'وصف الكاتب',
            ],
            'App\Models\BreakingNews' => [
                'title' => 'عنوان الخبر العاجل',
                'url' => 'رابط الخبر العاجل',
                'hide_date' => 'ينتهي بعد',
            ],
            'App\Models\Type' => [
                'type_name' => 'عنوان نوع الخبر',
            ],
            'App\Models\SpecialFile' => [
                'file_name' => 'عنوان الملف خاص',
            ],
            'App\Models\Tag' => [
                'tag_name' => 'عنوان الوسم',
            ],
            'App\Models\Quote' => [
                'quote_author' => 'كاتب الاقتباس',
                'quote_from' => 'الإقتباس من منصة',
                'quote_text' => '	نص الاقتباس',
            ],
            'App\Models\SpecialPage' => [
                'page_title' => 'العنوان',
                'page_content' => 'التفاصيل',
            ],
            'App\Models\Advertisement' => [
                'title' => 'العنوان',
                'place' => 'المكان',
                'type' => 'النوع',
                'url' => 'الرابط',
                'url_target' => 'ابن يفتح الرابط؟',
                'end_hour_time' => 'وقت الانتهاء بالساعة',
                'end_min_time' => 'وقت الانتهاء بالدقيقة',
            ],
            'App\Models\PodcastAlbum' => [
                'title' => 'عنوان الموضوع',
                'description' => 'وصف الموضوع',
            ],
            'App\Models\PodcastTrack' => [
                'title' => 'عنوان الموضوع',
                'description' => 'وصف الموضوع',
                'url' => 'الرابط',
                'podcast_album_id' => 'الموضوع',
            ],
            'App\Models\VideoAlbum' => [
                'title' => 'عنوان الالبوم',
                'description' => 'وصف الالبوم',
            ],
            'App\Models\VideoTrack' => [
                'title' => 'عنوان الفيديو',
                'description' => 'وصف الفيديو',
                'url' => 'رابط الفيديو',
                'video_album_id' => 'الألبوم',
            ],
            'App\Models\LiveStream' => [
                'title' => 'عنوان البث مباشر ',
                'url' => 'رابط البث المباشر',
                'end_date' =>'ينتهي بتاريخ'
            ],
            'App\Models\User' => [
                'first_name' => 'الاسم الأول',
                'last_name' => 'الاسم الأخير',
                'email' =>'البريد الالكتروني',
                'role_id' => 'حالة المستخدم',
            ],

        ];
        $this->model_title_en = [
            'App\Models\Category' => [
                'category_title' => 'Category Title',
                'category_description' => 'Category Description',
            ],
            'App\Models\Author' => [
                'author_name' =>'Author Name',
                'author_work' => 'Author Work ',
                'author_facebook' => ' Author Facebook',
                'author_twitter' => 'Author Twitter ',
                'author_google_plus' => 'Author google plus  ',
                'author_description' => 'Author Description ',
            ],
            'App\Models\BreakingNews' => [
                'title' => 'News Title ',
                'url' => 'News URL  ',
                'hide_date' => 'Expires In (Hours) ',
            ],
            'App\Models\Type' => [
                'type_name' => 'News Type Title',
            ],
            'App\Models\SpecialFile' => [
                'file_name' => 'File Title',
            ],
            'App\Models\Tag' => [
                'tag_name' => 'Tag Title',
            ],
            'App\Models\Quote' => [
                'quote_author' => 'Author name',
                'quote_from' => 'Quote from platform',
                'quote_text' => 'Quote text',
            ],
            'App\Models\SpecialPage' => [
                'page_title' => 'Title',
                'page_content' => 'Details',
            ],
            'App\Models\Advertisement' => [
                'title' => 'Title',
                'place' => 'Place',
                'type' => 'Type',
                'url' => 'Url',
                'url_target' => 'How should the URL open ?',
                'end_hour_time' => 'End Time in hour',
                'end_min_time' => 'End Time in time',
            ],
            'App\Models\PodcastAlbum' => [
                'title' => 'Subject Title',
                'description' => 'Subject Description',
            ],
            'App\Models\PodcastTrack' => [
                'title' => 'Podcast Title',
                'description' => 'Podcast Description',
                'url' => 'Url',
                'podcast_album_id' => 'Topic',


            ],
            'App\Models\VideoAlbum' => [
                'title' => 'Album Title',
                'description' =>'Album Description',
            ],
            'App\Models\VideoTrack' => [
                'title' => 'Video Title',
                'description' => 'Video Description',
                'url' => 'Video URL',
                'video_album_id' => 'Album',
            ],
            'App\Models\LiveStream' => [
                'title' => 'Live Stream Title',
                'url' => 'Live Stream URL',
                'end_date' =>'Ends At'
            ],
            'App\Models\User' => [
                'first_name' =>'First Name',
                'last_name' => 'Last Name',
                'email' =>'Email',
                'role_id' => 'User Status',
            ],

        ];
        $this->model_titles= [
            'App\Models\Category' => 'category_title',
            'App\Models\Author' => 'author_name',
            'App\Models\BreakingNews' => 'title',
            'App\Models\Type' => 'type_name',
            'App\Models\SpecialFile' => 'file_name',
            'App\Models\Tag' => 'tag_name',
            'App\Models\Quote' => 'quote_author',
            'App\Models\SpecialPage' => 'page_title',
            'App\Models\Advertisement' => 'title',
            'App\Models\PodcastAlbum' => 'title',
            'App\Models\PodcastTrack' => 'title',
            'App\Models\VideoAlbum' => 'title',
            'App\Models\VideoTrack' => 'title',
            'App\Models\LiveStream' => 'title',
            'App\Models\User' => 'first_name',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedList(): void
    {

        $this->resetPage();
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    #[Computed]
    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) {
            return 'bi-arrow-down-up';
        }

        return $this->sortDirection === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
    }

    #[Computed]
    public function user_logs()
    {
        // Select only essential fields, exclude heavy JSON columns
        $query = UserLog::select(
                'user_logs.id',
                'user_logs.user_id',
                'user_logs.actionable_type',
                'user_logs.actionable_id',
                'user_logs.action_status',
                'user_logs.created_at',
                // Only load JSON data for display purposes (first 200 chars)
                DB::raw('LEFT(user_logs.actionable_data_after, 200) as actionable_data_after_preview'),
                DB::raw('LEFT(user_logs.actionable_data_before, 200) as actionable_data_before_preview')
            )
            ->where(function ($q) {
                if ($this->list != "all") {
                    $q->where('actionable_type', $this->list);
                }
                if ($this->actionable_id) {
                    $q->where('actionable_id', $this->actionable_id);
                }
            })
            ->when(isset($this->start_date) && isset($this->end_date), function ($query) {
                $query->whereBetween('user_logs.created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59']);
            })
            ->when($this->search, function ($query) {
                $query->join('users', 'users.id', '=', 'user_logs.user_id');
                $searchTerms = explode(' ', $this->search);
                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->where(function ($subQ) use ($term) {
                            $subQ->where('users.first_name', 'like', '%' . $term . '%')
                                ->orWhere('users.last_name', 'like', '%' . $term . '%');
                        });
                    }
                });
            });

        // Always eager load user (the join for search doesn't conflict with eager loading)
        $query->with(['user:id,first_name,last_name']);

        // Do NOT eager load 'actionable' because actionable_type is an int now
        // and the morphTo relation would try to resolve a class from an int, causing errors.

        return $query->orderBy('user_logs.' . $this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    #[Computed]
    public function user_logs_counter()
    {
        return UserLog::select(
                'user_logs.user_id',
                'user_logs.action_status',
                DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_full_name'),
                DB::raw('count(*) as count')
            )
            ->join('users', 'users.id', '=', 'user_logs.user_id')
            ->where(function ($q) {
                if ($this->list != "all") {
                    $q->where('user_logs.actionable_type', $this->list);
                }
                if ($this->actionable_id) {
                    $q->where('user_logs.actionable_id', $this->actionable_id);
                }
            })
            ->when(isset($this->start_date) && isset($this->end_date), function ($query) {
                $query->whereBetween('user_logs.created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59']);
            })
            ->when($this->search, function ($query) {
                $searchTerms = explode(' ', $this->search);
                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->where(function ($subQ) use ($term) {
                            $subQ->where('users.first_name', 'like', '%' . $term . '%')
                                ->orWhere('users.last_name', 'like', '%' . $term . '%');
                        });
                    }
                });
            })
            ->groupBy('user_logs.user_id', 'user_logs.action_status', 'users.first_name', 'users.last_name')
            ->get()
            ->groupBy('user_full_name')
            ->map(function ($group) {
                $actionStatuses = [\App\Enums\ActionsEnum::CREATE->value, \App\Enums\ActionsEnum::EDIT->value, \App\Enums\ActionsEnum::DELETE->value];
                $result = [];
                foreach ($actionStatuses as $status) {
                    $result[$status] = 0;
                }
                foreach ($group as $item) {
                    $result[$item['action_status']] = $item['count'];
                }
                return $result;
            })
            ->toArray();
    }

    #[Computed(persist: true)]
    public function model_counts()
    {
        // Get all counts in a single query with caching
        return cache()->remember('user_logs_model_counts', 300, function () {
            return UserLog::select('actionable_type', DB::raw('count(*) as count'))
                ->groupBy('actionable_type')
                ->get()
                ->pluck('count', 'actionable_type')
                ->toArray();
        });
    }

    public function userLogDetails(int $userLogId): void
    {
        $userLog = UserLog::find($userLogId);
        if (!$userLog) {
            return;
        }

        $enumCase = LoggableModelsEnum::tryFrom($userLog?->actionable_type);
        $this->modelType = $enumCase ? class_basename($enumCase->modelClass()) : null;

        $before = json_decode($userLog?->actionable_data_before, true) ?? [];
        $after  = json_decode($userLog?->actionable_data_after, true) ?? [];

        // نجيب عناوين الحقول من ملف الترجمة
        $fields = __("enums.fields.{$this->modelType}");

        $changes = [];

        // هنا بنستخدم فقط المفاتيح اللي في after
        foreach ($after as $db_field => $afterValue) {
            $beforeValue = $before[$db_field] ?? null;

            if (is_null($beforeValue) && is_null($afterValue)) {
                continue;
            }

            if ($beforeValue == $afterValue) {
                continue;
            }

            $changes[] = [
                'field'  => $db_field,
                'label'  => $fields[$db_field] ?? $db_field,
                'before' => $beforeValue ?? __('messages.no_data'),
                'after'  => $afterValue ?? __('messages.no_data'),
            ];
        }

        $this->changes = $changes;
        $this->dispatch('show_user_logs_details');
    }


    public function export()
    {
        $name = Carbon::parse(now())->format('Y-m-d H-i') . ' Records.xlsx';

        if($this->export_lang == 'ar')
        {
            $model_title  = $this->model_title_ar;
        }else{
            $model_title  = $this->model_title_en;
        }
        $export = Excel::download(new UserLogsExport($this->user_logs(), $this->list, $model_title , $this->export_lang), $name);

        $this->dispatch('hide_export_with_sort');
        return $export;
    }

    public function exportWithSortModal()
    {

        $this->dispatch('show_export_with_sort');
    }

    public function clearPostFilter(): void
    {
        $this->actionable_id = null;
        $this->actionable_type = null;
        $this->list = "all";
        $this->resetPage();
    }

    public function userLogTypeDetails(int $userLogId): void
    {
        $userLog = UserLog::find($userLogId);
        if (!$userLog) {
            return;
        }
        $this->log_item = $userLog;

        $enumCase = LoggableModelsEnum::tryFrom($userLog?->actionable_type);

        $class = $enumCase?->modelClass();

        $this->modelType = $class ? class_basename($class) : null;

        $details = [];

        if ($class && $userLog->actionable_id) {
            $modelInstance = $class::find($userLog->actionable_id);

            $fields = __("enums.fields.{$this->modelType}");

            foreach ($fields as $db_field => $label) {
                $details[] = [
                    'field' => $db_field,
                    'label' => $label,
                    'value' => $modelInstance->{$db_field} ?? __('messages.no_data'),
                ];
            }
        }

        $this->details = $details;
        $this->dispatch('show_user_logs_type_details');
    }

}
