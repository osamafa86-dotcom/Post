<?php

namespace App\Livewire\Dashboard\Advertisements;

use App\Models\Advertisement;
use App\Models\Files;
use App\Traits\WhereIn;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateUpdateAdvertisement extends Component
{
    use WithFileUploads, WhereIn;

    public array $state = [];
    public ?object $Advertisement;
    public bool $showEdit = false;
    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.advertisements.create-update-advertisement');
    }

    #[On('imageSelected')]
    public function imageSelected($id): void
    {
        $this->state['image'] = $id;
        $this->state['image_name'] = Files::where('id', $id)->value('path');
    }

    public function clearColumn($column, $value = null): void
    {

        if (!isset($value)) {
            $this->state[$column] = null;
            $this->state[$column . '_name'] = null;
        } else {
            if (($key = array_search($value, $this->state[$column])) !== false) {
                unset($this->state[$column][$key]);
            }
            $this->state[$column] = array_values($this->state[$column]);
            unset($this->state[$column . '_name'][$value]);
        }
    }
    public function mount($advertisement_id = null): void
    {
        $this->state['type'] = "صورة";
        if ($advertisement_id) {
            $this->Advertisement = Advertisement::where('id', $advertisement_id)->with('files')->first();
            $this->state = [
                "id" => $this->Advertisement->id ?? null,
                'title' => $this->Advertisement->title ?? null,
                "image" => $this->Advertisement->files->file_id ?? null,
                "image_name" => isset($this->Advertisement->files->file_id) ? Files::where('id', $this->Advertisement->files->file_id)->value('path') : null,
                "type" => $this->Advertisement->type ?? null,
                "place" => $this->Advertisement->place ?? null,
                "url" => $this->Advertisement->url ?? null,
                "url_target" => $this->Advertisement->url_target ?? null,
                "code" => $this->Advertisement->code ?? null,
                "end_hour_time" => $this->Advertisement->end_hour_time ?? null,
                "end_min_time" => $this->Advertisement->end_min_time ?? null,
                "user_id" => $this->Advertisement->user_id ?? null,
                "publish_status" => $this->Advertisement->publish_status ?? null,
            ];
            $this->showEdit = true;
        }
    }

    /**
     * @throws ValidationException
     */
    public function createAdvertisement(): void
    {
        // Validate the input data
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:200',
            'image' => 'nullable|integer|exists:files,id',
            'type' => 'required|in:' . $this->whereIn()['AdvertisementTypeEnum'],
            'place' => 'required|in:' . $this->whereIn()['AdvertisementPlaceEnum'],
            'url' => 'nullable|url',
            'url_target' => 'nullable|in:' . $this->whereIn()['AdvertisementUrlTargetEnum'],
            'code' => 'nullable',
//            'end_hour_time' => 'nullable:end_min_time|integer|nullable',
//            'end_min_time' => 'nullable:end_hour_time|integer|nullable',
        ])->validate();
        // Capture the user ID
        $validation['user_id'] = Auth::id();
        // Create the advertisement
        $Advertisement = Advertisement::create([
            'title' => $validation['title'] ?? null,
            'image' => $validation['image'] ?? null,
            'type' => $validation['type'] ?? null,
            'place' => $validation['place'] ?? null,
            'url' => $validation['url'] ?? null,
            'url_target' => $validation['url_target'] ?? null,
            'code' => $validation['code'] ?? null,
//            'end_hour_time' => $validation['end_hour_time'] ?? null,
//            'end_min_time' => $validation['end_min_time'] ?? null,
            'user_id' => $validation['user_id'] ?? null,
            'publish_status' => $validation['publish_status'] ?? null,
        ]);

        if(!Empty($validation['image']))
        {
            $Advertisement->files()->create([
                'file_id' => $validation['image'] ?? null,
                'model_type' => Advertisement::class,
                'model_column' => 'image'
            ]);
        }


        $Advertisement->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::CREATE->value,
        ]);
        $this->state = [];
        $this->dispatch('advertisementCreated');
    }

    /**
     * @throws ValidationException
     */
    public function updateAdvertisement(): void
    {
        // Validate the input data
        $validation = Validator::make($this->state, [
            'title' => 'required|string|max:200',
            'image' => 'required|integer|exists:files,id',
            'type' => 'required|in:' . $this->whereIn()['AdvertisementTypeEnum'],
            'place' => 'required|in:' . $this->whereIn()['AdvertisementPlaceEnum'],
            'url' => 'nullable|url',
            'url_target' => 'nullable|in:' . $this->whereIn()['AdvertisementUrlTargetEnum'],
            'code' => 'nullable',
//            'end_hour_time' => 'required_without:end_min_time|integer|nullable',
//            'end_min_time' => 'required_without:end_hour_time|integer|nullable',
        ])->validate();

        $before_data = $this->Advertisement->toArray();

        // Create the advertisement
        $this->Advertisement->update([
            'title' => $validation['title'] ?? null,
            'image' => $validation['image'] ?? null,
            'type' => $validation['type'] ?? null,
            'place' => $validation['place'] ?? null,
            'url' => $validation['url'] ?? null,
            'url_target' => $validation['url_target'] ?? null,
            'code' => $validation['code'] ?? null,
//            'end_hour_time' => $validation['end_hour_time'] ?? null,
//            'end_min_time' => $validation['end_min_time'] ?? null,
            'publish_status' => $validation['publish_status'] ?? null,
        ]);
        $this->Advertisement->files()->update([
            'file_id' => $validation['image'] ?? null
        ]);

        $new_data = $this->Advertisement->toArray();

        $this->Advertisement->user_logs()->create([
            'user_id' => Auth::id(),
            'action_status' => \App\Enums\ActionsEnum::EDIT->value,
            'actionable_data_before' => json_encode($before_data),
            'actionable_data_after' => json_encode($new_data) ,
        ]);
        $this->state = [];
        $this->dispatch('advertisementCreated');
    }
}
