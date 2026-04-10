@php use App\Enums\MaterialTypeEnum; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="addform" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.albums.edit_album') : __('messages.albums.add_album')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'update' : 'create'}}">
                    <div class="row">

                        <div class="form-group col-md-12 mb-3">
                            <label for="author_image">{{ __('messages.albums.image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <!-- الصورة -->
                                <img
                                    src="{{ isset($state['image_name']) ? file_url($state['image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.material_album.main_image') .'/dddddd/000000' }}"
                                    alt="{{ __('messages.albums.image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

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
                            <label for="name">{{ __('messages.albums.name') }}</label>
                            <input wire:model="state.name" type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   placeholder="{{ __('messages.albums.name') }}">
                            @error('name')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="form-group col-md-12 mb-3">
                            <label for="description">{{ __('messages.albums.description') }}</label>
                            <input wire:model="state.description" type="text"
                                   class="form-control @error('description') is-invalid @enderror"
                                   id="description"
                                   placeholder="{{ __('messages.albums.description') }}">
                            @error('description')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="type">{{ __('messages.albums.type') }}</label>
                            <select class="form-control @error('type') is-invalid @enderror"
                                    wire:model.live="state.type"
                                    id="type">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(MaterialTypeEnum::available() as $row)
                                    <option value="{{$row->value}}">{{$row->label()}}</option>
                                @endforeach
                            </select>
                            @error('type')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="mt-3">{{ __('messages.albums.category') }}</label>
                            <div class="d-flex align-items-center" wire:ignore>
                                <select class="form-select"
                                        wire:model.live="state.category_id"
                                        id="category_id"
                                        multiple
                                        data-control="select2"
                                        data-placeholder="{{ __('messages.choose') }}">
                                    @foreach($this->categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div wire:ignore>
                                <label for="tags">{{ __('messages.albums.tags') }}</label>
                                <input class="form-control @error('tags') is-invalid @enderror"
                                       id="tags" name="tags"
                                       wire:model.live="state.tags">
                            </div>
                            @error('tags')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 my-3">
                            <a class="btn btn-sm btn-success" wire:click="addSocialMedia">
                                <span>{{__('messages.albums.add_link')}}</span>
                                <i class="bi bi-plus-circle fs-6"></i>
                            </a>
                        </div>
                        @foreach($links as $key => $row)
                            <h3 class="mb-2 text-primary">{{ __('messages.albums.link').'('.$key+1 .')' }}</h3>
                            <div class="form-group col-md-6 mb-3"
                                 wire:key="links.{{$key}}.title">
                                <label for="links.{{$key}}.title">{{__('messages.albums.link_title')}} </label>
                                <input wire:model="links.{{$key}}.title" type="text"
                                       class="form-control @error("$key.title") is-invalid @enderror"
                                       id="links.{{$key}}.title"
                                       placeholder="{{__('messages.albums.link_title')}} ">
                                @error("$key.title")
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 mb-3"
                                 wire:key="links.{{$key}}.url">
                                <label for="links.{{$key}}.url"> {{__('messages.albums.link_url')}}</label>
                                <input wire:model="links.{{$key}}.url" type="url"
                                       class="form-control @error("$key.url") is-invalid @enderror"
                                       id="links.{{$key}}.url">
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
                                            #{{__('messages.albums.link_icon')}}
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse my-3"
                                         aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @if($this->icons)
                                                    @foreach($this->icons as $row)
                                                        <div
                                                            class="col-md-3 text-center gap-2 mb-3 @if(!empty($links[$key]['icon_id']) && $links[$key]['icon_id'] == $row->id) border border-primary rounded @endif"
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
                                @error('$key.link_icon')
                                <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6" style="margin-top: 25px;">
                                <a class="btn btn-sm btn-danger" wire:click="removeLink('{{$key}}')">
                                    <span>{{__('messages.remove')}}</span>
                                    <i class="bi bi-dash-circle fs-6"></i>
                                </a>
                            </div>
                        @endforeach

                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn {{$showEdit ? "btn-warning" : "btn-primary"}}">{{ __('messages.confirm') }}</button>
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{ __('messages.albums.delete_album') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.albums.confirm_delete_album') }}</h4>
                <h6>{{ __('messages.albums.delete_album_message')}}: {{ $MaterialAlbums_->name ?? __('messages.no_data') }}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="deleteConfirm" class="btn btn-danger">{{ __('messages.confirm') }}</button>
                </div>
            </div>

        </div>
    </div>
</div>
