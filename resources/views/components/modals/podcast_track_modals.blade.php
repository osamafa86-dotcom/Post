<!--Create/Edit Modal -->
<div class="modal fade" id="trackForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? 'bg-warning' : 'bg-primary'}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.podcast_tracks.edit_podcast') : __('messages.podcast_tracks.add_podcast')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.close') }}"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateTrack' : 'createTrack'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="title">{{ __('messages.podcast_tracks.podcast_title') }}</label>
                            <input wire:model="state.title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title">
                            @error('title')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="image">{{ __('messages.podcast_tracks.podcast_image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <img
                                    src="{{ isset($state['image_name']) ? file_url($state['image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.podcast_track') .'/dddddd/000000' }}"
                                    alt="{{ __('messages.podcast_tracks.podcast_image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('columnDefined', { column: 'image' });Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="{{ __('messages.close') }}"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                            </div>
                            @error('image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="description">{{ __('messages.podcast_tracks.podcast_description') }}</label>
                            <textarea wire:model="state.description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      id="description"></textarea>
                            @error('description')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                            <label for="pdf_url" class="mt-3">{{ __('messages.podcast_tracks.podcast_url') }}</label>
                            <div class="input-group mb-3">
                                @if(!isset($state['url_id']))
                                    <input type="url" wire:model.live="state.url" class="form-control"
                                           placeholder="{{ __('messages.podcast_tracks.podcast_url') }}" id="url">
                                @else
                                    <input type="text" class="form-control"
                                           value="{{$this->state['url_id_name'] ?? null}}" disabled>
                                    <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                            aria-label="{{ __('messages.close') }}"
                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"
                                            wire:click.prevent="clearColumn('url_id')"></button>
                                @endif
                                <button class="btn btn-primary" type="button"
                                        onclick="Livewire.dispatch('columnDefined', { column: 'url_id' });Livewire.dispatch('typeDefined', { type: 'audio' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');">
                                    {{isset($state['url_id']) ? __('messages.edit') : __('messages.podcast_tracks.file_library')}}
                                </button>
                            </div>
                            @error('url')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                            @error('url_id')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                            <div class="form-group col-md-12 mb-3">
                                <label for="podcast_album_id">{{ __('messages.podcast_tracks.albums') }}</label>
                                <select class="form-control @error('podcast_album_id') is-invalid @enderror"
                                        wire:model="state.podcast_album_id"
                                        id="podcast_album_id">
                                    <option>{{ __('messages.choose') }}</option>
                                    @foreach($this->albums as $row)
                                        <option value="{{$row->id}}">{{$row->title}}</option>
                                    @endforeach
                                </select>
                                @error('podcast_album_id')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer p-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="btn {{$showEdit ? 'btn-warning' : 'btn-primary'}}">{{ __('messages.confirm') }}</button>
                        </div>
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.podcast_tracks.delete_podcast') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="{{ __('messages.close') }}"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.podcast_tracks.confirm_delete_podcast') }}</h4>
                <h6>{{ __('messages.podcast_tracks.delete_podcast_message', ['title' => $Tracks_->title ?? __('messages.not_available')]) }}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="trackDeleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>
