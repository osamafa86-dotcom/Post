@php use App\Enums\CategoryTypeEnum; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="categoryForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? 'bg-warning' : 'bg-primary'}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __('messages.categories.edit_category') : __('messages.categories.add_category')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateCategory' : 'createCategory'}}">
                    <div class="row">
                        <div class="form-group col-md-12 mb-3">
                            <label for="category_title">{{ __('messages.categories.category_title') }}</label>
                            <input wire:model="state.category_title" type="text"
                                   class="form-control @error('category_title') is-invalid @enderror"
                                   id="category_title"
                                   placeholder="{{ __('messages.categories.category_title') }}">
                            @error('category_title')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="author_image">{{ __('messages.categories.image') }}</label>
                            <br>
                            <div class="position-relative mb-3"
                                 style="display: inline-block; width: 100%; max-width: 150px;">
                                <!-- الصورة -->
                                <img
                                    src="{{ isset($state['category_image_name']) ? file_url($state['category_image_name']) : 'https://dummyimage.com/'. config('features.image_sizes.category_image') .'/dddddd/000000' }}"
                                    alt="{{ __('messages.categories.image') }}"
                                    id="imagePreview" class="img-fluid border"
                                    style="cursor: pointer; border-radius: 8px;"
                                    onclick="Livewire.dispatch('isMultiple', { active: false });Livewire.dispatch('typeDefined', { type: 'images' });$('#fileLibraryModal').modal('show')">

                                <!-- زر الإغلاق أعلى الصورة -->
                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close"
                                        style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"
                                        wire:click.prevent="clearColumn('category_image')"></button>
                            </div>
                            @error('category_image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label
                                for="category_description">{{ __('messages.categories.category_description') }}</label>
                            <textarea wire:model="state.category_description"
                                      class="form-control @error('category_description') is-invalid @enderror"
                                      id="category_description"
                                      placeholder="{{ __('messages.categories.category_description') }}..."></textarea>
                            @error('category_description')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        {{--                        <div class="form-group col-md-12 mb-3">--}}
                        {{--                            <label for="parent_id">{{ __('messages.categories.category_section') }}</label>--}}
                        {{--                            <select class="form-control @error('parent_id') is-invalid @enderror"--}}
                        {{--                                    wire:model="state.parent_id"--}}
                        {{--                                    id="parent_id">--}}
                        {{--                                <option>{{ __('messages.choose') }}</option>--}}
                        {{--                                @foreach($this->parents as $row)--}}
                        {{--                                    <option value="{{$row->id}}">{{$row->category_title}}</option>--}}
                        {{--                                @endforeach--}}
                        {{--                            </select>--}}
                        {{--                            @error('parent_id')--}}
                        {{--                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>--}}
                        {{--                            @enderror--}}
                        {{--                        </div>--}}
                        <div class="form-group col-md-12 mb-3">
                            <label for="category_type">{{ __('messages.categories.category_type') }}</label>
                            <select class="form-control @error('category_type') is-invalid @enderror"
                                    wire:model="state.category_type"
                                    id="category_type">
                                <option>{{ __('messages.choose') }}</option>
                                @foreach(CategoryTypeEnum::available() as $row)
                                    <option value="{{$row->value}}">{{$row->label()}}</option>
                                @endforeach
                            </select>
                            @error('category_type')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>

                        @php
                            $allowedFor = ['tayqan', 'maktoob']
                        @endphp
                        @if (in_array(config('app.launch'),$allowedFor))
                            <div class="form-group col-md-12 mb-3">
                                <label for="category_color">Category Color</label>
                                <input wire:model="state.category_color" type="color"
                                       class="form-control @error('category_color') is-invalid @enderror"
                                       id="category_color"
                                       placeholder="Category Color">
                                @error('category_color')
                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit"
                                class="btn {{$showEdit ? 'btn-warning' : 'btn-primary'}}">{{ __('messages.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="categoryDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{ __('messages.categories.delete_category') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('messages.categories.confirm_delete_category') }}</h4>
                <h6>{{ __('messages.categories.delete_category_message' , ['title' => $Categories_->category_title ?? __('messages.no_data')])}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button wire:click="categoryDeleteConfirm"
                            class="btn btn-danger">{{ __('messages.confirm') }}</button>
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
                    id="staticBackdropLabel">{{__('messages.categories.delete_categories')}}   </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.categories.confirm_delete_categories')}}</h4>
                <h6>{{__('messages.categories.delete_categories_message')}}</h6>
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
