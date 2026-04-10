@php use App\Enums\LinkPosition; @endphp

    <!-- ==================== Social Media — Create / Edit (devlo-modal) ==================== -->
<div class="modal fade" id="social_media" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="socialMediaFormLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg"><!-- wider for better rhythm -->
        <div class="modal-content devlo-modal">
            <!-- Header -->
            <div class="modal-header devlo-modal__header {{ $showEdit ? 'devlo-modal__header--warn' : '' }}">
                <h5 class="modal-title" id="socialMediaFormLabel">
                    {{ $showEdit ? __('messages.social_media_links.edit_social_media')
                                 : __('messages.social_media_links.add_social_media') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('messages.cancel') }}"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- Keep Livewire action as-is -->
                <form wire:submit.prevent="{{ $showEdit ? 'updateSocialMedia' : 'createSocialMedia' }}"
                      class="devlo-form">

                    <!-- Title -->
                    <div class="field">
                        <label class="field__label" for="sm_title">{{ __('messages.social_media_links.title') }}</label>
                        <input id="sm_title" type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               wire:model="state.title" placeholder="">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- URL -->
                    <div class="field">
                        <label class="field__label" for="sm_url">{{ __('messages.social_media_links.link') }}</label>
                        <input id="sm_url" type="text"
                               class="form-control @error('url') is-invalid @enderror"
                               wire:model="state.url" dir="ltr" inputmode="url" placeholder="">
                        <small class="field__hint">{{ __('messages.hints.absolute_or_relative_url') ?? 'https://domain.com / /path' }}</small>
                        @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Position -->
                    <div class="field">
                        <label class="field__label" for="sm_position">{{ __('messages.social_media_links.position') }}</label>
                        <select id="sm_position" class="form-select @error('position') is-invalid @enderror"
                                wire:model="state.position">
                            <option value="">{{ __('messages.select_position') }}</option>
                            @foreach (LinkPosition::cases() as $position)
                                <option value="{{ $position->value }}">{{ $position->label() }}</option>
                            @endforeach
                        </select>
                        @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Icon picker (accordion) -->
                    <div class="field">
                        <button class="accordion-button collapsed devlo-acc__btn" type="button"
                                data-bs-toggle="collapse" data-bs-target="#smIconPicker" aria-expanded="false"
                                aria-controls="smIconPicker">
                            {{ __('messages.social_media_links.icon') }}
                        </button>
                        <div id="smIconPicker" class="accordion-collapse collapse mt-3">
                            <div class="icon-grid">
                                @if ($this->icons)
                                    @foreach ($this->icons as $row)
                                        <div class="icon-tile @if(!empty($state['icon_id']) && $state['icon_id'] == $row->id) is-selected @endif"
                                             wire:click="iconSelect({{ $row }})" role="button" tabindex="0">
                                            <img width="40" height="40" class="rounded p-2"
                                                 src="{{ file_url($row->icon_path) }}" alt="icon">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @error('icon_id') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn {{ $showEdit ? 'btn-warning' : 'btn-primary' }}">
                            {{ __('messages.confirm') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- ==================== Reorder (Header list) ==================== -->
<div class="modal fade" id="reorderModal" tabindex="-1" aria-labelledby="reorderModalLabel"
     aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content devlo-modal">
            <div class="modal-header devlo-modal__header">
                <h5 class="modal-title" id="reorderModalLabel">{{ __('messages.navbar_links.reorder_list') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ __('messages.navbar_links.confirm_reorder') }}</p>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" wire:click="reorderLink">
                    {{ __('messages.confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== Reorder (Footer list) ==================== -->
<div class="modal fade" id="footerReorderModal" tabindex="-1" aria-labelledby="footerReorderModalLabel"
     aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content devlo-modal">
            <div class="modal-header devlo-modal__header">
                <h5 class="modal-title" id="footerReorderModalLabel">{{ __('messages.navbar_links.reorder_list') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ __('messages.navbar_links.confirm_reorder') }}</p>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" wire:click="reorderFooterLink">
                    {{ __('messages.confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== Delete (devlo-modal) ==================== -->
<div class="modal fade" id="SocialMediaDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="socialMediaDeleteLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content devlo-modal">
            <div class="modal-header devlo-modal__header devlo-modal__header--danger">
                <h5 class="modal-title" id="socialMediaDeleteLabel">
                    {{ __('messages.social_media_links.delete_social_media') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('messages.cancel') }}"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">{{ __('messages.social_media_links.confirm_delete_social_media') }}</p>
                <p class="text-muted">
                    {{ __('messages.social_media_links.delete_social_media_message') }}
                    <strong>{{ $SocialMedia_?->title ?? __('messages.no_data') }}</strong>
                </p>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button wire:click="socialMediaDeleteConfirm" class="btn btn-danger">
                    {{ __('messages.confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ====== Scoped styles (no backend changes) ====== --}}
<style>
    /* ---------- Modal shell ---------- */
    .devlo-modal{
        border-radius:16px;
        background:var(--bs-card-bg);
        box-shadow:0 10px 28px rgba(2,8,23,.18);
        border:1px solid var(--bs-border-color);
    }
    .devlo-modal__header{
        background:linear-gradient(180deg, var(--bs-primary) 0%, var(--brand-primary-600, #4c54f8) 100%);
        color:#fff;
    }
    .devlo-modal__header--warn{ background:linear-gradient(180deg, #f59e0b 0%, #d97706 100%); color:#fff; }
    .devlo-modal__header--danger{ background:linear-gradient(180deg, #ef4444 0%, #dc2626 100%); color:#fff; }
    .modal-title{ color:#fff; }

    /* ---------- Form rhythm ---------- */
    .devlo-form{ display:grid; gap:14px; }
    .field{ display:flex; flex-direction:column; gap:8px; }
    .field__label{ font-weight:600; color:var(--brand-fg); line-height:1; }
    .field__hint{ color:var(--bs-text-muted); font-size:.85rem; }

    /* Inputs */
    .form-control,.form-select{
        min-height:44px;
        background:var(--bs-card-bg);
        border-color:var(--bs-border-color);
    }
    .form-control:focus,.form-select:focus{
        box-shadow:var(--focus-ring);
        border-color:var(--brand-primary);
    }

    /* ---------- Icon picker ---------- */
    .devlo-acc__btn{
        background:var(--bs-card-bg);
        border:1px solid var(--bs-border-color);
        border-radius:10px;
        font-weight:600;
        padding:.775rem 1rem;
    }
    .icon-grid{ display:flex; flex-wrap:wrap; gap:10px; }
    .icon-tile{
        display:grid; place-items:center;
        border:1px dashed var(--bs-border-color);
        border-radius:12px;
        cursor:pointer; transition:.2s;
        background:var(--bs-card-bg);
        padding:8px;
    }
    .icon-tile:hover{ border-style:solid; transform:translateY(-1px); }
    .icon-tile.is-selected{
        border:2px solid var(--brand-primary);
        box-shadow:0 0 0 3px rgba(100,108,252,.25);
    }

    /* ---------- Footer ---------- */
    .modal-footer{ border-top:1px solid var(--bs-border-color); padding:0; }
    .btn-primary{ background:var(--bs-primary); border-color:var(--bs-primary); }
    .btn-primary:hover{ background:var(--bs-primary-hover); border-color:var(--bs-primary-hover); }
</style>
