<!--Create/Edit Modal -->
<div class="modal fade" id="albumForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? 'bg-warning' : 'bg-primary'}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.podcast_albums.edit_album') : __('messages.podcast_albums.add_album')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{__('messages.close')}}"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateAlbum' : 'createAlbum'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="title">{{__('messages.podcast_albums.album_title')}}</label>
                            <input wire:model="state.title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title">
                            @error('title')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="image">{{__('messages.podcast_albums.album_image')}}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <img
                                    src="{{ isset($state['image_name'])
        ? file_url($state['image_name'])
        : 'https://dummyimage.com/' . config('features.image_sizes.podcast_album') . '/dddddd/000000' }}"
                                    alt="{{__('messages.podcast_albums.album_image')}}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('columnDefined', { column: 'image' });Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' }); $('#fileLibraryModal').modal('show');">
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="{{__('messages.close')}}"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                            </div>
                            @error('image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="description">{{__('messages.podcast_albums.album_description')}}</label>
                            <textarea wire:model="state.description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      id="description"></textarea>
                            @error('description')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Social Links -->
                        <div class="form-group col-md-12 my-3">
                            <a class="btn btn-sm btn-success" wire:click="addAlbumLinks">
                                <span>{{__('messages.podcast_albums.add_album_link')}}</span>
                                <i class="bi bi-plus-circle fs-6"></i>
                            </a>
                        </div>
                        @foreach($album_links as $key => $row)
                            <div class="row my-3">
                                <h3 class="mb-2 text-primary">{{  __('messages.podcast_albums.link').'('.$key+1 .')' }}</h3>
                                <div class="form-group col-md-6 mb-3"
                                     wire:key="album_links.{{$key}}.title">
                                    <label
                                        for="album_links.{{$key}}.title">{{__('messages.podcast_albums.album_link_title')}} </label>
                                    <input wire:model="album_links.{{$key}}.title" type="text"
                                           class="form-control @error("$key.title") is-invalid @enderror"
                                           id="album_links.{{$key}}.title"
                                    >
                                    @error("$key.title")
                                    <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6 mb-3"
                                     wire:key="album_links.{{$key}}.url">
                                    <label
                                        for="album_links.{{$key}}.url">{{__('messages.podcast_albums.album_link_url')}}</label>
                                    <input wire:model="album_links.{{$key}}.url" type="url"
                                           class="form-control @error("$key.url") is-invalid @enderror"
                                           id="album_links.{{$key}}.url">
                                    @error("$key.url")
                                    <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne" aria-expanded="false"
                                                    aria-controls="collapseOne">
                                                #الأيقونة
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse my-3"
                                             aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @if($this->icons)
                                                        @foreach($this->icons as $row)
                                                            <div
                                                                class="col-md-3 text-center gap-2 mb-3 @if(!empty($album_links[$key]['icon_id']) && $album_links[$key]['icon_id'] == $row->id) border border-primary rounded @endif"
                                                                style="cursor: pointer;padding: 5px;"
                                                                wire:click="iconSelect({{$key}} ,{{$row}})">
                                                                <img width="50"
                                                                     src="{{file_url($row->icon_path)}}"
                                                                     alt="icon">
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('icon_id')
                                    <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <a class="btn btn-sm btn-danger" wire:click="removeAlbumLinks('{{$key}}')">
                                        <span>{{__('messages.remove')}}</span>
                                        <i class="bi bi-dash-circle fs-6"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button type="submit"
                                class="btn {{$showEdit ? 'btn-warning' : 'btn-primary'}}">{{__('messages.confirm')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Modal -->
<div class="modal fade" id="albumDelete" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">{{__('messages.podcast_albums.delete_album')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="{{__('messages.close')}}"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.podcast_albums.confirm_delete_album')}}</h4>
                <h6>{{__('messages.podcast_albums.delete_album_message', ['title' => $Albums_->title ?? __('messages.no_data')])}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="albumDeleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
