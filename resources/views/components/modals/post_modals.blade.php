@php use App\Enums\NewsTypeEnum;use App\Enums\PublishEnum; @endphp

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">


        <form>
            <div class="row g-4">
                <!-- ========= Left: Primary content ========= -->
                <div class="col-12 col-xl-8">
                    <div class="card fieldset-card">
                        <div class="card-header bg-transparent py-3">
                            @can($showEdit ? 'posts_edit' : 'posts_create')
                                <div class="editor-toolbar">
                                    <button type="button"
                                            class="btn btn-warning d-inline-flex align-items-center gap-2"
                                            wire:click="generatePreview({{ $this->Post?->id ?? 'null' }})"
                                            data-bs-toggle="tooltip" data-bs-title="{{ __('messages.posts.preview') }}">
                                        <i class="bi bi-eye"></i> <span>{{ __('messages.posts.preview') }}</span>
                                    </button>

                                    @if(config('app.launch') === 'quds' && !$showEdit)
                                        <button type="button"
                                                class="btn btn-info d-inline-flex align-items-center gap-2"
                                                wire:click="temporarySavePost"
                                                data-bs-toggle="tooltip" data-bs-title="Temporary Save (Title Only)">
                                            <i class="bi bi-floppy"></i>
                                            <span>Temporary Save</span>
                                        </button>
                                    @endif

                                    <a class="btn btn-secondary d-inline-flex align-items-center gap-2"
                                       wire:click="{{ $showEdit ? 'updatePost("'.PublishEnum::DRAFT->value . '")' : 'createPost("' . PublishEnum::DRAFT->value . '")' }}"
                                       data-bs-toggle="tooltip" data-bs-title="{{ __('messages.posts.save_draft') }}">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>{{ __('messages.posts.save_draft') }}</span>
                                    </a>

                                    @can('posts_publish')
                                        <a class="btn btn-primary d-inline-flex align-items-center gap-2"
                                           wire:click="{{ $showEdit ? 'updatePost("'.PublishEnum::PUBLISHED->value . '")' : 'createPost("' . PublishEnum::PUBLISHED->value . '")' }}"
                                           data-bs-toggle="tooltip" data-bs-title="{{ __('messages.posts.publish') }}">
                                            <i class="bi bi-check-circle"></i>
                                            <span>{{ __('messages.posts.publish') }}</span>
                                        </a>
                                    @else
                                        <a class="btn btn-primary d-inline-flex align-items-center gap-2"
                                           wire:click="{{ $showEdit ? 'updatePost("'.PublishEnum::PENDING->value . '")' : 'createPost("' . PublishEnum::PENDING->value . '")' }}"
                                           data-bs-toggle="tooltip" data-bs-title="{{ __('messages.posts.publish') }}">
                                            <i class="bi bi-check-circle"></i>
                                            <span>{{ __('messages.posts.publish') }}</span>
                                        </a>
                                    @endcan

                                    @can('posts_publish')
                                        <a class="btn btn-primary d-inline-flex align-items-center gap-2"
                                           wire:click="{{ $showEdit ? 'updatePost("'.PublishEnum::PUBLISHED->value . '",true)' : 'createPost("' . PublishEnum::PUBLISHED->value . '",false,true)' }}"
                                           data-bs-toggle="tooltip" data-bs-title="{{ __('messages.posts.publish_and_return') }}">
                                            <i class="bi bi-check-circle"></i>
                                            <span>{{ __('messages.posts.publish_and_return') }}</span>
                                        </a>
                                    @else
                                        <a class="btn btn-primary d-inline-flex align-items-center gap-2"
                                           wire:click="{{ $showEdit ? 'updatePost("'.PublishEnum::PENDING->value . '",true)' : 'createPost("' . PublishEnum::PENDING->value . '",false,true)' }}"
                                           data-bs-toggle="tooltip" data-bs-title="{{ __('messages.posts.publish_and_return') }}">
                                            <i class="bi bi-check-circle"></i>
                                            <span>{{ __('messages.posts.publish_and_return') }}</span>
                                        </a>
                                    @endcan
                                </div>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="row g-4">

                                {{-- Title --}}
                                @php $title_count = isset($state['title']) ? strlen($state['title']) : 0 @endphp
                                <div class="col-12">
                                    <label for="title"
                                           class="form-label fw-semibold {{ $title_count > 72 ? 'text-warning' : '' }}">
                                        <i class="bi bi-type me-1"></i> {{ __('messages.posts.title') }}
                                        <span class="counter-soft">({{ $title_count }})</span>
                                    </label>
                                    <input wire:model.blur="state.title" type="text"
                                           class="form-control @error('title') is-invalid @enderror"
                                           id="title" placeholder="{{ __('messages.posts.title') }}">
                                    @error('title')
                                    <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    @if($title_count > 72)
                                        <div
                                            class="text-warning input-hint mt-1">{{ __('messages.posts.title_warning') }}</div>
                                    @endif
                                </div>

                                {{-- Slug --}}
                                <div class="col-12">
                                    <label for="slug" class="form-label fw-semibold">
                                        <i class="bi bi-link-45deg me-1"></i> {{ __('messages.posts.slug') }}
                                    </label>
                                    <input wire:model.blur="state.slug" type="text"
                                           class="form-control @error('slug') is-invalid @enderror"
                                           id="slug" placeholder=" {{ __('messages.posts.slug') }}">
                                    @error('slug')
                                    <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>

                                {{-- Description / Claim --}}
                                @php $description_count = isset($state['description']) ? strlen($state['description']) : 0 @endphp
                                <div class="col-12">
                                    <label for="description"
                                           class="form-label fw-semibold {{ $description_count > 160 ? 'text-warning' : '' }}">
                                        <i class="bi bi-card-text me-1"></i>
                                        {{ __('messages.posts.description') }}
                                        <span class="counter-soft">({{ $description_count }})</span>
                                    </label>
                                    <textarea wire:model.live="state.description" rows="4"
                                              class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              placeholder="{{ __('messages.posts.description') }}"></textarea>
                                    @error('description')
                                    <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    @if($description_count > 160)
                                        <div
                                            class="text-warning input-hint mt-1">{{ __('messages.posts.description_warning') }}</div>
                                    @endif
                                </div>

                                {{-- Tags --}}
                                <div class="col-12">
                                    <div wire:ignore>
                                        <label for="tags" class="form-label fw-semibold">
                                            <i class="bi bi-tags me-1"></i> {{ __('messages.posts.tags') }}
                                        </label>
                                        <input class="form-control @error('tags') is-invalid @enderror"
                                               id="tags" name="tags" wire:model.live="state.tags">
                                    </div>
                                    @error('tags')
                                    <div class="text-danger mt-1" style="font-size: 15px">{{ $message }}</div>
                                    @enderror
                                    @php
                                        $body = $state['body'] ?? '';
                                        $tag = !empty($state['tags']) ? explode(',', $state['tags'])[0] : '';
                                        if (!empty($tag)) {
                                            $cleanedBody = strip_tags($body);
                                            $tagCount = substr_count($cleanedBody, $tag);
                                        }
                                    @endphp
                                    <div class="input-hint mt-2 d-flex flex-wrap gap-3">
                                        <span><i class="bi bi-hash me-1"></i>{{ __('messages.posts.main_tag') }}</span>
                                        <span class="pill-link"><i class="bi bi-123"></i> {{ __('messages.posts.tag_count', ['count' => $tagCount ?? 0]) }}</span>
                                    </div>
                                    @if(config('app.google_api_key'))

                                        {{--                                        <div--}}
                                        {{--                                            class="input-hint mt-2 d-flex flex-wrap gap-3 @if(!isset($this->state['title']) && !isset($this->state['description'])) opacity-50 @endif"--}}
                                        {{--                                            style=" cursor: pointer;" wire:loading.class="opacity-50"--}}
                                        {{--                                            wire:target="generateTags">--}}
                                        {{--                                        <span class="pill-link"><i class="bi bi-magic"></i><button--}}
                                        {{--                                                wire:click.prevent="generateTags"--}}
                                        {{--                                                style="all:unset;" wire:loading.attr="disabled"--}}
                                        {{--                                                wire:target="generateTags">Generate</button></span>--}}
                                        {{--                                        </div>--}}
                                        @if(isset($generatedTags))
                                            <div class="input-hint mt-2 d-flex flex-wrap gap-3">
                                                @foreach($generatedTags as $tag)
                                                    <button wire:click.prevent="addTag('{{$tag}}')" style="all:unset;">
                                                    <span class="pill-link">
                                                        {{$tag}}
                                                    </span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                {{-- Body / CKEditor --}}
                                @php
                                    $body = $state['body'] ?? '';
                                    $cleanedBody = strip_tags($body);
                                    preg_match_all('/\S+/', $cleanedBody, $matches);
                                    $wordCount = count($matches[0]);
                                    $paragraphs = preg_split('/\r\n|\n|\r/', trim($cleanedBody));
                                    $paragraphCount = count(array_filter($paragraphs));
                                @endphp

                                <div class="col-12 mt-4">
                                    <div wire:ignore>
                                        <label for="post-ckeditor-content" class="form-label fw-semibold">
                                            <i class="bi bi-textarea-t me-1"></i> {{ __('messages.posts.details') }}
                                        </label>
                                        <textarea id="post-ckeditor-content"
                                                  class="form-control @error('body') is-invalid @enderror"></textarea>
                                        <input type="hidden" wire:model="state.body" id="body-content-hidden">
                                    </div>
                                    @error('body')
                                    <div class="text-danger mt-1" style="font-size: 15px">{{ $message }}</div> @enderror

                                    <div class="d-flex flex-wrap gap-3 mt-3 align-items-center" wire:ignore>
                                        <span class="pill-link"><i class="bi bi-paragraph"></i>{{__('messages.posts.paragraph_count')}} <span
                                                data-paragraph-count>0</span></span>
                                        <span class="pill-link"><i class="bi bi-file-word"></i>{{__('messages.posts.word_count')}} <span
                                                data-word-count>0</span></span>
                                    </div>

                                    <div class="my-3 input-hint">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ __('messages.posts.add_from_the_library_title') }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========= Right: Meta / Images / SEO ========= -->
                <div class="col-12 col-xl-4 meta-aside">

                    {{-- Featured Image --}}
                    {{-- Featured / Advanced Images with Arabic/English text-based SVG placeholders --}}
                    @php
                        $locale = app()->getLocale();
                        $TXT_FEATURED = $locale === 'ar' ? 'صورة المنشور' : 'Post Image';
                        $TXT_PORTRAIT = $locale === 'ar' ? 'صورة عمودية' : 'Portrait Image';
                        $TXT_SLIDER   = $locale === 'ar' ? 'صورة السلايدر' : 'Slider Image';
                        $TXT_SOCIAL   = $locale === 'ar' ? 'صورة التواصل' : 'Social Image';

                        // Safe SVG data-URI placeholder generator (supports Arabic)
                      // Safe SVG data-URI placeholder generator (supports Arabic)
                            $svgPlaceholder = function(int $w, int $h, string $text, string $bg = '#dddddd', string $fg = '#555555') {
                            // font-family includes Arabic-friendly fonts
                            $font = "Noto Kufi Arabic, 'Noto Naskh Arabic', Tahoma, Arial, sans-serif";
                            $dir  = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
                            $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                            $svg = <<<SVG
                    <svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" role="img" aria-label="{$escaped}">
                      <rect width="100%" height="100%" fill="{$bg}"/>
                      <g>
                        <text x="50%" y="50%" fill="{$fg}" font-size="48" font-family="{$font}" text-anchor="middle" dominant-baseline="middle" direction="{$dir}">{$escaped}</text>
                      </g>
                    </svg>
                    SVG;
                            return 'data:image/svg+xml;charset=UTF-8,'.rawurlencode($svg);
                        };

                        // Parse WxH strings from config if present (fallback to sane defaults)
                        $parseSize = function (?string $wh, array $fallback) {
                            if (!$wh || strpos($wh, 'x') === false) return $fallback;
                            [$w,$h] = array_map('intval', explode('x', $wh, 2));
                            return [$w ?: $fallback[0], $h ?: $fallback[1]];
                        };

                        // Featured default size
                        [$F_W, $F_H] = [1200, 630];

                        // Advanced sizes from config with fallbacks
                        [$P_W,$P_H] = $parseSize(config('features.image_sizes.post.portrait_image') ?? null, [630,1200]);
                        [$S_W,$S_H] = $parseSize(config('features.image_sizes.post.slider_image') ?? null,   [1920,1080]);
                        [$SM_W,$SM_H] = $parseSize(config('features.image_sizes.post.social_media_image') ?? null, [916,430]);

                        // Build data URIs
                        $PH_FEATURED = $svgPlaceholder($F_W, $F_H, $TXT_FEATURED);
                        $PH_PORTRAIT = $svgPlaceholder($P_W, $P_H, $TXT_PORTRAIT);
                        $PH_SLIDER   = $svgPlaceholder($S_W, $S_H, $TXT_SLIDER);
                        $PH_SOCIAL   = $svgPlaceholder($SM_W, $SM_H, $TXT_SOCIAL);
                    @endphp

                    <div class="card image-box">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-2 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-image"></i> <span>{{ __('messages.posts.featured_image') }}</span>
                            </h5>

                            {{--                            <div class="dim-hint my-2">--}}
                            {{--                                <i class="bi bi-tag"></i>--}}
                            {{--                                <span>{{ $TXT_FEATURED }}</span>--}}
                            {{--                            </div>--}}
                            {{-- Temporary debug --}}

                            <div class="position-relative mb-3 mx-auto" style="max-width: 320px;">
                                <img
                                    src="{{ isset($state['image_name']) ? file_url($state['image_name']) : $PH_FEATURED }}"
                                    alt="{{ __('messages.posts.featured_image') }}"
                                    id="imagePreview"
                                    class="img-fluid preview {{ isset($state['image_name']) ? '' : 'is-ph' }}"
                                    style="cursor:pointer;"
                                    onclick="Livewire.dispatch('columnDefined', { column: 'image' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');">

                                <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                        aria-label="Close" wire:click.prevent="clearColumn('image')"></button>
                            </div>

                            @error('image')
                            <div class="text-danger" style="font-size: 15px">{{ $message }}</div> @enderror

                            <input type="text" wire:model.live="state.image_alt" class="form-control mb-3"
                                   id="image_alt" placeholder="{{ __('messages.posts.image_description') }}"
                                   style="max-width: 320px; margin: 0 auto;">

                            <div class="form-check form-switch d-inline-flex align-items-center gap-2 mt-2">
                                <input class="form-check-input" type="checkbox" wire:model.live="advancedImage"
                                       id="checkDefault">
                                <label class="form-check-label" for="checkDefault">
                                    <i class="bi bi-layout-wtf me-1"></i>{{__('messages.advancedImages')}}
                                </label>
                            </div>

                            @if($advancedImage)
                                {{-- Portrait --}}
                                <div class="mt-4">
                                    <div
                                        class="fw-semibold mb-1 d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-phone-flip"></i> {{__('messages.posts.PortraitImage')}}
                                    </div>
                                    {{--                                    <div class="dim-hint mb-2">--}}
                                    {{--                                        <i class="bi bi-tag"></i> <span>{{ $TXT_PORTRAIT }}</span>--}}
                                    {{--                                    </div>--}}
                                    <div class="position-relative mb-2 mx-auto" style="max-width: 220px;">
                                        <img
                                            src="{{ isset($state['portrait_image_name']) ? file_url($state['portrait_image_name']) : $PH_PORTRAIT }}"
                                            alt="{{ __('messages.posts.PortraitImage') }}"
                                            class="img-fluid preview {{ isset($state['portrait_image_name']) ? '' : 'is-ph' }}"
                                            style="cursor:pointer;"
                                            onclick="Livewire.dispatch('columnDefined', { column: 'portrait_image' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');">
                                        <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                aria-label="Close"
                                                wire:click.prevent="clearColumn('portrait_image')"></button>
                                    </div>
                                    @error('portrait_image')
                                    <div class="text-danger mt-1" style="font-size: 15px">{{ $message }}</div> @enderror
                                </div>

                                {{-- Slider --}}
                                <div class="mt-4">
                                    <div
                                        class="fw-semibold mb-1 d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-sliders2"></i> {{__('messages.posts.SliderImage')}}
                                    </div>
                                    {{--                                    <div class="dim-hint mb-2">--}}
                                    {{--                                        <i class="bi bi-tag"></i> <span>{{ $TXT_SLIDER }}</span>--}}
                                    {{--                                    </div>--}}
                                    <div class="position-relative mb-2 mx-auto" style="max-width: 220px;">
                                        <img
                                            src="{{ isset($state['slider_image_name']) ? file_url($state['slider_image_name']) : $PH_SLIDER }}"
                                            alt="{{ __('messages.posts.SliderImage') }}"
                                            class="img-fluid preview {{ isset($state['slider_image_name']) ? '' : 'is-ph' }}"

                                            style="cursor:pointer;"
                                            onclick="Livewire.dispatch('columnDefined', { column: 'slider_image' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');">
                                        <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                aria-label="Close"
                                                wire:click.prevent="clearColumn('slider_image')"></button>
                                    </div>
                                    @error('slider_image')
                                    <div class="text-danger mt-1" style="font-size: 15px">{{ $message }}</div> @enderror
                                </div>

                                {{-- Social --}}
                                <div class="mt-4">
                                    <div
                                        class="fw-semibold mb-1 d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-share"></i> {{__('messages.posts.SocialMediaImage')}}
                                    </div>
                                    {{--                                    <div class="dim-hint mb-2">--}}
                                    {{--                                        <i class="bi bi-tag"></i> <span>{{ $TXT_SOCIAL }}</span>--}}
                                    {{--                                    </div>--}}
                                    <div class="position-relative mb-2 mx-auto" style="max-width: 220px;">
                                        <img
                                            src="{{ isset($state['social_media_image_name']) ? file_url($state['social_media_image_name']) : $PH_SOCIAL }}"
                                            alt="{{ __('messages.posts.SocialMediaImage') }}"
                                            class="img-fluid preview {{ isset($state['social_media_image_name']) ? '' : 'is-ph' }}"

                                            style="cursor:pointer;"
                                            onclick="Livewire.dispatch('columnDefined', { column: 'social_media_image' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');">
                                        <button type="button" class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                aria-label="Close"
                                                wire:click.prevent="clearColumn('social_media_image')"></button>
                                    </div>
                                    @error('social_media_image')
                                    <div class="text-danger mt-1" style="font-size: 15px">{{ $message }}</div> @enderror
                                </div>
                            @endif
                        </div>
                    </div>


                    {{-- Publishing / Meta (Sidebar Card) --}}
                    <div class="card" x-data>
                        <div class="card-body p-3">
                            <div class="d-flex flex-column gap-3">

                                {{-- ======================== NEWS TYPE ======================== --}}
                                {{-- ======================== NEWS TYPE ======================== --}}
                                <div class="meta-section">
                                    {{-- Section head --}}
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-newspaper"></i>
                                        <label class="form-label fw-semibold small mb-1"
                                               for="news_type">{{ __('messages.posts.news_type') }}</label>
                                        <span class="text-muted small">{{ __('messages.optional') }}</span>
                                    </div>

                                    {{-- Chips group (accessible radios) --}}
                                    <div class="chip-group" role="radiogroup"
                                         aria-label="{{ __('messages.posts.news_type') }}">
                                        @foreach(NewsTypeEnum::cases() as $type)
                                            @php
                                                $id = 'newsType_'.$type->value; // unique id per radio
                                                $isActive = ($state['news_type'] ?? null) === $type->value;
                                            @endphp

                                            {{--
                                                Keep the real <input> for accessibility & Livewire binding.
                                                We visually hide it and style the <label> as a chip.
                                            --}}
                                            <input
                                                class="chip-input"
                                                type="radio"
                                                id="{{ $id }}"
                                                name="news_type"
                                                wire:model.live="state.news_type"
                                                value="{{ $type->value }}"
                                                aria-checked="{{ $isActive ? 'true' : 'false' }}"
                                            >

                                            <label
                                                for="{{ $id }}"
                                                class="chip {{ $isActive ? 'is-active' : '' }}"
                                                data-value="{{ $type->value }}"
                                            >
                                                <span>{{ $type->label() }}</span>
                                            </label>
                                        @endforeach
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger "
                                            wire:click="$set('state.news_type', null)"
                                        >
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('messages.cancel') }}
                                        </button>
                                    </div>

                                    {{-- Error + reset --}}
                                    @error('news_type')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror


                                </div>


                                {{-- ======================== IMAGE SIZE ======================== --}}
                                @if(!empty(\App\Enums\ImageSizeTypeEnum::available()))
                                    <div class="meta-section">
                                        {{-- Section head --}}
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-aspect-ratio"></i>
                                            <label class="form-label fw-semibold small mb-1"
                                                   for="image_size">{{ __('messages.posts.image_size') }}</label>
                                        </div>

                                        {{-- Chips group (accessible radios) --}}
                                        <div class="chip-group" role="radiogroup"
                                             aria-label="{{ __('messages.posts.image_size') }}">
                                            @foreach(\App\Enums\ImageSizeTypeEnum::available() as $type)
                                                @php
                                                    $id = 'imageSize_'.$type->value; // unique id per radio
                                                    $isActive = ($state['image_size'] ?? null) === $type->value;
                                                @endphp

                                                {{--
                                                    Keep the real <input> for accessibility & Livewire binding.
                                                    We visually hide it and style the <label> as a chip.
                                                --}}
                                                <input
                                                    class="chip-input"
                                                    type="radio"
                                                    id="{{ $id }}"
                                                    name="image_size"
                                                    wire:model.live="state.image_size"
                                                    value="{{ $type->value }}"
                                                    aria-checked="{{ $isActive ? 'true' : 'false' }}"
                                                >

                                                <label
                                                    for="{{ $id }}"
                                                    class="chip {{ $isActive ? 'is-active' : '' }}"
                                                    data-value="{{ $type->value }}"
                                                >
                                                    <span>{{ $type->label() }}</span>
                                                </label>
                                            @endforeach
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger "
                                                wire:click="$set('state.image_size', null)"
                                            >
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('messages.cancel') }}
                                            </button>
                                        </div>

                                        {{-- Error + reset --}}
                                        @error('image_size')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror

                                    </div>
                                @endif

                                {{-- ======================== DATES ======================== --}}
                                <div class="meta-section">
                                    <label for="publish_date" class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-calendar2-check me-1"></i>
                                        {{ __('messages.posts.publish_date') }}
                                    </label>
                                    <input type="datetime-local"
                                           id="publish_date"
                                           class="form-control form-control-sm"
                                           wire:model.live="state.publish_date">
                                    @error('publish_date')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror

                                    @if(config('features.general.has_post_available_date'))
                                        <label for="available_date" class="form-label fw-semibold small mt-2 mb-1">
                                            <i class="bi bi-clock-history me-1"></i>
                                            {{ __('messages.posts.available_date') }}
                                        </label>
                                        <input type="datetime-local"
                                               id="available_date"
                                               class="form-control form-control-sm"
                                               wire:model.live="state.available_date">
                                        @error('available_date')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                {{-- ===================================================================
                                     CATEGORY  — Select existing + inline "Add" popup (entangled list)
                                     - We DO NOT touch backend: new items are pushed into categories_list
                                     - On Save/Publish, your existing code creates/relates them.
                                   =================================================================== --}}
                                <div class="meta-section"
                                     x-data="{
                    show:false,
                    name:'',
                    // Two-way bind to Livewire array so we can push without backend changes
                    list: @entangle('categories_list').live,
                    add(){
                      if(!this.name.trim()) return;
                      this.list.push({ title: this.name.trim() });
                      this.name=''; this.show=false;
                    }
                 }">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-collection"></i>
                                        <span class="fw-semibold small">{{ __('messages.posts.category') }}</span>
                                    </div>

                                    {{-- Row: existing select + add button --}}
                                    <div class="d-flex align-items-stretch gap-2 position-relative">
                                        <div class="flex-grow-1" wire:ignore>
                                            <select id="category_id"
                                                    class="form-select form-select-sm"
                                                    wire:model.live="state.category_id"
                                                    multiple
                                                    data-control="select2"
                                                    data-placeholder="{{ __('messages.choose') }}">
                                                @foreach($this->categories as $category)
                                                    <option
                                                        @selected(isset($state['category_id']) && $state['category_id'] == $category['id'])
                                                        value="{{ $category['id'] }}">
                                                        {{ $category['category_title'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Add button (opens popup ABOVE the button) --}}
                                        <div class="position-relative">
                                            <button type="button" class="btn btn-sm btn-success btn-add-input"
                                                    @click="show = !show"
                                                    aria-expanded="false"
                                                    aria-controls="cat-popup">
                                                <i class="bi bi-plus-circle "></i>

                                            </button>

                                            {{-- Lightweight popup: input + confirm/cancel --}}
                                            <div id="cat-popup"
                                                 x-show="show"
                                                 x-transition
                                                 @keydown.escape.window="show=false"
                                                 class="popup-add shadow-sm"
                                                 style="bottom: 110%; right: 0;"
                                                 @click.outside="show=false">
                                                <label class="form-label small mb-1">{{ __('messages.categories.category_title') }}</label>
                                                <input type="text" class="form-control form-control-sm mb-2"
                                                       x-model="name"
                                                       placeholder="{{ __('messages.categories.category_title') }}"
                                                       @keydown.enter.prevent="add()">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-light"
                                                            @click="show=false">
                                                        {{ __('messages.cancel') }}
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary" @click="add()">
                                                        {{ __('messages.confirm') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Inline newly-added (unsaved) items list (as before) --}}
                                    <div class="mt-2">
                                        @foreach($categories_list as $key => $category)
                                            <div class="card mb-2" wire:key="category-{{ $key }}">
                                                <div class="card-body p-2">
                                                    <label class="form-label small mb-1"
                                                           for="categories_list.{{$key}}.title">
                                                        {{ __('messages.categories.category_title') }}
                                                    </label>
                                                    <input type="text"
                                                           id="categories_list.{{$key}}.title"
                                                           class="form-control form-control-sm @error("categories_list.{$key}.title") is-invalid @enderror"
                                                           wire:model="categories_list.{{$key}}.title"
                                                           placeholder="{{ __('messages.categories.category_title') }}">
                                                    @error("categories_list.{$key}.title")
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror

                                                    <button type="button"
                                                            class="btn btn-xs btn-outline-danger mt-1"
                                                            wire:click="removeCategory({{ $key }})">
                                                        <i class="bi bi-dash-circle"></i>
                                                        {{ __('messages.remove') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('category_id')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ===================================================================
                                     AUTHOR — Select existing + inline “Add” popup (name)
                                   =================================================================== --}}
                                <div class="meta-section"
                                     x-data="{
                    show:false,
                    name:'',
                    list:@entangle('authors_list').live,
                    add(){
                      if(!this.name.trim()) return;
                      this.list.push({ name: this.name.trim() });
                      this.name=''; this.show=false;
                    }
                 }">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-person-badge"></i>
                                        <span class="fw-semibold small">{{ __('messages.posts.author') }}</span>
                                    </div>

                                    <div class="d-flex align-items-stretch gap-2 position-relative">
                                        <div class="flex-grow-1" wire:ignore>
                                            <select id="author_id"
                                                    class="form-select form-select-sm"
                                                    wire:model.live="state.author_id"
                                                    multiple
                                                    data-control="select2"
                                                    data-placeholder="{{ __('messages.choose') }}">
                                                @foreach($this->authors as $author)
                                                    <option value="{{ $author['id'] }}">{{ $author['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="position-relative">
                                            <button type="button" class="btn btn-sm btn-success btn-add-input"
                                                    @click="show = !show" aria-controls="auth-popup">
                                                <i class="bi bi-plus-circle"></i>
                                            </button>
                                            <div id="auth-popup"
                                                 x-show="show" x-transition @click.outside="show=false"
                                                 class="popup-add shadow-sm" style="bottom: 110%; right: 0;">
                                                <label class="form-label small mb-1">{{ __('messages.authors.author_name') }}</label>
                                                <input type="text" class="form-control form-control-sm mb-2"
                                                       x-model="name" placeholder="{{ __('messages.authors.author_name') }}"
                                                       @keydown.enter.prevent="add()">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-light"
                                                            @click="show=false">{{ __('messages.cancel') }}</button>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                            @click="add()">{{ __('messages.confirm') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Inline authors_list cards (existing behavior) --}}
                                    <div class="mt-2">
                                        @foreach($authors_list as $key => $author)
                                            <div class="card mb-2" wire:key="author-{{ $key }}">
                                                <div class="card-body p-2">
                                                    <label class="form-label small mb-1"
                                                           for="authors_list.{{$key}}.name">
                                                        {{ __('messages.authors.author_name') }}
                                                    </label>
                                                    <input type="text"
                                                           id="authors_list.{{$key}}.name"
                                                           class="form-control form-control-sm @error("authors_list.{$key}.name") is-invalid @enderror"
                                                           wire:model="authors_list.{{$key}}.name"
                                                           placeholder="{{ __('messages.authors.author_name') }}">
                                                    @error("authors_list.{$key}.name")
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror

                                                    <button type="button" class="btn btn-xs btn-outline-danger mt-1"
                                                            wire:click="removeAuthor({{ $key }})">
                                                        <i class="bi bi-dash-circle"></i>{{ __('messages.remove') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @error('author_id')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ===================================================================
                                     PUBLISHERS — Select existing + inline “Add” popup (name + url)
                                 =================================================================== --}}
                                @if (config('features.general.has_publishers'))
                                    <div class="meta-section"
                                         x-data="{
                    show:false, name:'', url:'',
                    list:@entangle('publishers_list').live,
                    add(){
                      if(!this.name.trim()) return;
                      this.list.push({ name: this.name.trim(), url: this.url.trim() || '' });
                      this.name=''; this.url=''; this.show=false;
                    }
                 }">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-megaphone"></i>
                                            <span class="fw-semibold small">{{ __('messages.posts.publishers') }}</span>
                                        </div>

                                        <div class="d-flex align-items-stretch gap-2 position-relative">
                                            {{--                                            <div class="flex-grow-1" wire:ignore>--}}
                                            {{--                                                <select class="form-select form-select-sm"--}}
                                            {{--                                                        wire:model.live="state.publisher_id"--}}
                                            {{--                                                        multiple--}}
                                            {{--                                                        data-control="select2"--}}
                                            {{--                                                        data-placeholder="{{ __('messages.choose') }}">--}}
                                            {{--                                                    @foreach($this->publishers as $p)--}}
                                            {{--                                                        <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>--}}
                                            {{--                                                    @endforeach--}}
                                            {{--                                                </select>--}}
                                            {{--                                            </div>--}}

                                            <div class="position-relative">
                                                <button type="button" class="btn btn-sm btn-success btn-add-input"
                                                        @click="show=!show" aria-controls="pub-popup">
                                                    <i class="bi bi-plus-circle"></i>
                                                </button>
                                                <div id="pub-popup" x-show="show" x-transition
                                                     @click.outside="show=false"
                                                     class="popup-add shadow-sm"
                                                     style="bottom: 110%; right: 0; width: 260px;">
                                                    <label
                                                        class="form-label small mb-1">{{ __('messages.participants.name') }}</label>
                                                    <input type="text" class="form-control form-control-sm mb-2"
                                                           x-model="name"
                                                           placeholder="{{ __('messages.participants.name') }}">
                                                    <label
                                                        class="form-label small mb-1">{{ __('messages.participants.url') }}</label>
                                                    <input type="url" class="form-control form-control-sm mb-2"
                                                           x-model="url" placeholder="https://...">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-sm btn-light"
                                                                @click="show=false">{{ __('messages.cancel') }}</button>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                                @click="add()">{{ __('messages.confirm') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Inline cards (existing behavior) --}}
                                        <div class="mt-2">
                                            @foreach($publishers_list as $key => $publisher)
                                                <div class="card mb-2" wire:key="publisher-{{ $key }}">
                                                    <div class="card-body p-2">
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-1"
                                                                   for="publishers_list.{{$key}}.name">
                                                                {{ __('messages.participants.name') }}
                                                            </label>
                                                            <input type="text"
                                                                   id="publishers_list.{{$key}}.name"
                                                                   class="form-control form-control-sm @error("publishers_list.{$key}.name") is-invalid @enderror"
                                                                   wire:model="publishers_list.{{$key}}.name">
                                                            @error("publishers_list.{$key}.name")
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-1">
                                                            <label class="form-label small mb-1"
                                                                   for="publishers_list.{{$key}}.url">
                                                                {{ __('messages.participants.url') }}
                                                            </label>
                                                            <input type="url"
                                                                   id="publishers_list.{{$key}}.url"
                                                                   class="form-control form-control-sm @error("publishers_list.{$key}.url") is-invalid @enderror"
                                                                   wire:model="publishers_list.{{$key}}.url">
                                                            @error("publishers_list.{$key}.url")
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <button type="button" class="btn btn-xs btn-outline-danger mt-1"
                                                                wire:click="removePublisher({{ $key }})">
                                                            <i class="bi bi-dash-circle"></i>{{ __('messages.remove') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- ===================================================================
                                     RESOURCES — Select existing + inline “Add” popup (name + url)
                                 =================================================================== --}}
                                @if (config('features.general.has_resources'))
                                    <div class="meta-section"
                                         x-data="{
                    show:false, name:'', url:'',
                    list:@entangle('resources_list').live,
                    add(){
                      if(!this.name.trim()) return;
                      this.list.push({ name: this.name.trim(), url: this.url.trim() || '' });
                      this.name=''; this.url=''; this.show=false;
                    }
                 }">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-link-45deg"></i>
                                            <span class="fw-semibold small">{{ __('messages.posts.resources') }}</span>
                                        </div>

                                        <div class="d-flex align-items-stretch gap-2 position-relative">
                                            {{--                                            <div class="flex-grow-1" wire:ignore>--}}
                                            {{--                                                <select class="form-select form-select-sm"--}}
                                            {{--                                                        wire:model.live="state.resource_id"--}}
                                            {{--                                                        multiple--}}
                                            {{--                                                        data-control="select2"--}}
                                            {{--                                                        data-placeholder="{{ __('messages.choose') }}">--}}
                                            {{--                                                    @foreach($this->resources as $r)--}}
                                            {{--                                                        <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>--}}
                                            {{--                                                    @endforeach--}}
                                            {{--                                                </select>--}}
                                            {{--                                            </div>--}}

                                            <div class="position-relative">
                                                <button type="button" class="btn btn-sm btn-success btn-add-input"
                                                        @click="show=!show" aria-controls="res-popup">
                                                    <i class="bi bi-plus-circle"></i>
                                                </button>
                                                <div id="res-popup" x-show="show" x-transition
                                                     @click.outside="show=false"
                                                     class="popup-add shadow-sm"
                                                     style="bottom: 110%; right: 0; width: 260px;">
                                                    <label
                                                        class="form-label small mb-1">{{ __('messages.participants.name') }}</label>
                                                    <input type="text" class="form-control form-control-sm mb-2"
                                                           x-model="name"
                                                           placeholder="{{ __('messages.participants.name') }}">
                                                    <label
                                                        class="form-label small mb-1">{{ __('messages.participants.url') }}</label>
                                                    <input type="url" class="form-control form-control-sm mb-2"
                                                           x-model="url" placeholder="https://...">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-sm btn-light"
                                                                @click="show=false">{{ __('messages.cancel') }}</button>
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                                @click="add()">{{ __('messages.confirm') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Inline resources_list cards (existing behavior) --}}
                                        <div class="mt-2">
                                            @foreach($resources_list as $key => $resource)
                                                <div class="card mb-2" wire:key="resource-{{ $key }}">
                                                    <div class="card-body p-2">
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-1"
                                                                   for="resources_list.{{$key}}.name">
                                                                {{ __('messages.participants.name') }}
                                                            </label>
                                                            <input type="text"
                                                                   id="resources_list.{{$key}}.name"
                                                                   class="form-control form-control-sm @error("resources_list.{$key}.name") is-invalid @enderror"
                                                                   wire:model="resources_list.{{$key}}.name">
                                                            @error("resources_list.{$key}.name")
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-1">
                                                            <label class="form-label small mb-1"
                                                                   for="resources_list.{{$key}}.url">
                                                                {{ __('messages.participants.url') }}
                                                            </label>
                                                            <input type="url"
                                                                   id="resources_list.{{$key}}.url"
                                                                   class="form-control form-control-sm @error("resources_list.{$key}.url") is-invalid @enderror"
                                                                   wire:model="resources_list.{{$key}}.url">
                                                            @error("resources_list.{$key}.url")
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <button type="button" class="btn btn-xs btn-outline-danger mt-1"
                                                                wire:click="removeResource({{ $key }})">
                                                            <i class="bi bi-dash-circle"></i>{{ __('messages.remove') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- ======================== TYPES ======================== --}}
                                @if(config('features.permissions.types'))
                                    <div class="meta-section"
                                         x-data="{
                        show:false,
                        name:'',
                        list: @entangle('types_list').live,
                        add(){
                          if(!this.name.trim()) return;
                          this.list.push({ name: this.name.trim() });
                          this.name=''; this.show=false;
                        }
                     }">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-diagram-3"></i>
                                            <span class="fw-semibold small">{{ __('messages.posts.type') }}</span>
                                        </div>

                                        {{-- Row: existing select + add button --}}
                                        <div class="d-flex align-items-stretch gap-2 position-relative">
                                            <div class="flex-grow-1" wire:ignore>
                                                <select id="type_id"
                                                        class="form-select form-select-sm"
                                                        wire:model.live="state.type_id"
                                                        multiple
                                                        data-control="select2"
                                                        data-placeholder="{{ __('messages.choose') }}">
                                                    <option value="">{{ __('messages.choose') }}</option>
                                                    @foreach($this->types as $type)
                                                        <option value="{{ $type['id'] }}">{{ $type['type_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Add button (opens popup ABOVE the button) --}}
                                            <div class="position-relative">
                                                <button type="button" class="btn btn-sm btn-success btn-add-input"
                                                        @click="show = !show"
                                                        aria-expanded="false"
                                                        aria-controls="type-popup">
                                                    <i class="bi bi-plus-circle "></i>
                                                </button>

                                                {{-- Lightweight popup: input + confirm/cancel --}}
                                                <div id="type-popup"
                                                     x-show="show"
                                                     x-transition
                                                     @keydown.escape.window="show=false"
                                                     class="popup-add shadow-sm"
                                                     style="bottom: 110%; right: 0;"
                                                     @click.outside="show=false">
                                                    <label class="form-label small mb-1">{{ __('messages.navbar_links.type') }}</label>
                                                    <input type="text" class="form-control form-control-sm mb-2"
                                                           x-model="name"
                                                           placeholder="{{ __('messages.navbar_links.type') }}"
                                                           @keydown.enter.prevent="add()">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-sm btn-light"
                                                                @click="show=false">
                                                            {{ __('messages.cancel') }}
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary" @click="add()">
                                                            {{ __('messages.confirm') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @error('type_id')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror

                                        {{-- Inline newly-added (unsaved) items list --}}
                                        <div class="mt-2">
                                            @foreach($types_list as $key => $type)
                                                <div class="card mb-2" wire:key="type-{{ $key }}">
                                                    <div class="card-body p-2">
                                                        <label class="form-label small mb-1"
                                                               for="types_list.{{$key}}.name">
                                                            {{ __('Type Name') }}
                                                        </label>
                                                        <input type="text"
                                                               id="types_list.{{$key}}.name"
                                                               class="form-control form-control-sm @error("types_list.{$key}.name") is-invalid @enderror"
                                                               wire:model="types_list.{{$key}}.name"
                                                               placeholder="{{ __('Type Name') }}">
                                                        @error("types_list.{$key}.name")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <button type="button"
                                                                class="btn btn-sm btn-danger mt-2"
                                                                wire:click="removeType({{$key}})">
                                                            {{ __('messages.remove') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- ======================== SPECIAL FILE (unchanged) ======================== --}}
                                @if(config('features.permissions.special_files'))
                                    <div class="meta-section">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-file-earmark-text"></i>
                                            <span
                                                class="fw-semibold small">{{ __('messages.posts.special_file') }}</span>
                                        </div>
                                        <div wire:ignore>
                                            <select id="special_file_id"
                                                    class="form-select form-select-sm"
                                                    wire:model.live="state.special_file_id"
                                                    data-control="select2"
                                                    data-placeholder="{{ __('messages.choose') }}">
                                                <option value="">{{ __('messages.choose') }}</option>
                                                @foreach($this->special_files as $file)
                                                    <option value="{{ $file['id'] }}">{{ $file['file_name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('special_file_id')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>


                    {{-- SEO --}}
                    <div class="card">
                        <div class="card-header p-3">
                            <h1 class="card-title fs-4 d-flex align-items-center gap-2">
                                <i class="bi bi-graph-up"></i> {{ __('messages.seo_post.SEO_analysis') }}
                            </h1>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                @php
                                    $tag = !empty($state['tags']) ? explode(',', $state['tags'])[0] : null;
                                    $title = isset($state['title']) ? explode(' ',$state['title']) : [];
                                    $description = $state['description'] ?? null;
                                    $tagInTitle = isset($tag) && str_contains($state['title'] ?? '', $tag);
                                    $tagInDescription = isset($tag) && str_contains($description, $tag);
                                    $body = $state['body'] ?? '';
                                    $paragraphs = preg_split('/\r?\n\r?\n/', $body);
                                    $firstParagraph = $paragraphs[0] ?? null;
                                    $tagInFirstParagraph = isset($tag) && str_contains($firstParagraph, $tag);
                                    $remainingParagraphs = array_slice($paragraphs, 1);
                                    $remainingText = implode(' ', $remainingParagraphs);
                                    $tagCounts = 0;
                                    if (!empty($tag)) {
                                        $count = substr_count($remainingText, $tag);
                                        if (!empty($count) && $count > 0) { $tagCounts += $count; }
                                    }
                                    $body = $state['body'] ?? '';
                                    $cleanedBody = strip_tags($body);
                                    preg_match_all('/\S+/', $cleanedBody, $matches);
                                    $wordCount = count($matches[0]);
                                    $tagCount = !empty($tag) ? substr_count($cleanedBody, $tag) : 0;
                                    $tagPercentage = ($tagCount > 0) ? ($tagCount / $wordCount) * 100 : 0;
                                    $hasSubheading = isset($tag) && preg_match('/<h[2-3][^>]*>.*?' . preg_quote($tag, '/') . '.*?<\/h[2-3]>/', $body);
                                    $hasImageWithAlt = isset($tag) && preg_match('/<img[^>]+alt=["\']?[^"\'>]*' . preg_quote($tag, '/') . '[^"\'>]*["\']?[^>]*>/', $body);
                                    $hasInternalLinks = preg_match_all('/<a[^>]+href=["\']?([^"\'>]*)(["\'][^>]*>|[^>]*>)/', $body, $internalLinks) > 0;
                                    $hasContentEnhancingLinks = count(array_filter($internalLinks[1], function($link) {
                                        return str_contains($link, config()->get('app.url')) || str_contains($link, config()->get('app.url'));
                                    })) > 0;
                                    $shortParagraphs = array_reduce($paragraphs, function($carry, $paragraph) {
                                        return $carry + (str_word_count(trim($paragraph)) <= 4);
                                    }, 0) > 0;
                                    $hasHeadings = preg_match('/<h[2-3][^>]*>/', $body) > 0;
                                    $containsMedia = preg_match('/<img|<video|<iframe/', $body) > 0;
                                    $shortParagraphs = preg_match_all('/\b(\w+\s*){0,20}\b/', $cleanedBody) > 0;
                                @endphp

                                <div class="col-12 mb-3">
                                    <h6 class="mt-2">{{ __('messages.seo_post.basic_check') }}</h6>
                                    <ul class="seo-checklist list-unstyled">
                                        <li class="{{ $tagInTitle ? 'text-success' : 'text-danger' }}">
                                            <i class="bi {{ $tagInTitle ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-danger' }} me-2"></i>
                                            {{ __('messages.seo_post.add_primary_keyword_title') }}
                                        </li>
                                        <li class="{{ $tagInDescription  ? 'text-success' : 'text-danger' }}">
                                            <i class="bi {{ $tagInDescription ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-danger' }} me-2"></i>
                                            {{ __('messages.seo_post.add_primary_keyword_description') }}
                                        </li>
                                        <li class="{{ $tagInFirstParagraph ? 'text-success' : 'text-danger' }}">
                                            <i class="bi {{ $tagInFirstParagraph ? 'bi-check-circle-fill text-success' : 'bi-exclamation-circle-fill text-danger' }} me-2"></i>
                                            {{ __('messages.seo_post.use_primary_keyword_first_paragraph') }}
                                        </li>
                                        <li class="{{ $tagCounts > 2 ? 'text-success' : ($tagCounts <= 2 && $tagCounts > 0 ? 'text-warning' : 'text-danger') }}">
                                            <i class="bi {{ $tagCounts > 2 ? 'bi-check-circle-fill text-success' : ($tagCounts <= 2 && $tagCounts > 0 ? 'bi-exclamation-triangle-fill text-warning' : 'bi-exclamation-circle-fill text-danger') }} me-2"></i>
                                            {{ __('messages.seo_post.use_keyword_in_other_paragraphs') }}
                                        </li>
                                        <li class="{{ $wordCount > 500 ? 'text-success' : ($wordCount < 500 && $wordCount > 300 ? 'text-warning' : 'text-danger') }}">
                                            <i class="bi {{ $wordCount > 500 ? 'bi-check-circle-fill text-success' : ($wordCount < 500 && $wordCount > 300 ? 'bi-exclamation-triangle-fill text-warning': 'bi-exclamation-circle-fill text-danger') }} me-2"></i>
                                            {{ __('messages.seo_post.article_length_300_500_words') }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-12 mb-3">
                                    <h6 class="mt-2">{{ __('messages.seo_post.additional_check') }}</h6>
                                    <ul class="seo-checklist list-unstyled">
                                        <li class="{{ $hasSubheading ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $hasSubheading ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.use_subheadings_keyword') }}
                                        </li>
                                        <li class="{{ $hasImageWithAlt ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $hasImageWithAlt ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.add_image_with_alt') }}
                                        </li>
                                        <li class="{{ $tagPercentage >= 1 ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $tagPercentage >= 1 ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.keyword_percentage_text') }}
                                            ({{ number_format($tagPercentage, 2) }}
                                            %) {{ __('messages.seo_post.must_be_at_least_1') }}
                                        </li>
                                        <li class="{{ $hasInternalLinks ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $hasInternalLinks ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.enhance_content_internal_links') }}
                                        </li>
                                        <li class="{{ $hasContentEnhancingLinks ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $hasContentEnhancingLinks ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.add_content_enhancing_links') }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-12 mb-1">
                                    <h6 class="mt-2">{{ __('messages.seo_post.content_readability') }}</h6>
                                    <ul class="seo-checklist list-unstyled">
                                        <li class="{{ $shortParagraphs ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $shortParagraphs ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.use_short_paragraphs') }}
                                        </li>
                                        <li class="{{ $containsMedia ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $containsMedia ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.add_images_videos') }}
                                        </li>
                                        <li class="{{ $shortParagraphs ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $shortParagraphs ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.ensure_short_paragraphs') }}
                                        </li>
                                        <li class="{{ $hasHeadings ? 'text-success' : 'text-warning' }}">
                                            <i class="bi {{ $hasHeadings ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-warning' }} me-2"></i>
                                            {{ __('messages.seo_post.use_headings_properly') }}
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <!-- /Right -->
            </div>
        </form>
    </div>


</div>
@push('scripts')

    <script>
        // ================== CKEditor -> hidden input sync + live counters ==================
        document.addEventListener('DOMContentLoaded', function () {
            const hidden = document.getElementById('body-content-hidden');
            const wordEl = document.querySelector('[data-word-count]');
            const paraEl = document.querySelector('[data-paragraph-count]');
            const textarea = document.getElementById('post-ckeditor-content');
            if (!hidden) return;

            function stripHtml(html) {
                const tmp = document.createElement('div');
                tmp.innerHTML = html || '';
                return tmp.textContent || tmp.innerText || '';
            }

            function normalizeForParagraphs(html) {
                return (html || '')
                    .replace(/<br\s*\/?>/gi, '\n')
                    .replace(/<\/(p|div|li|h[1-6])>/gi, '\n\n');
            }

            function getCountsFromHtml(html) {
                if (!html) return {words: 0, paragraphs: 0};
                const normalized = normalizeForParagraphs(html);
                const text = stripHtml(normalized).replace(/\u00A0/g, ' ').trim();
                const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
                const paragraphs = text ? text.split(/\n+/).map(s => s.trim()).filter(Boolean).length : 0;
                return {words, paragraphs};
            }

            // Try to read content from CKEditor4, CKEditor5, editable div, or textarea
            function readEditorHtml() {
                // CKEditor4
                if (window.CKEDITOR && CKEDITOR.instances && Object.keys(CKEDITOR.instances).length) {
                    const inst = CKEDITOR.instances['post-ckeditor-content'] || CKEDITOR.instances[textarea && textarea.id] || CKEDITOR.instances[Object.keys(CKEDITOR.instances)[0]];
                    if (inst && typeof inst.getData === 'function') return inst.getData();
                }
                // CKEditor5 if you or your init code stored instance on the textarea
                if (textarea && textarea._ckEditorInstance && typeof textarea._ckEditorInstance.getData === 'function') {
                    return textarea._ckEditorInstance.getData();
                }
                // Try common CKEditor5 editable div
                const editable = document.querySelector('.ck-editor__editable[contenteditable="true"]');
                if (editable) return editable.innerHTML;
                // Fallback to textarea value
                if (textarea) return textarea.value;
                return '';
            }

            let lastHtml = null;

            function updateIfChanged() {
                const html = readEditorHtml();
                if (html === lastHtml) return;
                lastHtml = html;
                const counts = getCountsFromHtml(html);
                if (wordEl) wordEl.textContent = counts.words;
                if (paraEl) paraEl.textContent = counts.paragraphs;
                // set hidden value to HTML so Livewire receives formatted content
                hidden.value = html;
                // notify Livewire of change
                hidden.dispatchEvent(new Event('input', {bubbles: true}));
            }

            // Poll for changes (works regardless of how CKEditor was initialized)
            updateIfChanged();
            const poll = setInterval(updateIfChanged, 400);
            // stop polling after 30s (safety)
            setTimeout(() => clearInterval(poll), 30_000);

            // extra triggers
            window.addEventListener('focus', updateIfChanged);
            document.addEventListener('paste', () => setTimeout(updateIfChanged, 100));
        });
    </script>

    <script>
        // ================== Title/Description counters + chip-field aria sync ==================
        document.addEventListener('DOMContentLoaded', () => {
            // live counters for title/description even if model is .blur
            const bindCounter = (sel, counterSel) => {
                const el = document.querySelector(sel);
                const counter = document.querySelector(counterSel);
                if (!el || !counter) return;
                const update = () => {
                    counter.textContent = (el.value || '').length;
                };
                el.addEventListener('input', update);
                update();
            };
            bindCounter('#title', 'label[for="title"] .counter-soft');
            bindCounter('#description', 'label[for="description"] .counter-soft');

            // keep aria-checked of chips in sync (generic chip-field groups)
            document.querySelectorAll('.chip-field').forEach(group => {
                group.addEventListener('change', e => {
                    if (e.target && e.target.type === 'radio') {
                        const name = e.target.name;
                        group.querySelectorAll('input[type="radio"][name="' + name + '"]').forEach(r => {
                            r.setAttribute('aria-checked', r.checked ? 'true' : 'false');
                        });
                    }
                });
            });
        });
    </script>

@endpush

{{-- Modal for Video Dimensions --}}
<div class="modal fade" id="videoDimensionsModal" tabindex="-1" aria-labelledby="videoDimensionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoDimensionsModalLabel">
                    <i class="bi bi-aspect-ratio me-2"></i>تخصيص أبعاد الفيديو
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="videoUrlDisplay" class="form-label">الرابط</label>
                    <input type="text" class="form-control" id="videoUrlDisplay" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="videoWidth" class="form-label">العرض</label>
                        <input type="text" class="form-control" id="videoWidth" value="640px" placeholder="مثال: 100% أو 640px">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="videoHeight" class="form-label">الارتفاع</label>
                        <input type="text" class="form-control" id="videoHeight" value="360px" placeholder="مثال: 360px أو 480px">
                    </div>
                </div>
                <input type="hidden" id="pendingVideoUrl">
                <input type="hidden" id="pendingVideoMimeType">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="insertVideoWithDimensions()">
                    <i class="bi bi-check-circle me-1"></i>إدراج
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function insertVideoWithDimensions() {
        const width = document.getElementById('videoWidth').value || '640px';
        const height = document.getElementById('videoHeight').value || '360px';
        const url = document.getElementById('pendingVideoUrl').value;
        const mimeType = document.getElementById('pendingVideoMimeType').value;
        
        console.log('📐 Inserting video with dimensions:', {width, height, url, mimeType});
        
        // Check if we're editing an existing video iframe
        if (window.currentEditingVideo) {
            console.log('✏️ Updating existing video dimensions');
            // Update existing iframe dimensions
            window.currentEditingVideo.setAttribute('width', width);
            window.currentEditingVideo.setAttribute('height', height);
            window.currentEditingVideo.style.width = width;
            window.currentEditingVideo.style.height = height;
            
            // Clear the reference
            window.currentEditingVideo = null;
        } else if (window.CKEDITOR && CKEDITOR.instances['post-ckeditor-content']) {
            console.log('➕ Inserting new video iframe');
            // Insert new video as iframe
            const editor = CKEDITOR.instances['post-ckeditor-content'];
            
            const html = `<p><iframe src="${url}" width="${width}" height="${height}" style="max-width: 100%;" frameborder="0" allowfullscreen allow="autoplay; encrypted-media" data-video-url="${url}" data-video-mime="${mimeType}"></iframe></p>`;
            
            console.log('📝 HTML to insert:', html);
            editor.insertHtml(html);
            console.log('✅ Video iframe inserted successfully');
        }
        
        // Close the modal
        $('#videoDimensionsModal').modal('hide');
    }
</script>
