@php use App\Enums\LinkStatusEnum; @endphp

    <!-- ==================== Create / Edit Navbar Link ==================== -->
<div class="modal fade" id="navbar_linkForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="navbarLinkFormLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg"> {{-- wider for better rhythm --}}
        <div class="modal-content devlo-modal">
            <!-- Header -->
            <div class="modal-header devlo-modal__header">
                <h5 class="modal-title" id="navbarLinkFormLabel">
                    {{ $parent ? __('messages.navbar_links.add_sub_link') : __('messages.navbar_links.add_main_link') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('messages.cancel') }}"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form wire:submit.prevent="createNavbarLink" class="devlo-form">
                    {{-- Link type --}}
                    <div class="field">
                        <label class="field__label" for="link_status">
                            {{ __('messages.navbar_links.link_type') }}
                        </label>
                        <select id="link_status"
                                class="form-select @error('link_status') is-invalid @enderror"
                                wire:model.live="state.link_status">
                            <option value="">{{ __('messages.choose') }}</option>
                            @foreach($this->LinkStatusOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        <small class="field__hint">{{ __('messages.hints.choose_type_first') ?? '' }}</small>
                        @error('link_status')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Conditional selectors (Category / Type / Podcast / Video / Tag / Special Page) --}}
                    @if(isset($state['link_status']) && $state['link_status'] == LinkStatusEnum::CATEGORY->name)
                        <div class="field">
                            <label class="field__label"
                                   for="category_id">{{ __('messages.navbar_links.category') }}</label>
                            <select id="category_id" class="form-select @error('category_id') is-invalid @enderror"
                                    wire:model.live="state.category_id">
                                <option value="">{{ __('messages.choose') }}</option>
                                @foreach($this->categories as $row)
                                    <option value="{{ $row->id }}">{{ $row->category_title }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if(isset($state['link_status']) && $state['link_status'] == LinkStatusEnum::TYPE->name)
                        <div class="field">
                            <label class="field__label" for="type_id">{{ __('messages.navbar_links.type') }}</label>
                            <select id="type_id" class="form-select @error('type_id') is-invalid @enderror"
                                    wire:model.live="state.type_id">
                                <option value="">{{ __('messages.choose') }}</option>
                                @foreach($this->types as $row)
                                    <option value="{{ $row->id }}">{{ $row->type_name }}</option>
                                @endforeach
                            </select>
                            @error('type_id')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if(isset($state['link_status']) && $state['link_status'] == LinkStatusEnum::PODCAST->name)
                        <div class="field">
                            <label class="field__label"
                                   for="podcast_id">{{ __('messages.navbar_links.category') }}</label>
                            <select id="podcast_id" class="form-select @error('podcast_id') is-invalid @enderror"
                                    wire:model.live="state.podcast_id">
                                <option value="">{{ __('messages.choose') }}</option>
                                @foreach($this->category_podcasts as $row)
                                    <option value="{{ $row->id }}">{{ $row->category_title }}</option>
                                @endforeach
                            </select>
                            @error('podcast_id')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if(isset($state['link_status']) && $state['link_status'] == LinkStatusEnum::VIDEO->name)
                        <div class="field">
                            <label class="field__label"
                                   for="video_id">{{ __('messages.navbar_links.category') }}</label>
                            <select id="video_id" class="form-select @error('video_id') is-invalid @enderror"
                                    wire:model.live="state.video_id">
                                <option value="">{{ __('messages.choose') }}</option>
                                @foreach($this->category_videos as $row)
                                    <option value="{{ $row->id }}">{{ $row->category_title }}</option>
                                @endforeach
                            </select>
                            @error('video_id')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if(isset($state['link_status']) && $state['link_status'] == LinkStatusEnum::TAG->name)
                        <div class="field">
                            <label class="field__label" for="tag_id">{{ __('messages.navbar_links.tag') }}</label>
                            <select id="tag_id" class="form-select @error('tag_id') is-invalid @enderror"
                                    wire:model.live="state.tag_id">
                                <option value="">{{ __('messages.choose') }}</option>
                                @foreach($this->tags as $row)
                                    <option value="{{ $row->id }}">{{ $row->tag_name }}</option>
                                @endforeach
                            </select>
                            @error('tag_id')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if(isset($state['link_status']) && $state['link_status'] == LinkStatusEnum::SPECIAL_PAGE->name)
                        <div class="field">
                            <label class="field__label"
                                   for="special_page_id">{{ __('messages.navbar_links.special_page') }}</label>
                            <select id="special_page_id"
                                    class="form-select @error('special_page_id') is-invalid @enderror"
                                    wire:model.live="state.special_page_id">
                                <option value="">{{ __('messages.choose') }}</option>
                                @foreach($this->specialPages as $row)
                                    <option value="{{ $row->id }}">{{ $row->page_title }}</option>
                                @endforeach
                            </select>
                            @error('special_page_id')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    {{-- Menu text --}}
                    <div class="field">
                        <label class="field__label" for="link_name">{{ __('messages.navbar_links.menu_text') }}</label>
                        <input id="link_name" type="text"
                               class="form-control @error('link_name') is-invalid @enderror"
                               wire:model="state.link_name"
                               placeholder="{{ __('messages.navbar_links.menu_text') }}">
                        @error('link_name')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- URL (only for custom link) --}}
                    <div class="field">
                        <label class="field__label" for="link_url">{{ __('messages.navbar_links.link') }}</label>
                        <input id="link_url" type="text"
                               class="form-control text-start @error('link_url') is-invalid @enderror"
                               wire:model="state.link_url"
                               @disabled(isset($state['link_status']) && $state['link_status'] != LinkStatusEnum::CUSTOM_LINK->name)
                               @disabled(!isset($state['link_status']))
                               dir="ltr" inputmode="url"
                               placeholder="{{ __('messages.navbar_links.link') }}">
                        <small
                            class="field__hint">{{ __('messages.hints.absolute_or_relative_url') ?? 'https://domain.com / /path' }}</small>
                        @error('link_url')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Target --}}
                    <div class="field">
                        <label class="field__label" for="link_open">{{ __('messages.navbar_links.open_in') }}</label>
                        <select id="link_open"
                                class="form-select @error('link_open') is-invalid @enderror"
                                @disabled(!isset($state['link_status']))
                                wire:model="state.link_open">
                            <option value="">{{ __('messages.choose') }}</option>
                            @foreach(\App\Enums\LinkUrlTargetEnum::cases() as $row)
                                <option
                                    value="{{ $row->value }}">{{ \App\Enums\LinkUrlTargetEnum::fromValue($row->value) }}</option>
                            @endforeach
                        </select>
                        @error('link_open')
                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Icons (accordion) --}}
                    <div class="field">
                        <button class="accordion-button collapsed devlo-acc__btn" type="button"
                                data-bs-toggle="collapse" data-bs-target="#iconPicker" aria-expanded="false"
                                aria-controls="iconPicker">
                            # {{ __('messages.navbar_links.icon') }}
                        </button>
                        <div id="iconPicker" class="accordion-collapse collapse mt-3">
                            <div class="icon-grid">
                                @if($this->icons)
                                    @foreach($this->icons as $row)
                                        <div
                                            class="icon-tile @if(!empty($state['icon_id']) && $state['icon_id']==$row->id) is-selected @endif"
                                            wire:click="iconSelect({{ $row }})" role="button" tabindex="0">
                                            <img src="{{ file_url($row->icon_path) }}" alt="icon" width="48"
                                                 height="48">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @error('icon_id')
                        <div class="text-danger mt-2">{{ $message }}</div> @enderror
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('messages.confirm') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==================== Delete Modal ==================== -->
<div class="modal fade" id="navbar_linkDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="navbarLinkDeleteLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content devlo-modal">
            <div class="modal-header devlo-modal__header devlo-modal__header--danger">
                <h5 class="modal-title" id="navbarLinkDeleteLabel">{{ __('messages.navbar_links.delete_link') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">{{ __('messages.navbar_links.confirm_delete') }}</p>
                <p class="text-muted">{{ __('messages.navbar_links.link_will_be_deleted') }}:
                    <strong>{{ $NavbarLinks_->navbar_link_name ?? __('messages.not_available') }}</strong>
                </p>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button wire:click="navbar_linkDeleteConfirm"
                        class="btn btn-danger">{{ __('messages.confirm') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- ====== Scoped styles (no backend changes) ====== --}}
<style>
    /* ---------- Modal shell ---------- */
    .devlo-modal {
        border-radius: 16px;
        background: var(--bs-card-bg);
        box-shadow: 0 10px 28px rgba(2, 8, 23, .18);
    }

    .devlo-modal__header {
        background: linear-gradient(180deg, var(--bs-primary) 0%, var(--brand-primary-600, #4c54f8) 100%);
        color: #fff;
    }

    .devlo-modal__header--danger {
        background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
    }

    /* ---------- Form rhythm ---------- */
    .devlo-form {
        display: grid;
        gap: 14px;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .field__label {
        font-weight: 600;
        color: var(--brand-fg);
        line-height: 1;
    }

    .field__hint {
        color: var(--bs-text-muted);
        font-size: .85rem;
    }

    /* Inputs */
    .form-control, .form-select {
        min-height: 44px;
        background: var(--bs-card-bg);
        border-color: var(--bs-border-color);
    }

    .form-control:focus, .form-select:focus {
        box-shadow: var(--focus-ring);
        border-color: var(--brand-primary);
    }

    /* ---------- Icon picker ---------- */
    .devlo-acc__btn {
        background: var(--bs-card-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 10px;
        font-weight: 600;
        padding: 0.775rem 1rem;
    }

    .icon-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }



    .icon-tile {
        display: grid;
        place-items: center;
        border: 1px dashed var(--bs-border-color);
        border-radius: 12px;
        cursor: pointer;
        transition: .2s;
        background: var(--bs-card-bg);
    }

    .icon-tile:hover {
        border-style: solid;
        transform: translateY(-1px);
    }

    .icon-tile.is-selected {
        border: 2px solid var(--brand-primary);
        box-shadow: 0 0 0 3px rgba(100, 108, 252, .25);
    }

    /* ---------- Footer ---------- */
    .modal-footer {
        border-top: 1px solid var(--bs-border-color);
        padding: 0;
    }

    .btn-primary {
        background: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .btn-primary:hover {
        background: var(--bs-primary-hover);
        border-color: var(--bs-primary-hover);
    }
    .modal-title{
        color: #fff;
    }
</style>
