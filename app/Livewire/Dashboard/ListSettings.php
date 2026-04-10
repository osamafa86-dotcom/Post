<?php


namespace App\Livewire\Dashboard;


use App\Models\Files;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use App\Traits\WhereIn;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;


class ListSettings extends Component
{
    use WithFileUploads, WhereIn;


    public array $state = [];
    #[Url]
    public string $tab = 'public';
    public $isHodhod;
    public $isCillium;
    public $isAlmohajer;
    public $isTayqan;

    public function mount(): void
    {
        $this->isHodhod = config('app.launch') === 'hodhod';
        $this->isCillium = config('app.launch') === 'cillium_fm';
        $this->isAlmohajer = config('app.launch') === 'almohajer';
        $this->isTayqan = config('app.launch') === 'tayqan';


        $querySetting = Setting::with('files')->first();
        $site_logo_file = $querySetting?->files()?->where('model_column', 'site_logo')->with('file')->first()->file->path ?? null;
        $favicon_file = $querySetting?->files()?->where('model_column', 'favicon')->with('file')->first()->file->path ?? null;
        $footer_logo_file = $querySetting?->files()?->where('model_column', 'footer_logo')->with('file')->first()->file->path ?? null;
        $hodhod_banner_file = $querySetting?->files()?->where('model_column', 'hodhod_banner')->with('file')->first()->file->path ?? null;
        $landingPageInformationPicturesTopLeft = $querySetting?->files()?->where('model_column', 'landingPageInformationPicturesTopLeft')->with('file')->first() ?? null;
        $landingPageInformationPicturesTopRight = $querySetting?->files()?->where('model_column', 'landingPageInformationPicturesTopRight')->with('file')->first() ?? null;
        $landingPageInformationPicturesBottomLeft = $querySetting?->files()?->where('model_column', 'landingPageInformationPicturesBottomLeft')->with('file')->first() ?? null;
        $landingPageInformationPicturesBottomRight = $querySetting?->files()?->where('model_column', 'landingPageInformationPicturesBottomRight')->with('file')->first() ?? null;
        $open_graph_image = $querySetting?->files()?->where('model_column', 'open_graph_image')->first();
        $water_mark_image = $querySetting?->files()?->where('model_column', 'water_mark_image')->first();
        $setting = $querySetting?->toArray();


        $this->state = [
            'site_name' => $setting['site_name'] ?? null,
            'light_site_name' => $setting['light_site_name'] ?? null,
            'address' => $setting['address'] ?? null,
            'phone' => $setting['phone'] ?? null,
            'footer_description' => $setting['footer_description'] ?? null,
            'site_description' => $setting['site_description'] ?? null,
            'copyright_text' => $setting['copyright_text'] ?? null,
            'whatsapp_link' => ($this->isAlmohajer || $this->isTayqan) && isset($setting['whatsapp_link']) ? $setting['whatsapp_link'] : null,
            'podcast_url' => $this->isCillium ? ($setting['podcast_url'] ?? null) : null,
            'claim_url' => $this->isTayqan ? ($setting['claim_url'] ?? null) : null,
            'site_logo' => $querySetting?->files()->where('model_column', 'site_logo')->with('file')->first()->file->id ?? null,
            'favicon' => $querySetting?->files()->where('model_column', 'favicon')->with('file')->first()->file->id ?? null,
            'footer_logo' => $querySetting?->files()->where('model_column', 'footer_logo')->with('file')->first()->file->id ?? null,
            'hodhod_banner' => $this->isHodhod ? ($querySetting?->files()->where('model_column', 'hodhod_banner')->with('file')->first()->file->id ?? null) : null,
            'hodhod_banner_name' => $this->isHodhod ? $hodhod_banner_file : null,
            'site_logo_name' => $site_logo_file,
            'favicon_name' => $favicon_file,
            'footer_logo_name' => $footer_logo_file,
            'website_active' => isset($setting['website_active']) ? (bool)$setting['website_active'] : null,
            'site_email' => $setting['site_email'] ?? null,
            'open_graph_image' => $open_graph_image?->file_id,
            'open_graph_image_name' => isset($open_graph_image) ? Files::where('id', $open_graph_image->file_id)->value('path') : null,
            'tags' => $setting['tags'] ?? null,
            'open_graph_description' => $setting['open_graph_description'] ?? null,
            'open_graph_local' => $setting['open_graph_local'] ?? null,
            'open_graph_author' => $setting['open_graph_author'] ?? null,
            'open_graph_twitter_creator' => $setting['open_graph_twitter_creator'] ?? null,
            'open_graph_twitter_site' => $setting['open_graph_twitter_site'] ?? null,
            'header_code' => $setting['header_code'] ?? null,
            'footer_code' => $setting['footer_code'] ?? null,
            'water_mark_image' => $water_mark_image?->file_id,
            'water_mark_image_name' => isset($water_mark_image) ? Files::where('id', $water_mark_image->file_id)->value('path') : null,
            'water_mark_place' => $setting['water_mark_place'] ?? null,
            'landing_page_url' => $setting['landing_page_url'] ?? null,
            'landingPageInformationPicturesTopLeft' => $landingPageInformationPicturesTopLeft?->file_id ?? null,
            'landingPageInformationPicturesTopRight' => $landingPageInformationPicturesTopRight?->file_id ?? null,
            'landingPageInformationPicturesBottomLeft' => $landingPageInformationPicturesBottomLeft?->file_id ?? null,
            'landingPageInformationPicturesBottomRight' => $landingPageInformationPicturesBottomRight?->file_id ?? null,
            'landingPageInformationPicturesTopLeft_name' => isset($landingPageInformationPicturesTopLeft) ? Files::where('id', $landingPageInformationPicturesTopLeft?->file_id)->value('path') : null,
            'landingPageInformationPicturesTopRight_name' => isset($landingPageInformationPicturesTopRight) ? Files::where('id', $landingPageInformationPicturesTopRight?->file_id)->value('path') : null,
            'landingPageInformationPicturesBottomLeft_name' => isset($landingPageInformationPicturesBottomLeft) ? Files::where('id', $landingPageInformationPicturesBottomLeft?->file_id)->value('path') : null,
            'landingPageInformationPicturesBottomRight_name' => isset($landingPageInformationPicturesBottomRight) ? Files::where('id', $landingPageInformationPicturesBottomRight?->file_id)->value('path') : null,
        ];

//
//        if (!empty($landingPageInformationPicturesTopLeft)) {
//            foreach ($landingPageInformationPicturesTopLeft as $morePicture) {
//                $this->state[''][] = $morePicture->file_id;
//                $this->state['landingPageInformationPicturesTopLeft_name'][$morePicture->file_id] = Files::where('id', $morePicture->file_id)->value('path');
//            }
//        }
//        if (!empty($landingPageInformationPicturesTopRight)) {
//            foreach ($landingPageInformationPicturesTopRight as $morePicture) {
//                $this->state[''][] = $morePicture->file_id;
//                $this->state['landingPageInformationPicturesTopRight_name'][$morePicture->file_id] = Files::where('id', $morePicture->file_id)->value('path');
//            }
//        }
//        if (!empty($landingPageInformationPicturesBottomLeft)) {
//            foreach ($landingPageInformationPicturesBottomLeft as $morePicture) {
//                $this->state[''][] = $morePicture->file_id;
//                $this->state['landingPageInformationPicturesBottomLeft_name'][$morePicture->file_id] = Files::where('id', $morePicture->file_id)->value('path');
//            }
//        }
//        if (!empty($landingPageInformationPicturesBottomRight)) {
//            foreach ($landingPageInformationPicturesBottomRight as $morePicture) {
//                $this->state[''][] = $morePicture->file_id;
//                $this->state['landingPageInformationPicturesBottomRight_name'][$morePicture->file_id] = Files::where('id', $morePicture->file_id)->value('path');
//            }
//        }
    }


    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.list-settings');
    }


    #[Computed]
    public function tags(): array
    {
        return Tag::select('tag_name')->get()->pluck('tag_name')->toArray();
    }


    public function openTab($value): void
    {
        $this->tab = $value;
    }


    #[On('imageSelected')]
    public function imageSelected($id, $column = null)
    {
        if (isset($column)) {
            if (is_array($id)) {
                $this->state[$column] = $id;
                foreach ($id as $file_id) {
                    $this->state[$column . '_name'][$file_id] = Files::where('id', $file_id)->value('path');
                }
            } else {
                $this->state[$column] = $id;
                $this->state[$column . '_name'] = Files::where('id', $id)->value('path');
            }
            if ($column !== 'hodhod_banner' && $this->isHodhod) {
                $this->state['is_hodhod'] = true;
            }
        }
    }


    public function clearColumn($column, $value = null): void
    {
        if (!isset($value)) {
            $this->state[$column] = null;
            $this->state[$column . '_name'] = null;

            if ($column !== 'hodhod_banner' && $this->isHodhod) {
                $this->state['is_hodhod'] = true;
            }
        } else {
            if (($key = array_search($value, $this->state[$column])) !== false) {
                unset($this->state[$column][$key]);
            }
            $this->state[$column] = array_values($this->state[$column]);
            unset($this->state[$column . '_name'][$value]);
        }
    }


    /**
     * @throws ValidationException
     */
    public function updateSetting(): void
    {
        if ($this->tab == 'public' && auth()->user()->can('settings_general_settings')) {
            $public_validate = Validator::make($this->state, [
                'site_name' => 'nullable|string|max:25',
                'light_site_name' => 'nullable|string|max:100',
                'site_email' => 'nullable|email',
                'whatsapp_link' => 'nullable|string|url|max:255',
                'podcast_url' => 'nullable|string|url|max:255',
                'claim_url' => 'nullable|string|url|max:255',
                'site_logo' => 'nullable|integer|exists:files,id',
                'favicon' => 'nullable|integer|exists:files,id',
                'footer_logo' => 'nullable|integer|exists:files,id',
                'hodhod_banner' => 'nullable|integer|exists:files,id',
                'footer_description' => 'nullable|string',
                'address' => 'nullable|string|max:100',
                'phone' => 'nullable|numeric',
                'copyright_text' => 'nullable|string|max:100',
            ])->validate();
            $settings = Setting::with('files')->first();
            Setting::updateOrCreate(
                ['id' => $settings?->id],
                [
                'site_name' => $public_validate['site_name'] ?? null,
                'light_site_name' => $public_validate['light_site_name'] ?? null,
                'site_email' => $public_validate['site_email'] ?? null,
                'whatsapp_link' => $public_validate['whatsapp_link'] ?? null,
                'podcast_url' => $public_validate['podcast_url'] ?? null,
                'claim_url' => $public_validate['claim_url'] ?? null,
                'phone' => $public_validate['phone'] ?? null,
                'address' => $public_validate['address'] ?? null,
                'footer_description' => $public_validate['footer_description'] ?? null,
                'copyright_text' => $public_validate['copyright_text'] ?? null,
            ]);
            $this->handleFileUpdate($settings, 'site_logo', $public_validate['site_logo'] ?? null);
            $this->handleFileUpdate($settings, 'favicon', $public_validate['favicon'] ?? null);
            $this->handleFileUpdate($settings, 'footer_logo', $public_validate['footer_logo'] ?? null);
            $this->handleFileUpdate($settings, 'hodhod_banner', $public_validate['hodhod_banner'] ?? null);
        }
        if ($this->tab == 'open_graph') {
            $public_validate = Validator::make($this->state, [
                'open_graph_image' => 'nullable|integer|exists:files,id',
                'tags' => 'nullable',
                'open_graph_description' => 'nullable',
                'open_graph_local' => 'nullable',
                'open_graph_author' => 'nullable',
                'open_graph_twitter_creator' => 'nullable',
                'open_graph_twitter_site' => 'nullable',
            ])->validate();
            $settings = Setting::with('files')->first();
            Setting::updateOrCreate(
                ['id' => $settings?->id],
                [
                'tags' => $public_validate['tags'] ?? null,
                'open_graph_description' => $public_validate['open_graph_description'] ?? null,
                'open_graph_local' => $public_validate['open_graph_local'] ?? null,
                'open_graph_author' => $public_validate['open_graph_author'] ?? null,
                'open_graph_twitter_creator' => $public_validate['open_graph_twitter_creator'] ?? null,
                'open_graph_twitter_site' => $public_validate['open_graph_twitter_site'] ?? null,
            ]);
            if ($settings->files->where('model_column', 'open_graph_image')->first()) {
                $open_graph_image = $public_validate['open_graph_image'];
                $settings->files()->where('model_column', 'open_graph_image')->update([
                    'file_id' => $open_graph_image,
                ]);
            } else {
                $open_graph_image = $public_validate['open_graph_image'];
                if ($open_graph_image)
                    $settings->files()->create([
                        'file_id' => $open_graph_image,
                        'model_column' => 'open_graph_image',
                        'model_type' => Setting::class
                    ]);
            }
        }
        if ($this->tab == 'extra_code' && auth()->user()->can('settings_extra_codes')) {
            $public_validate = Validator::make($this->state, [
                'header_code' => 'nullable',
                'footer_code' => 'nullable',
            ])->validate();


            Setting::first()->update([
                'header_code' => $public_validate['header_code'] ?? null,
                'footer_code' => $public_validate['footer_code'] ?? null,
            ]);
        }
        if ($this->tab == 'water_mark') {
            $public_validate = Validator::make($this->state, [
                'water_mark_image' => 'nullable|integer|exists:files,id',
                'water_mark_place' => 'nullable|in:' . $this->whereIn()['WaterMarkEnum'],
            ])->validate();
            $settings = Setting::with('files')->first();
            $settings->update([
                'water_mark_place' => $public_validate['water_mark_place'] ?? null,
            ]);
            if ($settings->files->where('model_column', 'water_mark_image')->first()) {
                $water_mark_image = $public_validate['water_mark_image'];
                $settings->files()->where('model_column', 'water_mark_image')->update([
                    'file_id' => $water_mark_image,
                ]);
            } else {
                $water_mark_image = $public_validate['water_mark_image'];
                if ($water_mark_image)
                    $settings->files()->create([
                        'file_id' => $water_mark_image,
                        'model_column' => 'water_mark_image',
                        'model_type' => Setting::class
                    ]);
            }
        }
        if ($this->tab == 'tags' && auth()->user()->can('settings_custom_tags')) {
            $public_validate = Validator::make($this->state, [
                'tags' => 'nullable|string',
            ])->validate();
            $settings = Setting::with('files')->first();
            Setting::updateOrCreate(
                ['id' => $settings?->id],
                [
                'tags' => $public_validate['tags'] ?? null,
            ]);
        }
        if ($this->tab == 'landing_page_information' && env('APP_LUNCH') == 'adani_cast') {
            $public_validate = Validator::make($this->state, [
                'landing_page_url' => 'nullable|url',
                'landingPageInformationPicturesTopLeft' => 'nullable|integer|exists:files,id',
                'landingPageInformationPicturesTopRight' => 'nullable|integer|exists:files,id',
                'landingPageInformationPicturesBottomLeft' => 'nullable|integer|exists:files,id',
                'landingPageInformationPicturesBottomRight' => 'nullable|integer|exists:files,id',
            ])->validate();
            $settings = Setting::with('files')->first();
            Setting::updateOrCreate(
                ['id' => $settings?->id],
                [
                'landing_page_url' => $public_validate['landing_page_url'] ?? null,
            ]);
            $settings?->files()?->where('model_column', 'landingPageInformationPicturesTopLeft')->delete();
            $settings?->files()?->where('model_column', 'landingPageInformationPicturesTopRight')->delete();
            $settings?->files()?->where('model_column', 'landingPageInformationPicturesBottomLeft')->delete();
            $settings?->files()?->where('model_column', 'landingPageInformationPicturesBottomRight')->delete();
            if (isset($public_validate['landingPageInformationPicturesTopLeft'])) {
                $settings->files()->create([
                    'file_id' => $public_validate['landingPageInformationPicturesTopLeft'],
                    'model_type' => Setting::class,
                    'model_column' => 'landingPageInformationPicturesTopLeft',
                ]);
            }
            if (isset($public_validate['landingPageInformationPicturesTopRight'])) {
                $settings->files()->create([
                    'file_id' => $public_validate['landingPageInformationPicturesTopRight'],
                    'model_type' => Setting::class,
                    'model_column' => 'landingPageInformationPicturesTopRight',
                ]);
            }
            if (isset($public_validate['landingPageInformationPicturesBottomLeft'])) {
                $settings->files()->create([
                    'file_id' => $public_validate['landingPageInformationPicturesBottomLeft'],
                    'model_type' => Setting::class,
                    'model_column' => 'landingPageInformationPicturesBottomLeft',
                ]);
            }
            if (isset($public_validate['landingPageInformationPicturesBottomRight'])) {
                $settings->files()->create([
                    'file_id' => $public_validate['landingPageInformationPicturesBottomRight'],
                    'model_type' => Setting::class,
                    'model_column' => 'landingPageInformationPicturesBottomRight',
                ]);
            }
        }

        $this->dispatch('settingUpdated');
    }


    protected function handleFileUpdate(Setting $settings, string $column, ?int $fileId): void
    {
        $filePath = $fileId ? Files::find($fileId)?->path : null;


        if ($settings->files->where('model_column', $column)->first()) {
            if (isset($fileId)) {
                $settings?->files()->where('model_column', $column)->update([
                    'file_id' => $fileId,
                ]);
            } else {
                $settings?->files()->where('model_column', $column)->delete();
            }
        } else {
            if ($fileId) {
                $settings?->files()->create([
                    'file_id' => $fileId,
                    'model_column' => $column,
                    'model_type' => Setting::class
                ]);
            }
        }
    }
}
