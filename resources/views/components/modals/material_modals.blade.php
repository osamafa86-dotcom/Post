@php use App\Enums\MaterialTypeEnum;use App\Enums\VideoTypeEnum;use Illuminate\Support\Str; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="addform" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.materials.edit_material') : __('messages.materials.add_material')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'update' : 'create'}}">
                    <div class="row">

                        <div class="form-group col-md-12 mb-3">
                            <label for="author_image">{{ __('messages.materials.image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <!-- الصورة -->
                                <img
                                    src="{{ isset($state['image_name']) ? file_url($state['image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.material.main_image') .'/dddddd/000000' }}"
                                    alt="{{ __('messages.materials.image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px; width: 100%; object-fit: cover;"
                                    onclick="Livewire.dispatch('columnDefined', { column: 'image' });Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <!-- زر الإغلاق أعلى الصورة -->
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close" wire:click.prevent="clearColumn('image')"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                            </div>
                            @error('image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="title">{{ __('messages.materials.title') }}</label>
                            <input wire:model="state.title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   placeholder="{{ __('messages.materials.title') }}">
                            @error('title')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="type">{{ __('messages.materials.type') }}</label>
                            <select class="form-control @error('type') is-invalid @enderror"
                                    wire:model.live="state.type"
                                    id="type">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(MaterialTypeEnum::available() as $row)
                                    <option value="{{$row->value}}">{{ MaterialTypeEnum::fromValue($row->value) }}</option>
                                @endforeach
                            </select>
                            @error('type')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        @if(!empty($state['type']) && $state['type'] == MaterialTypeEnum::VIDEO->value)
                            <div class="form-group col-md-12 mb-3">
                                <div class="row">
                                    @foreach(VideoTypeEnum::available() as $row)
                                        <div class="form-check col-lg-4 mb-1">
                                            <input class="form-check-input" type="radio"
                                                   wire:model.live="state.video_type"
                                                   id="{{$row->value}}" value="{{$row->value}}">
                                            <label class="form-check-label"
                                                   for="{{$row->value}}">{{ $row->label() }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('image_size')
                                <div class="text-danger">{{ $message }}</div> @enderror
                            </div>



                            @if(!empty($state['video_type']) && $state['video_type'] != VideoTypeEnum::LOCAL->value)
                                <div class="form-group col-md-12 mb-3">
                                    <label for="link">{{ __('messages.materials.link') }}</label>
                                    <input wire:model="state.link" type="url"
                                           class="form-control @error('link') is-invalid @enderror"
                                           id="link"
                                           placeholder="{{ __('messages.materials.link') }}">
                                    @error('link')
                                    <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- الكاتب -->

                            @endif
                        @endif
                        @php
                            $allowed_with_video = !empty($state['type']) && !empty($state['video_type']) && $state['type'] == MaterialTypeEnum::VIDEO->value && $state['video_type'] == VideoTypeEnum::LOCAL->value;
                            $allowed = !empty($state['type']) && $state['type'] != MaterialTypeEnum::VIDEO->value;
                        @endphp
                        <div class="form-group col-md-12 mb-3"
                             @if($allowed_with_video || $allowed) @else hidden="" @endif>
                            <label for="file">{{ __('messages.materials.file') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <!-- الصورة -->
                                @php
                                    $type = isset($state['type']) && $state['type'] == MaterialTypeEnum::PODCAST->value ? 'audio' : (isset($state['type']) && $state['type'] == MaterialTypeEnum::VIDEO->value && isset($state['video_type']) && $state['video_type'] == VideoTypeEnum::LOCAL->value ? 'videos' : 'images');

                                    $isAudioFile = isset($state['file_name']) && (Str::endsWith(file_url($state['file_name']), '.mp3') || Str::endsWith(file_url($state['file_name']), '.MP3'));
                                    $isVideoFile = isset($state['file_name']) && (Str::endsWith(file_url($state['file_name']), '.mp4') || Str::endsWith(file_url($state['file_name']), '.MP4'));
                                    $isMediaIcon = $isAudioFile || $isVideoFile;

                                    if ($isAudioFile) {
                                        $src_image = asset('assets/media/svg/audio-icon.svg');
                                    } elseif ($isVideoFile) {
                                        $src_image = asset('assets/media/svg/video-icon.svg');
                                    } else {
                                        $src_image = isset($state['file_name']) ? file_url($state['file_name']) : 'https://dummyimage.com/'. config('features.image_sizes.material.main_image') .'/dddddd/000000';
                                    }
                                @endphp

                                <div class="d-flex align-items-center justify-content-center position-relative"
                                     style="cursor: pointer; border-radius: 12px; width: 100%; height: 150px; {{ $isMediaIcon ? 'background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%); border: 1px solid #e2e8f0;' : '' }}"
                                     onclick="Livewire.dispatch('columnDefined', { column: 'file' });Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: '{{$type}}' });$('#fileLibraryModal').modal('show')">
                                    <img
                                        src="{{ $src_image }}"
                                        alt="{{ __('messages.materials.file') }}"
                                        id="filePreview" class="{{ $isMediaIcon ? '' : 'img-fluid border' }}"
                                        style="{{ $isMediaIcon ? 'width: 70px; height: 70px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.15));' : 'border-radius: 12px; width: 100%; height: 100%; object-fit: cover;' }}">
                                </div>

                                <!-- زر الإغلاق أعلى الصورة -->
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close" wire:click.prevent="clearColumn('file')"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                            </div>
                            @error('file')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror

                            <div>
                                <label for="presenter_id" class="mt-3">{{ __('messages.materials.presenters') }}</label>
                                <div class="d-flex align-items-center" wire:ignore>
                                    <select class="form-select"
                                            wire:model.live="state.presenter_id"
                                            id="presenter_id"
                                            multiple
                                            data-control="select2"
                                            data-placeholder="{{ __('messages.choose') }}">
                                        @foreach($this->presenters as $presenter)
                                            <option value="{{ $presenter['id'] }}">{{ $presenter['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('presenter_id')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="guest_id" class="mt-3">{{ __('messages.materials.guests') }}</label>
                                <div class="d-flex align-items-center" wire:ignore>
                                    <select class="form-select"
                                            wire:model.live="state.guest_id"
                                            id="guest_id"
                                            multiple
                                            data-control="select2"
                                            data-placeholder="{{ __('messages.choose') }}">
                                        @foreach($this->guests as $guest)
                                            <option value="{{ $guest['id'] }}">{{ $guest['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('guest_id')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="description">{{ __('messages.materials.description') }}</label>
                            <input wire:model="state.description" type="text"
                                   class="form-control @error('description') is-invalid @enderror"
                                   id="description"
                                   placeholder="{{ __('messages.materials.description') }}">
                            @error('description')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="mt-3">{{ __('messages.materials.category') }}</label>
                            <div class="d-flex align-items-center" wire:ignore>
                                <select class="form-select"
                                        wire:model.live="state.category_id"
                                        id="category_id"
                                        multiple
                                        data-control="select2"
                                        data-placeholder="{{ __('messages.choose') }}">
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category['id'] }}">{{ $category['category_title'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        @error('category_id')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                        <div class="mb-3" wire:ignore>
                            <div>
                                <label for="tags">{{ __('messages.materials.tags') }}</label>
                                <input class="form-control @error('tags') is-invalid @enderror"
                                       id="tags" name="tags"
                                       wire:model.live="state.tags">
                            </div>

                        </div>
                        @error('tags')
                        <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                        @enderror
                        <div class="form-group col-md-12 mb-3">
                            <label for="album_id">{{ __('messages.materials.album') }}</label>
                            <select class="form-control @error('album_id') is-invalid @enderror"
                                    wire:model="state.album_id"
                                    id="album_id">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach($this->albums as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                            @error('album_id')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit"
                                class="btn {{$showEdit ? "btn-warning" : "btn-primary"}}">{{ __('messages.confirm') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="delete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{ __('messages.materials.delete_material') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.materials.confirm_delete_material') }}</h4>
                <h6>{{ __('messages.materials.delete_material_message')}}
                    : {{ $Materials_->title ?? __('messages.no_data') }}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="deleteSelected" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{__('messages.materials.delete_materials')}}   </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.materials.confirm_delete_materials')}}</h4>
                <h6>{{__('messages.materials.delete_materials_message')}}</h6>
                <div class="form-group col-md-12 mb-3">
                    <input wire:model="delete_text"
                           class="form-control"
                           id="category_description"
                    >
                </div>
                <!-- Error Message -->
                @if (session()->has('error'))
                    <div style="color: red; padding: 10px;">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="confirmDeleteSelected"
                            class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
