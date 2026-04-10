@php use App\Enums\ReelTypeEnum;use Illuminate\Support\Str; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="reelForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ?  __('messages.reels.add_reel') :  __('messages.reels.edit_reel')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateReel' : 'createReel'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="reel_title">  {{__('messages.reels.reel_title')}}</label>
                            <input wire:model="state.reel_title" type="text"
                                   class="form-control @error('reel_title') is-invalid @enderror"
                                   id="reel_title"
                                   placeholder="{{__('messages.reels.reel_title')}}">
                            @error('reel_title')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="reel_type">{{__('messages.reels.type')}}</label>
                            <select class="form-control @error('reel_type') is-invalid @enderror"
                                    wire:model.live="state.reel_type"
                                    id="reel_type">
                                <option>{{__('messages.choose')}}</option>
                                @foreach(ReelTypeEnum::cases() as $row)
                                    <option value="{{$row->value}}">{{$row->label()}}</option>
                                @endforeach
                            </select>
                            @error('reel_type')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(!empty($state['reel_type']) && $state['reel_type'] != ReelTypeEnum::LOCAL->value)
                            <div class="form-group col-md-12 mb-3">
                                <label for="reel_url">{{__('messages.reels.reel_url')}}</label>
                                <input wire:model="state.reel_url" type="text"
                                       class="form-control @error('reel_url') is-invalid @enderror"
                                       id="reel_url"
                                       placeholder=" {{__('messages.reels.reel_url')}}">
                                @error('reel_url')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if(!empty($state['reel_type']) && $state['reel_type'] == ReelTypeEnum::LOCAL->value)
                            <div class="form-group col-md-12 mb-3">
                                <label for="file">{{ __('messages.reels.file') }}</label>
                                <br>
                                <div class="position-relative mb-3"
                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                    @php
                                        $isVideoFile = isset($state['file_name']) && Str::endsWith($state['file_name'], '.mp4');
                                        if ($isVideoFile) {
                                            $src_image = asset('assets/media/svg/video-icon.svg');
                                        } else {
                                            $src_image = isset($state['file_name']) ? file_url($state['file_name']) : 'https://dummyimage.com/150x150/dddddd/000000';
                                        }
                                    @endphp

                                    <div class="d-flex align-items-center justify-content-center position-relative"
                                         style="cursor: pointer; border-radius: 12px; width: 100%; height: 150px; {{ $isVideoFile ? 'background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%); border: 1px solid #e2e8f0;' : '' }}"
                                         onclick="Livewire.dispatch('columnDefined', { column: 'file' });Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'videos' });$('#fileLibraryModal').modal('show')">
                                        <img
                                            src="{{ $src_image }}"
                                            alt="{{ __('messages.reels.file') }}"
                                            id="filePreview" class="{{ $isVideoFile ? '' : 'img-fluid border' }}"
                                            style="{{ $isVideoFile ? 'width: 70px; height: 70px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.15));' : 'border-radius: 12px; width: 100%; height: 100%; object-fit: cover;' }}">
                                    </div>

                                    <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                            aria-label="Close" wire:click.prevent="clearColumn('file')"
                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                </div>
                                @error('file')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="form-group col-md-12 mb-3">
                            <label for="poster">{{ __('messages.reels.poster') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                @php
                                    $poster_image = isset($state['poster_name']) ? file_url($state['poster_name']) : 'https://dummyimage.com/150x150/dddddd/000000' ;
                                @endphp

                                <img
                                    src="{{ $poster_image }}"
                                    alt="{{ __('messages.reels.poster') }}"
                                    id="posterPreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px; width: 100%; object-fit: cover;"
                                    onclick="Livewire.dispatch('columnDefined', { column: 'poster' });Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close" wire:click.prevent="clearColumn('poster')"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                            </div>
                            @error('poster')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button type="submit" class="btn {{$showEdit ? "btn-warning" : "btn-primary"}}">{{__('messages.confirm')}}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!--Delete Modal -->
<div class="modal fade" id="reelDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.reels.delete_reel')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.reels.confirm_delete_reel')}}</h4>

                <h6>{{ __('messages.reels.delete_reel_message') }} : {{$reels_->reel_title ?? __('messages.no_data')}}</h6>

                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="reelDeleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>

