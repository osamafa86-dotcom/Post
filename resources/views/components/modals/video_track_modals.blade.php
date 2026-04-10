<!--Create/Edit Modal -->
<div class="modal fade" id="trackForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? 'bg-warning' : 'bg-primary'}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.video_tracks.edit_video') : __('messages.video_tracks.add_video')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateTrack' : 'createTrack'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="title">{{ __('messages.video_tracks.video_title') }}</label>
                            <input wire:model="state.title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title">
                            @error('title')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="image">{{ __('messages.video_tracks.video_image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <img
                                    src="{{ isset($state['image_name']) ? file_url($state['image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.video_image') .'/dddddd/000000' }}"
                                    alt="{{ __('messages.video_tracks.video_image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <!-- زر الإغلاق أعلى الصورة -->
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                            </div>
                            @error('image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="description">{{ __('messages.video_tracks.video_description') }}</label>
                            <textarea wire:model="state.description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      id="description"></textarea>
                            @error('description')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="url">{{ __('messages.video_tracks.video_url') }}</label>
                            <input wire:model="state.url" type="url"
                                   class="form-control @error('url') is-invalid @enderror"
                                   id="url">
                            @error('url')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="video_album_id">{{ __('messages.video_tracks.albums') }}</label>
                            <select class="form-control @error('video_album_id') is-invalid @enderror"
                                    wire:model="state.video_album_id"
                                    id="video_album_id">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach($this->albums as $row)
                                    <option value="{{$row->id}}">{{$row->title}}</option>
                                @endforeach
                            </select>
                            @error('video_album_id')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn {{$showEdit ? 'btn-warning' : 'btn-primary'}}">{{ __('messages.confirm') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="trackDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.video_tracks.delete_video') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.video_tracks.confirm_delete_video') }}</h4>
                <h6>{{ __('messages.video_tracks.delete_video_message') }}: {{$Tracks_->title ?? __('messages.not_available')}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="trackDeleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>
