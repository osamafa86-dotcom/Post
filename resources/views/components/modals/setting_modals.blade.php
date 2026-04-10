@php use App\Enums\WaterMarkEnum; @endphp
<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-fluid">
        <form wire:submit.prevent="updateSetting">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body d-flex flex-wrap justify-content-between align-items-center w-100">
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6 w-100 pb-5">
                                @can('settings_general_settings')
                                    <li class="nav-item">
                                        <a class="nav-link fs-3 {{ $tab == 'public' ? 'active' : '' }}"
                                           wire:click="openTab('public')" data-bs-toggle="tab"
                                           href="#kt_tab_pane_1">{{ __('messages.settings.general_settings') }}</a>
                                    </li>
                                @endcan

                                {{--    <li class="nav-item">
                                        <a class="nav-link fs-3 {{$tab == 'contact' ? 'active' : ''}}"
                                        wire:click="openTab('contact')" data-bs-toggle="tab" href="#kt_tab_pane_2">{{ __('messages.contact_us') }}</a>
                                    </li> --}}
                                {{--                                <li class="nav-item"> --}}
                                {{--                                    <a class="nav-link fs-3 {{$tab == 'open_graph' ? 'active' : ''}}" --}}
                                {{--                                       wire:click="openTab('open_graph')" data-bs-toggle="tab" --}}
                                {{--                                       href="#kt_tab_pane_3">{{ __('messages.settings.open_graph') }}</a> --}}
                                {{--                                </li> --}}
                                @can('settings_extra_codes')
                                    <li class="nav-item">
                                        <a class="nav-link fs-3 {{ $tab == 'extra_code' ? 'active' : '' }}"
                                           wire:click="openTab('extra_code')" data-bs-toggle="tab"
                                           href="#kt_tab_pane_4">{{ __('messages.settings.extra_codes') }}</a>
                                    </li>
                                @endcan
                                {{--                                <li class="nav-item"> --}}
                                {{--                                    <a class="nav-link fs-3 {{$tab == 'water_mark' ? 'active' : ''}}" --}}
                                {{--                                       wire:click="openTab('water_mark')" data-bs-toggle="tab" --}}
                                {{--                                       href="#kt_tab_pane_5">{{ __('messages.settings.watermark') }}</a> --}}
                                {{--                                </li> --}}
                                @can('settings_custom_tags')
                                    <li class="nav-item">
                                        <a class="nav-link fs-3 {{ $tab == 'tags' ? 'active' : '' }}"
                                           wire:click="openTab('tags')" data-bs-toggle="tab"
                                           href="#kt_tab_pane_6">{{ __('messages.settings.tags') }}</a>
                                    </li>
                                @endcan
                                @if(env('APP_LUNCH')=='adani_cast')
                                    <li class="nav-item">
                                        <a class="nav-link fs-3 {{ $tab == 'landing_page_information' ? 'active' : '' }}"
                                           wire:click="openTab('landing_page_information')" data-bs-toggle="tab"
                                           href="#kt_tab_pane_7">{{ __('messages.landing_page_information') }}</a>
                                    </li>
                                @endif
                                @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags'])
                                    <li class="nav-item ms-auto">
                                        <button type="submit"
                                                class="nav-link btn btn-primary px-13 text-white">{{ __('messages.save') }}</button>
                                    </li>
                                @endcanany
                            </ul>
                            <div class="tab-content  w-100 " id="myTabContent">
                                @can('settings_general_settings')
                                    <div class="tab-pane fade {{ $tab == 'public' ? 'show active' : '' }}"
                                         id="kt_tab_pane_1" role="tabpanel">
                                        <div class="row">

                                            {{--                                        <div class="form-group col-md-6 mb-3"> --}}
                                            {{--                                            <label class="fs-4" --}}
                                            {{--                                                   for="site_name_en">{{ __('messages.settings.site_name') }} --}}
                                            {{--                                                (en)</label> --}}
                                            {{--                                            <input @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled --}}
                                            {{--                                                   @endcanany wire:model="state.site_name_en" type="text" --}}
                                            {{--                                                   class="form-control @error('site_name_en') is-invalid @enderror" --}}
                                            {{--                                                   id="site_name_en"> --}}
                                            {{--                                            @error('site_name_en') --}}
                                            {{--                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div> --}}
                                            {{--                                            @enderror --}}
                                            {{--                                        </div> --}}

                                            <div class="form-group col-md-12 mb-3">
                                                <label class="fs-4"
                                                       for="site_name">{{ __('messages.settings.site_name') }}
                                                </label>
                                                <input
                                                    @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                    @endcanany
                                                    wire:model="state.site_name" type="text"
                                                    class="form-control @error('site_name') is-invalid @enderror"
                                                    id="site_name">
                                                @error('site_name')
                                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            {{--                                        <div class="form-group col-md-6 mb-3"> --}}
                                            {{--                                            <label class="fs-4" --}}
                                            {{--                                                   for="light_site_name_en">{{ __('messages.settings.light_site_name') }} --}}
                                            {{--                                                (en)</label> --}}
                                            {{--                                            <input @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled --}}
                                            {{--                                                   @endcanany wire:model="state.light_site_name_en" type="text" --}}
                                            {{--                                                   class="form-control @error('light_site_name_en') is-invalid @enderror" --}}
                                            {{--                                                   id="light_site_name_en"> --}}
                                            {{--                                            @error('light_site_name_en') --}}
                                            {{--                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div> --}}
                                            {{--                                            @enderror --}}
                                            {{--                                        </div> --}}
                                            <div class="form-group col-md-12 mb-3">
                                                <label class="fs-4"
                                                       for="light_site_name">{{ __('messages.settings.light_site_name') }}
                                                </label>
                                                <input
                                                    @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                    @endcanany
                                                    wire:model="state.light_site_name" type="text"
                                                    class="form-control @error('light_site_name') is-invalid @enderror"
                                                    id="light_site_name">
                                                @error('light_site_name')
                                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 mb-3">
                                            <label class="fs-4"
                                                   for="site_email">{{ __('messages.settings.site_email') }}</label>
                                            <input
                                                @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                @endcanany
                                                wire:model="state.site_email" type="email"
                                                class="form-control @error('site_email') is-invalid @enderror"
                                                id="site_email">
                                            @error('site_email')
                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-12 mb-3">
                                            <label class="fs-4"
                                                   for="phone">{{ __('messages.settings.phone') }}</label>
                                            <input
                                                @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                @endcanany
                                                wire:model="state.phone" type="text"
                                                class="form-control @error('phone') is-invalid @enderror" id="phone">
                                            @error('phone')
                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-12 mb-3">
                                            <label class="fs-4"
                                                   for="address">{{ __('messages.settings.address') }}</label>
                                            <input
                                                @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                @endcanany
                                                wire:model="state.address" type="text"
                                                class="form-control @error('address') is-invalid @enderror"
                                                id="address">
                                            @error('address')
                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if($isCillium)
                                            <div class="form-group col-md-12 mb-3">
                                                <label class="fs-4"
                                                       for="address">{{ __('messages.settings.podcast_url') }}</label>
                                                <input
                                                    @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                    @endcanany
                                                    wire:model="state.podcast_url" type="text"
                                                    class="form-control @error('podcast_url') is-invalid @enderror"
                                                    id="podcast_url">
                                                @error('podcast_url')
                                                <div class="invalid-feedback"
                                                     style="font-size: 15px">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                        @if($isTayqan)
                                            <div class="form-group col-md-12 mb-3">
                                                <label class="fs-4"
                                                       for="claim_url">{{ __('messages.settings.claim_url') }}</label>
                                                <input
                                                    @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                    @endcanany
                                                    wire:model="state.claim_url" type="text"
                                                    class="form-control @error('claim_url') is-invalid @enderror"
                                                    id="claim_url">
                                                @error('claim_url')
                                                <div class="invalid-feedback"
                                                     style="font-size: 15px">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                        @if($isAlmohajer || $isTayqan)
                                            <div class="form-group col-md-12 mb-3">
                                                <label class="fs-4"
                                                       for="whatsapp">{{ __('messages.settings.whatsapp_link') }}</label>
                                                <input
                                                    @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                    @endcanany
                                                    wire:model="state.whatsapp_link" type="text"
                                                    class="form-control @error('whatsapp_link') is-invalid @enderror"
                                                    id="whatsapp">
                                                @error('whatsapp_link')
                                                <div class="invalid-feedback"
                                                     style="font-size: 15px">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="row">
                                            <div class="form-group col-md-3 mb-3">
                                                <label class="fs-4"
                                                       for="site_logo">{{ __('messages.settings.site_logo') }}</label>
                                                <br>
                                                <div class="position-relative mb-3"
                                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                                    <img
                                                        src="{{ isset($state['site_logo_name']) ? file_url($state['site_logo_name']) : 'https://dummyimage.com/' . config('features.image_sizes.settings.site_logo') . '/dddddd/000000' }}"
                                                        alt="{{ __('messages.cover_image') }}" id="imagePreview"
                                                        class="img-fluid border"
                                                        style="cursor: pointer; border-radius: 8px;"
                                                        @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information'])onclick=" Livewire.dispatch('columnDefined', { column: 'site_logo' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');"@endcanany>

                                                    <!-- زر الإغلاق أعلى الصورة -->
                                                    <button type="button"
                                                            class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                            aria-label="Close"
                                                            wire:click.prevent="clearColumn('site_logo')"
                                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                                    @error('site_logo')
                                                    <div class="invalid-feedback" style="font-size: 15px">
                                                        {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 mb-3">
                                                <label class="fs-4"
                                                       for="footer_logo">{{ __('messages.settings.footer_logo') }}
                                                </label>
                                                <br>
                                                <div class="position-relative mb-3"
                                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                                    <img
                                                        src="{{ isset($state['footer_logo_name']) ? file_url($state['footer_logo_name']) : 'https://dummyimage.com/' . config('features.image_sizes.settings.footer_logo') . '/dddddd/000000' }}"
                                                        alt="{{ __('messages.settings.footer_image') }}"
                                                        id="footerImagePreview" class="img-fluid border"
                                                        style="cursor: pointer; border-radius: 8px;"
                                                        @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information'])onclick=" Livewire.dispatch('columnDefined', { column: 'footer_logo' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');"@endcanany>

                                                    <button type="button"
                                                            class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                            aria-label="Close"
                                                            wire:click.prevent="clearColumn('footer_logo')"
                                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                                    @error('footer_logo')
                                                    <div class="invalid-feedback" style="font-size: 15px">
                                                        {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 mb-3">
                                                <label class="fs-4"
                                                       for="favicon">{{ __('messages.settings.favicon') }}
                                                </label>
                                                <br>
                                                <div class="position-relative mb-3"
                                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                                    <img
                                                        src="{{ isset($state['favicon_name']) ? file_url($state['favicon_name']) : 'https://dummyimage.com/' . config('features.image_sizes.settings.favicon_image') . '/dddddd/000000' }}"
                                                        alt="{{ __('messages.cover_image') }}" id="faviconImagePreview"
                                                        class="img-fluid border"
                                                        style="cursor: pointer; border-radius: 8px;"
                                                        @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information'])onclick=" Livewire.dispatch('columnDefined', { column: 'favicon' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');"@endcanany>

                                                    <!-- زر الإغلاق أعلى الصورة -->
                                                    <button type="button" wire:click.prevent="clearColumn('favicon')"
                                                            class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                            aria-label="Close"
                                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                                    @error('favicon')
                                                    <div class="invalid-feedback" style="font-size: 15px">
                                                        {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            @if ($isHodhod || ($state['is_hodhod'] ?? false))
                                                <div class="form-group col-md-3 mb-3">
                                                    <label class="fs-4"
                                                           for="hodhod_banner">{{ __('messages.settings.hodhod_banner') }}</label>
                                                    <br>
                                                    <div class="position-relative mb-3"
                                                         style="display: inline-block; width: 100%; max-width: 150px;">

                                                        <img src="{{ isset($state['hodhod_banner_name'])
                                                            ? file_url($state['hodhod_banner_name'])
                                                            :'https://dummyimage.com/' . config('features.image_sizes.settings.banner') . '/dddddd/000000' }}"
                                                             alt="{{ __('messages.settings.hodhod_banner') }}"
                                                             id="hodhodBannerImagePreview" class="img-fluid border"
                                                             style="cursor: pointer; border-radius: 8px;"
                                                             @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information'])
                                                                 onclick="
                    Livewire.dispatch('columnDefined', { column: 'hodhod_banner' });
                    Livewire.dispatch('typeDefined', { type: 'images' });
                    Livewire.dispatch('isMultiple', { active: false });
                    $('#fileLibraryModal').modal('show');
                "
                                                            @endcanany>

                                                        <button type="button"
                                                                class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                                aria-label="Close"
                                                                wire:click.prevent="clearColumn('hodhod_banner')"
                                                                style="background-color: white; border-radius: 50%; border: 1px solid #ccc;">
                                                        </button>

                                                        @error('hodhod_banner')
                                                        <div class="invalid-feedback" style="font-size: 15px">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                        <div class="row">
                                            {{--                                        <div class="form-group col-md-6 mb-3"> --}}
                                            {{--                                            <label class="fs-4" --}}
                                            {{--                                                   for="footer_description_en">{{ __('messages.settings.footer_description') }} --}}
                                            {{--                                                (en)</label> --}}
                                            {{--                                            <textarea @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled --}}
                                            {{--                                                      @endcanany wire:model="state.footer_description_en" type="text" --}}
                                            {{--                                                      class="form-control @error('footer_description_en') is-invalid @enderror" --}}
                                            {{--                                                      id="footer_description_en"></textarea> --}}
                                            {{--                                            @error('footer_description_en') --}}
                                            {{--                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div> --}}
                                            {{--                                            @enderror --}}
                                            {{--                                        </div> --}}
                                            {{--                                        <div class="form-group col-md-12 mb-3"> --}}
                                            {{--                                            <label class="fs-4" --}}
                                            {{--                                                   for="footer_description">{{ __('messages.settings.footer_description') }} --}}
                                            {{--                                               </label> --}}
                                            {{--                                            <textarea @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled --}}
                                            {{--                                                      @endcanany wire:model="state.footer_description" type="text" --}}
                                            {{--                                                      class="form-control @error('footer_description') is-invalid @enderror" --}}
                                            {{--                                                      id="footer_description"></textarea> --}}
                                            {{--                                            @error('footer_description') --}}
                                            {{--                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div> --}}
                                            {{--                                            @enderror --}}
                                            {{--                                        </div> --}}
                                        </div>
                                        <div class="row">
                                            {{--                                        <div class="form-group col-md-6 mb-3"> --}}
                                            {{--                                            <label class="fs-4" --}}
                                            {{--                                                   for="footer_description_en">{{ __('messages.settings.footer_description') }} --}}
                                            {{--                                                (en)</label> --}}
                                            {{--                                            <textarea @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled --}}
                                            {{--                                                      @endcanany wire:model="state.footer_description_en" type="text" --}}
                                            {{--                                                      class="form-control @error('footer_description_en') is-invalid @enderror" --}}
                                            {{--                                                      id="footer_description_en"></textarea> --}}
                                            {{--                                            @error('footer_description_en') --}}
                                            {{--                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div> --}}
                                            {{--                                            @enderror --}}
                                            {{--                                        </div> --}}

                                            <div class="form-group col-md-12 mb-3">
                                                <label class="fs-4"
                                                       for="footer_description">{{ __('messages.settings.footer_description') }}
                                                </label>
                                                <textarea
                                                    @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                    @endcanany
                                                    wire:model="state.footer_description" type="text"
                                                    class="form-control @error('footer_description') is-invalid @enderror"
                                                    id="footer_description"></textarea>
                                                @error('footer_description')
                                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}
                                                </div>
                                                @enderror
                                            </div>


                                        </div>

                                        <div class="row">
                                            {{--                                        <div class="form-group col-md-6 mb-3"> --}}
                                            {{--                                            <label class="fs-4" --}}
                                            {{--                                                   for="copyright_text_en">{{ __('messages.settings.copyright_text') }} --}}
                                            {{--                                                (en)</label> --}}
                                            {{--                                            <input @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled --}}
                                            {{--                                                   @endcanany wire:model="state.copyright_text_en" type="text" --}}
                                            {{--                                                   class="form-control @error('copyright_text_en') is-invalid @enderror" --}}
                                            {{--                                                   id="copyright_text_en"> --}}
                                            {{--                                            @error('copyright_text_en') --}}
                                            {{--                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div> --}}
                                            {{--                                            @enderror --}}
                                            {{--                                        </div> --}}
                                            <div class="form-group col-md-12 mb-3">
                                                <label class="fs-4"
                                                       for="copyright_text">{{ __('messages.settings.copyright_text') }}
                                                </label>
                                                <input
                                                    @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                    @endcanany
                                                    wire:model="state.copyright_text" type="text"
                                                    class="form-control @error('copyright_text') is-invalid @enderror"
                                                    id="copyright_text">
                                                @error('copyright_text')
                                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{--                                    <div class="form-check form-check-solid ms-2 col-md-12 mb-3"> --}}
                                        {{--                                        <label class="form-check-label fs-4" --}}
                                        {{--                                               for="website_active">{{ __('messages.settings.website_activation') }}</label> --}}
                                        {{--                                        <input @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled --}}
                                        {{--                                               @endcanany class="form-check-input @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled @endcanany @error('website_active') is-invalid @enderror" --}}
                                        {{--                                               wire:model="state.website_active" --}}
                                        {{--                                               type="checkbox" value="" id="website_active"> --}}
                                        {{--                                        @error('website_active') --}}
                                        {{--                                        <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div> --}}
                                        {{--                                        @enderror --}}
                                        {{--                                    </div> --}}
                                    </div>
                                @endcan
                                @can('settings_extra_codes')
                                    <div class="tab-pane fade {{ $tab == 'extra_code' ? 'show active' : '' }}"
                                         id="kt_tab_pane_4" role="tabpanel">
                                        <div class="form-group col-md-12 mb-3">
                                            <label class="fs-4"
                                                   for="header_code">{{ __('messages.settings.header_code') }}</label>
                                            <textarea
                                                @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                @endcanany
                                                wire:model="state.header_code" type="text" rows="7"
                                                class="form-control @error('header_code') is-invalid @enderror"
                                                id="header_code"></textarea>
                                            @error('header_code')
                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-12 mb-3">
                                            <label class="fs-4"
                                                   for="footer_code">{{ __('messages.settings.footer_code') }}</label>
                                            <textarea
                                                @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                @endcanany
                                                wire:model="state.footer_code" type="text" rows="7"
                                                class="form-control @error('footer_code') is-invalid @enderror"
                                                id="footer_code"></textarea>
                                            @error('footer_code')
                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                @endcan
                                @can('settings_custom_tags')
                                    <div class="tab-pane fade {{ $tab == 'tags' ? 'show active' : '' }}"
                                         id="kt_tab_pane_6" role="tabpanel">

                                        <div class="form-group col-md-12 mb-3">
                                            <div wire:ignore>
                                                <label class="fs-4" for="tags">{{ __('messages.settings.tags') }}
                                                </label>

                                                <input
                                                    @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                    @endcanany
                                                    wire:model="state.tags" type="text"
                                                    class="form-control @error('tags') is-invalid @enderror"
                                                    id="tags">
                                                @error('tags')
                                                <div class="invalid-feedback" style="font-size: 15px">{{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                @endcan
                                @if(env('APP_LUNCH')=='adani_cast')
                                    <div
                                        class="tab-pane fade {{ $tab == 'landing_page_information' ? 'show active' : '' }}"
                                        id="kt_tab_pane_7" role="tabpanel">
                                        <div class="form-group col-md-3 mb-3">
                                            <label class="fs-4"
                                                   for="landing_page_url">{{ __('messages.settings.landing_page_url') }}
                                            </label>
                                            <input
                                                @canany(['settings_general_settings', 'settings_extra_codes', 'settings_custom_tags', 'settings_landing_page_information']) @else disabled
                                                @endcanany
                                                wire:model="state.landing_page_url" type="url"
                                                class="form-control @error('landing_page_url') is-invalid @enderror"
                                                id="landing_page_url">
                                            @error('landing_page_url')
                                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <br>
                                        <div class="row">

                                            <div class="form-group col-md-3 mb-3">
                                                <label class="fs-4"
                                                       for="landingPageInformationPicturesTopLeft">{{ __('messages.settings.top_left') }}
                                                </label>
                                                <br>
                                                <div class="position-relative mb-3"
                                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                                    <img
                                                        src="{{ isset($state['landingPageInformationPicturesTopLeft_name']) ? file_url($state['landingPageInformationPicturesTopLeft_name']) : 'https://dummyimage.com/' . config('features.image_sizes.landing_page.top_left') . '/dddddd/000000' }}"
                                                        alt="{{ __('messages.settings.top_left') }}" id="imagePreview"
                                                        class="img-fluid border"
                                                        style="cursor: pointer; border-radius: 8px;"
                                                        @if(env('APP_LUNCH')=='adani_cast') onclick=" Livewire.dispatch('columnDefined', { column: 'landingPageInformationPicturesTopLeft' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');"@endif>

                                                    <!-- زر الإغلاق أعلى الصورة -->
                                                    <button type="button"
                                                            class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                            aria-label="Close"
                                                            wire:click.prevent="clearColumn('landingPageInformationPicturesTopLeft')"
                                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                                    @error('landingPageInformationPicturesTopLeft')
                                                    <div class="invalid-feedback" style="font-size: 15px">
                                                        {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 mb-3">
                                                <label class="fs-4"
                                                       for="landingPageInformationPicturesTopRight">{{ __('messages.settings.top_right') }}
                                                </label>
                                                <br>
                                                <div class="position-relative mb-3"
                                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                                    <img
                                                        src="{{ isset($state['landingPageInformationPicturesTopRight_name']) ? file_url($state['landingPageInformationPicturesTopRight_name']) : 'https://dummyimage.com/' . config('features.image_sizes.landing_page.top_right') . '/dddddd/000000' }}"
                                                        alt="{{ __('messages.settings.top_right') }}" id="imagePreview"
                                                        class="img-fluid border"
                                                        style="cursor: pointer; border-radius: 8px;"
                                                        @if(env('APP_LUNCH')=='adani_cast') onclick=" Livewire.dispatch('columnDefined', { column: 'landingPageInformationPicturesTopRight' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');"@endif>

                                                    <!-- زر الإغلاق أعلى الصورة -->
                                                    <button type="button"
                                                            class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                            aria-label="Close"
                                                            wire:click.prevent="clearColumn('landingPageInformationPicturesTopRight')"
                                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                                    @error('landingPageInformationPicturesTopRight')
                                                    <div class="invalid-feedback" style="font-size: 15px">
                                                        {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 mb-3">
                                                <label class="fs-4"
                                                       for="landingPageInformationPicturesBottomLeft">{{ __('messages.settings.bottom_left') }}
                                                </label>
                                                <br>
                                                <div class="position-relative mb-3"
                                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                                    <img
                                                        src="{{ isset($state['landingPageInformationPicturesBottomLeft_name']) ? file_url($state['landingPageInformationPicturesBottomLeft_name']) : 'https://dummyimage.com/' . config('features.image_sizes.landing_page.bottom_left') . '/dddddd/000000' }}"
                                                        alt="{{ __('messages.settings.bottom_left') }}"
                                                        id="imagePreview"
                                                        class="img-fluid border"
                                                        style="cursor: pointer; border-radius: 8px;"
                                                        @if(env('APP_LUNCH')=='adani_cast') onclick=" Livewire.dispatch('columnDefined', { column: 'landingPageInformationPicturesBottomLeft' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');"@endif>

                                                    <!-- زر الإغلاق أعلى الصورة -->
                                                    <button type="button"
                                                            class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                            aria-label="Close"
                                                            wire:click.prevent="clearColumn('landingPageInformationPicturesBottomLeft')"
                                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                                    @error('landingPageInformationPicturesBottomLeft')
                                                    <div class="invalid-feedback" style="font-size: 15px">
                                                        {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3 mb-3">
                                                <label class="fs-4"
                                                       for="landingPageInformationPicturesBottomRight">{{ __('messages.settings.bottom_right') }}
                                                </label>
                                                <br>
                                                <div class="position-relative mb-3"
                                                     style="display: inline-block; width: 100%; max-width: 150px;">
                                                    <img
                                                        src="{{ isset($state['landingPageInformationPicturesBottomRight_name']) ? file_url($state['landingPageInformationPicturesBottomRight_name']) : 'https://dummyimage.com/' . config('features.image_sizes.landing_page.bottom_right') . '/dddddd/000000' }}"
                                                        alt="{{ __('messages.settings.bottom_right') }}"
                                                        id="imagePreview"
                                                        class="img-fluid border"
                                                        style="cursor: pointer; border-radius: 8px;"
                                                        @if(env('APP_LUNCH')=='adani_cast') onclick=" Livewire.dispatch('columnDefined', { column: 'landingPageInformationPicturesBottomRight' });Livewire.dispatch('typeDefined', { type: 'images' });Livewire.dispatch('isMultiple', { active: false }); $('#fileLibraryModal').modal('show');"@endif>

                                                    <!-- زر الإغلاق أعلى الصورة -->
                                                    <button type="button"
                                                            class="btn-close position-absolute top-0 start-0 m-2 p-2"
                                                            aria-label="Close"
                                                            wire:click.prevent="clearColumn('landingPageInformationPicturesBottomRight')"
                                                            style="background-color: white; border-radius: 50%; border: 1px solid #ccc;"></button>
                                                    @error('landingPageInformationPicturesBottomRight')
                                                    <div class="invalid-feedback" style="font-size: 15px">
                                                        {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
