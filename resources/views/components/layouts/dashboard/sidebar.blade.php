@php use App\Enums\PublishEnum;use App\Models\Alert;use App\Models\Post; @endphp

    <!--begin::Sidebar-->
<div id="kt_app_sidebar"
     class="app-sidebar flex-column"
     data-kt-drawer="true"
     data-kt-drawer-name="app-sidebar"
     data-kt-drawer-activate="{default: true, lg: false}"
     data-kt-drawer-overlay="true"
     data-kt-drawer-width="225px"
     data-kt-drawer-direction="start"
     data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        @php
            /*
             |--------------------------------------------------------------------------
             | Company-aware + Theme-aware logos
             | - Keeps backend intact; only CSS switches light/dark variants.
             |--------------------------------------------------------------------------
             */
            $company = strtolower(trim($_ENV['COMPANY_LUNCH']
                ?? $_SERVER['COMPANY_LUNCH']
                ?? env('COMPANY_LUNCH', '')));
        @endphp

        <a href="{{ route('dashboard.main') }}"
           class="d-flex flex-column align-items-center text-decoration-none">

            @if($company === 'zentra')
                {{-- Zentra: light / dark / minimized --}}
                <img src="{{ asset('assets/dashboard/images/zentra/logo-light.png') }}"
                     alt="Zentra Logo Light"
                     class="app-sidebar-logo-default logo-light"
                     style="max-width: 140px;">
                <img src="{{ asset('assets/dashboard/images/zentra/logo-dark.png') }}"
                     alt="Zentra Logo Dark"
                     class="app-sidebar-logo-default logo-dark"
                     style="max-width: 140px;">
                <img src="{{ asset('assets/dashboard/images/zentra/logo-icon.png') }}"
                     alt="Zentra Mini Logo"
                     class="app-sidebar-logo-minimize"
                     style="width: 40px;">
            @else
                {{-- Taktek: light / dark / minimized --}}
                <img src="{{ asset('assets/dashboard/images/logo-light.png') }}"
                     alt="Taktek Logo Light"
                     class="app-sidebar-logo-default logo-light"
                     style="max-width: 140px;">
                <img src="{{ asset('assets/dashboard/images/logo-dark.png') }}"
                     alt="Taktek Logo Dark"
                     class="app-sidebar-logo-default logo-dark"
                     style="max-width: 140px;">
                <img src="{{ asset('assets/dashboard/images/logo-icon.png') }}"
                     alt="Taktek Mini Logo"
                     class="app-sidebar-logo-minimize"
                     style="width: 40px;">
            @endif
        </a>

        <!-- Toggle (minimize) -->
        <div id="kt_app_sidebar_toggle"
             class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
             data-kt-toggle="true"
             data-kt-toggle-state="active"
             data-kt-toggle-target="body"
             data-kt-toggle-name="app-sidebar-minimize">
            <i class="bi bi-arrow-left-circle fs-3 rotate-180"></i>
        </div>
    </div>
    <!--end::Logo-->

    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <div id="kt_app_sidebar_menu_scroll"
                 class="scroll-y my-5 mx-1"
                 data-kt-scroll="true"
                 data-kt-scroll-activate="true"
                 data-kt-scroll-height="auto"
                 data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                 data-kt-scroll-wrappers="#kt_app_sidebar_menu"
                 data-kt-scroll-offset="5px"
                 data-kt-scroll-save-state="true">

                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6 px-2"
                     id="#kt_app_sidebar_menu"
                     data-kt-menu="true"
                     data-kt-menu-expand="false">

                    <!-- ================= Dashboard ================= -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('dashboard.main') ? 'active' : '' }}"
                           href="{{ route('dashboard.main') }}">
                            <span class="menu-icon">
                                <i class="bi bi-speedometer fs-2"></i>
                            </span>
                            <span class="menu-title">{{ __('messages.sidebar.dashboard') }}</span>
                        </a>
                    </div>

                    <!-- ================= CONTENT ================= -->
                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                        <div class="menu-item">
                            <div class="menu-content pt-8 pb-2">
                                <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                    {{ __('messages.sidebar.CONTENT') }}
                                </span>
                            </div>
                        </div>

                        @if (config('features.permissions.posts'))
                            @php
                                $content_menu = [
                                    'dashboard.posts',
                                    'dashboard.draft_posts',
                                    'dashboard.deleted_posts',
                                    'dashboard.posts.create_update_post',
                                    'dashboard.pending_actions',
                                ];
                            @endphp
                            @can('posts_show')
                                <div data-kt-menu-trigger="click"
                                     class="menu-item menu-accordion {{ in_array(request()->route()->getName(), $content_menu) ? 'show here' : '' }}">
                                    <span class="menu-link">
                                        <span class="menu-icon"><i class="bi bi-file-earmark-post fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.posts') }}</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <div class="menu-sub menu-sub-accordion">
                                        @can('posts_create')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.posts.create_update_post') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.posts.create_update_post') }}">
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.add_post') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                        @can('posts_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.posts') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.posts') }}">
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.all_posts') }}</span>
                                                </a>
                                            </div>
                                        @endcan

                                        @php  $posts_count= Post::query()->where('publish_status', PublishEnum::PENDING->value)->count(); @endphp
                                        @can('pending_actions_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.pending_actions') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.pending_actions') }}">
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.pending_actions') }}</span>
                                                    @if($posts_count > 0)
                                                        <span class="menu-badge badge-pulse"
                                                              aria-label="Pending">{{ $posts_count }}</span>
                                                    @endif
                                                </a>
                                            </div>
                                        @endcan

                                        @canany(['posts_create', 'posts_edit'])
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.draft_posts') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.draft_posts') }}">
                                                    <span class="menu-title">{{ __('messages.sidebar.draft') }}</span>
                                                </a>
                                            </div>
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.deleted_posts') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.deleted_posts') }}">
                                                    <span class="menu-title">{{ __('messages.sidebar.trash') }}</span>
                                                </a>
                                            </div>
                                        @endcanany
                                    </div>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.materials') && config('features.permissions.materials_albums'))
                            @php $podcasts_menu = ['dashboard.materials.albums','dashboard.materials']; @endphp
                            @canany(['materials_albums_show','materials_show'])
                                <div data-kt-menu-trigger="click"
                                     class="menu-item menu-accordion {{ in_array(request()->route()->getName(), $podcasts_menu) ? 'show here' : '' }}">
                                    <span class="menu-link">
                                        <span class="menu-icon"><i class="bi bi-mic fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.materials') }}</span>
                                        <span class="menu-arrow"></span>
                                    </span>
                                    <div class="menu-sub menu-sub-accordion">
                                        @can('materials_albums_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.materials.albums') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.materials.albums') }}">
                                                    <span class="menu-title">{{ __('messages.sidebar.albums') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                        @can('materials_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.materials') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.materials') }}">
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.materials') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            @endcanany
                        @endif

                        @if (config('features.permissions.breaking_news'))
                            @can('breaking_news_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.breaking_news') ? 'active' : '' }}"
                                       href="{{ route('dashboard.breaking_news') }}">
                                        <span class="menu-icon"><i class="bi bi-newspaper fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.breaking_news') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.events'))
                            @can('events_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.events') ? 'active' : '' }}"
                                       href="{{ route('dashboard.events') }}">
                                        <span class="menu-icon"><i class="bi bi-calendar-event fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.events') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.sort_contents'))
                            @can('sort_contents_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.sort_contents') ? 'active' : '' }}"
                                       href="{{ route('dashboard.sort_contents') }}">
                                        <span class="menu-icon"><i class="bi bi-sort-alpha-down fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.content_sorting') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.advertisements'))
                            @can('advertisements_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.advertisements') ? 'active' : '' }}"
                                       href="{{ route('dashboard.advertisements') }}">
                                        <span class="menu-icon"><i class="bi bi-badge-ad fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.advertisements') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.special_pages'))
                            @can('special_pages_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.special_pages') ? 'active' : '' }}"
                                       href="{{ route('dashboard.special_pages') }}">
                                        <span class="menu-icon"><i class="bi bi-globe2 fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.special_pages') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.data_page'))
                            @can('data_page_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.data_page') ? 'active' : '' }}"
                                       href="{{ route('dashboard.data_page') }}">
                                        <span class="menu-icon"><i class="bi bi-send fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.data_page') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.quotes'))
                            @can('quotes_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.quotes') ? 'active' : '' }}"
                                       href="{{ route('dashboard.quotes') }}">
                                        <span class="menu-icon"><i class="bi bi-chat-left-quote fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.quotes') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.reels'))
                            @can('reels_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.reels') ? 'active' : '' }}"
                                       href="{{ route('dashboard.reels') }}">
                                        <span class="menu-icon"><i
                                                class="bi bi-camera-reels fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.reels') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (config('features.permissions.applications'))
                            @can('applications_show')
                                <div class="menu-item">
                                    <a class="menu-link {{ request()->routeIs('dashboard.applications') ? 'active' : '' }}"
                                       href="{{ route('dashboard.applications') }}">
                                        <span class="menu-icon"><i class="bi bi-chat-left-quote fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.sidebar.applications') }}</span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        <!-- ================= Sub Tables ================= -->
                        @php
                            $sub_tables_menu = ['dashboard.categories','dashboard.types','dashboard.tags','dashboard.participants','dashboard.icons','dashboard.special_files','dashboard.courses'];
                            $permission = ['categories_show','types_show','tags_show','participants_show','icons_show','special_files_show','courses_show'];
                            $hasAnyPermission = collect($permission)->contains(fn($p) => auth()->user()->can($p));
                        @endphp

                        @if($hasAnyPermission)
                            <div data-kt-menu-trigger="click"
                                 class="menu-item menu-accordion @if(in_array(request()->route()->getName(), $sub_tables_menu)) show here @endif">
                                <span class="menu-link">
                                    <span class="menu-icon"><i class="bi bi-list-columns-reverse fs-2"></i></span>
                                    <span class="menu-title">{{ __('messages.sidebar.sub_tables') }}</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion">
                                    @if (config('features.permissions.categories'))
                                        @can('categories_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.categories') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.categories') }}">
                                                    <span class="menu-icon"><i class="bi bi-card-list fs-2"></i></span>
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.categories') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif

                                    @if (config('features.permissions.types'))
                                        @can('types_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.types') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.types') }}">
                                                    <span class="menu-icon"><i class="bi bi-text-wrap fs-2"></i></span>
                                                    <span class="menu-title">{{ __('messages.sidebar.types') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif

                                    @if (config('features.permissions.tags'))
                                        @can('tags_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.tags') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.tags') }}">
                                                    <span class="menu-icon"><i class="bi bi-tags fs-2"></i></span>
                                                    <span class="menu-title">{{ __('messages.sidebar.tags') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif

                                    @if (config('features.permissions.participants'))
                                        @can('participants_show')
                                            <div class="menu-item">
                                                @php
                                                    $title = (config('features.general.has_publishers') && config('features.general.has_resources'))
                                                        ? __('messages.sidebar.participants')
                                                        : __('messages.authors.authors');
                                                @endphp

                                                <a class="menu-link {{ request()->routeIs('dashboard.participants') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.participants') }}">
                                                    <span class="menu-icon"><i class="bi bi-people fs-2"></i></span>
                                                    <span class="menu-title">{{ $title }}</span>
                                                </a>

                                            </div>
                                        @endcan
                                    @endif

                                    @if (config('features.permissions.icons'))
                                        @can('icons_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.icons') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.icons') }}">
                                                    <span class="menu-icon"><i class="bi bi-image fs-2"></i></span>
                                                    <span class="menu-title">{{ __('messages.sidebar.icons') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif


                                    @if (config('features.permissions.services'))
                                        @can('services_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.services') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.services') }}">
                                                    <span class="menu-icon"><i class="bi bi-server fs-2"></i></span>
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.services') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif

                                    @if (config('features.permissions.courses'))
                                        @can('courses_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.courses') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.courses') }}">
                                                    <span class="menu-icon"><i class="bi bi-book-half fs-2"></i></span>
                                                    <span class="menu-title">{{ __('messages.sidebar.courses') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif

                                    @if (config('features.permissions.special_files'))
                                        @can('special_files_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.special_files') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.special_files') }}">
                                                    <span class="menu-icon"><i class="bi bi-file-lock fs-2"></i></span>
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.special_files') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        @endif
                        <!-- ============== End Sub Tables ============== -->

                        @php
                            $subscriptions_tables_menu = [ 'dashboard.subscriptions', 'dashboard.subscriber_notifications' ];
                            $perm_sub = ['subscriber_notifications_show' , 'subscriptions_show'];
                            $hasAnyPermissionSubscriptions = collect($perm_sub)->contains(fn($p) => auth()->user()->can($p));
                        @endphp
                        @if (config('features.permissions.subscriptions') && $hasAnyPermissionSubscriptions)
                            <div data-kt-menu-trigger="click"
                                 class="menu-item menu-accordion @if(in_array(request()->route()->getName(), $subscriptions_tables_menu)) show here @endif">
                                <span class="menu-link">
                                    <span class="menu-icon"><i class="bi bi-list-columns-reverse fs-2"></i></span>
                                    <span class="menu-title">{{ __('messages.sidebar.subscriptions_tables') }}</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion">
                                    @if (config('features.permissions.subscriptions'))
                                        @can('subscriptions_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.subscriptions') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.subscriptions') }}">
                                                    <span class="menu-icon"><i class="bi bi-cash fs-2"></i></span>
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.subscriptions') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif
                                    @if (config('features.permissions.subscriber_notifications'))
                                        @can('subscriber_notifications_show')
                                            <div class="menu-item">
                                                <a class="menu-link {{ request()->routeIs('dashboard.subscriber_notifications') ? 'active' : '' }}"
                                                   href="{{ route('dashboard.subscriber_notifications') }}">
                                                    <span class="menu-icon"><i class="bi bi-bell fs-2"></i></span>
                                                    <span
                                                        class="menu-title">{{ __('messages.sidebar.subscriber_notifications') }}</span>
                                                </a>
                                            </div>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- ============== ADMINISTRATION ============== -->

                    @php
                        $administration_menu = [
                          'dashboard.teams','dashboard.users','dashboard.user_details','dashboard.permissions',
                          'dashboard.permissions.create_update_permission','dashboard.publish_roles','dashboard.roles',
                          'dashboard.user_logs','dashboard.alerts','dashboard.navbar_links','dashboard.import',
                          'dashboard.settings','dashboard.news_letter_emails','dashboard.sends','dashboard.contact_us'
                        ];
                    @endphp

                    <div data-kt-menu-trigger="click"
                         class="menu-item menu-accordion @if(in_array(request()->route()->getName(), $administration_menu)) show here @endif">
                        <span class="menu-link">
                            <span class="menu-icon"><i class="bi bi-gear fs-2"></i></span>
                            <span class="menu-title">{{ __('messages.sidebar.administration_section') }}</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            @can('teams_show')
                                <div class="menu-item">
                                    <a href="{{ route('dashboard.teams') }}"
                                       class="menu-link {{ request()->routeIs('dashboard.teams') ? 'active' : '' }}">
                                        <span class="menu-icon"><i class="bi bi-mailbox fs-2"></i></span>
                                        <span class="menu-title">{{ __('messages.teams.title') }}</span>
                                    </a>
                                </div>
                            @endcan

                            @if (config('features.permissions.users'))
                                @can('users_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.users') ? 'active' : '' }}"
                                           href="{{ route('dashboard.users') }}">
                                            <span class="menu-icon"><i class="bi bi-person-gear fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.supervisors') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.user_details'))
                                @can('user_details_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.user_details') ? 'active' : '' }}"
                                           href="{{ route('dashboard.user_details') }}">
                                            <span class="menu-icon"><i class="bi bi-file-earmark-post fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.user_details') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.permissions'))
                                @can('permissions_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.permissions') || request()->routeIs('dashboard.permissions.create_update_permission') ? 'active' : '' }}"
                                           href="{{ route('dashboard.permissions') }}">
                                            <span class="menu-icon"><i class="bi bi-key fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.permissions') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.publish_roles'))
                                @can('publish_roles_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.publish_roles') ? 'active' : '' }}"
                                           href="{{ route('dashboard.publish_roles') }}">
                                            <span class="menu-icon"><i class="bi bi-bar-chart-steps fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.publish_role') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.roles'))
                                @can('roles_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.roles') ? 'active' : '' }}"
                                           href="{{ route('dashboard.roles') }}">
                                            <span class="menu-icon"><i class="bi bi-file-earmark-post fs-2"></i></span>
                                            <span
                                                class="menu-title">{{ __('messages.sidebar.notification_roles') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.user_logs'))
                                @can('user_logs_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.user_logs') ? 'active' : '' }}"
                                           href="{{ route('dashboard.user_logs') }}">
                                            <span class="menu-icon"><i
                                                    class="bi bi-person-fill-exclamation fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.user_logs') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.alerts'))
                                @can('alerts_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.alerts') ? 'active' : '' }}"
                                           href="{{ route('dashboard.alerts') }}">
                                            <span class="menu-icon"><i class="bi bi-bell fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.alerts') }}</span>
                                            @php $count = Alert::where('status', false)->count() @endphp
                                            @if($count > 0)
                                                <span class="menu-badge badge-pulse">{{ $count }}</span>
                                            @endif
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.navbar_links'))
                                @can('navbar_links_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.navbar_links') ? 'active' : '' }}"
                                           href="{{ route('dashboard.navbar_links') }}">
                                            <span class="menu-icon"><i class="bi bi-segmented-nav fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.menus') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.import'))
                                @can('import_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.import') ? 'active' : '' }}"
                                           href="{{ route('dashboard.import') }}">
                                            <span class="menu-icon"><i class="bi bi-mailbox fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.import.import') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.settings'))
                                @canany(['settings_general_settings','settings_extra_codes','settings_custom_tags','settings_landing_page_information'])
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.settings') ? 'active' : '' }}"
                                           href="{{ route('dashboard.settings') }}">
                                            <span class="menu-icon"><i class="bi bi-gear fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.settings') }}</span>
                                        </a>
                                    </div>
                                @endcanany
                            @endif

                            @if (config('features.permissions.news_letter_emails'))
                                @can('news_letter_emails_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.news_letter_emails') ? 'active' : '' }}"
                                           href="{{ route('dashboard.news_letter_emails') }}">
                                            <span class="menu-icon"><i class="bi bi-send fs-2"></i></span>
                                            <span
                                                class="menu-title">{{ __('messages.sidebar.news_letter_emails') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.send_news'))
                                @can('send_news_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.sends') ? 'active' : '' }}"
                                           href="{{ route('dashboard.sends') }}">
                                            <span class="menu-icon"><i class="bi bi-send fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.submit_news') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif

                            @if (config('features.permissions.contact_us'))
                                @can('contact_us_show')
                                    <div class="menu-item">
                                        <a class="menu-link {{ request()->routeIs('dashboard.contact_us') ? 'active' : '' }}"
                                           href="{{ route('dashboard.contact_us') }}">
                                            <span class="menu-icon"><i class="bi bi-headset fs-2"></i></span>
                                            <span class="menu-title">{{ __('messages.sidebar.contact_us') }}</span>
                                        </a>
                                    </div>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
                <!--end::Menu-->
            </div>
        </div>
    </div>
    <!--end::sidebar menu-->
</div>
<!--end::Sidebar-->

<style>
    /* -----------------------------------------------------------------------------
       Sidebar UX polish (Light/Dark-aware). Uses your existing :root variables.
       No backend changes; purely presentational.
    ----------------------------------------------------------------------------- */

    /* 1) Theme-aware logo switching (no JS needed) */
    .logo-dark {
        display: none;
    }

    .logo-light {
        display: block;
    }

    [data-bs-theme="dark"] .logo-dark {
        display: block;
    }

    [data-bs-theme="dark"] .logo-light {
        display: none;
    }

    /* 2) Base container */
    .app-sidebar {
        background: var(--brand-sidebar-bg);
    }

    /* 3) Menu link: spacing, radius & transitions */
    .app-sidebar .menu .menu-link {
        border-radius: .65rem;
        padding: .6rem 1rem;

        color: var(--brand-sidebar-fg);
        transition: background-color .18s ease, color .18s ease, transform .12s ease;
    }

    /* 4) Hover state (subtle tint that works in dark & light) */
    .app-sidebar .menu .menu-item:not(.here) .menu-link:hover {
        background-color: color-mix(in srgb, var(--bs-card-bg) 86%, var(--bs-primary));
        color: var(--brand-fg);
    }

    /* 5) Active item: solid brand background + left indicator bar */
    .app-sidebar .menu .menu-item .menu-link.active {
        background-color: var(--brand-primary) !important;
        color: #fff !important;
        box-shadow: 0 1px 0 rgba(0, 0, 0, .04) inset, 0 1px 8px rgba(0, 0, 0, .08);
        position: relative;
    }

    .app-sidebar .menu .menu-item .menu-link.active::before {
        /* Accent bar (RTL-aware: use inline-start) */
        content: "";
        position: absolute;
        inset-inline-start: .35rem;
        top: .45rem;
        bottom: .45rem;
        width: 4px;
        border-radius: 4px;
        background: #fff;
        opacity: .9;
    }

    /* 6) Make icons/text white on active link */
    .app-sidebar .menu .menu-item .menu-link.active .menu-icon i,
    .app-sidebar .menu .menu-item .menu-link.active .menu-title {
        color: #fff !important;
    }

    /* 7) Accordion arrow rotation on open */
    .menu-accordion > .menu-link .menu-arrow {
        transition: transform .18s ease;
    }

    .menu-accordion.show > .menu-link .menu-arrow {
        transform: rotate(180deg);
    }

    /* 8) Submenu wrapper: soft background + border + rounded corners */
    .menu-sub {
        margin-top: .35rem;
        margin-bottom: .35rem;
        padding: .4rem .35rem .6rem;
        border: 1px solid color-mix(in srgb, var(--bs-border-color) 70%, transparent);
        background: color-mix(in srgb, var(--bs-app-bg-color) 92%, var(--bs-primary));
        border-radius: .75rem;
    }

    :root:not([data-bs-theme="dark"]) .menu-sub {
        /* Light-only styles */
        background: hsl(237 96% 100% / 1);
    }

    /* 9) Submenu links: slightly smaller + tighter indent */
    .menu-sub .menu-link {
        padding: .45rem .65rem;
        border-radius: .55rem;
        font-size: .95rem;
    }

    .menu-sub .menu-link:hover {
        background: color-mix(in srgb, var(--bs-card-bg) 90%, var(--bs-primary));
    }

    /* 10) Section label tone */
    .menu-section {
        color: var(--brand-muted) !important;
    }

    /* 11) Badges for counters (pending, alerts…) */
    .menu-badge.badge-pulse {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 .35rem;
        font-size: .75rem;
        font-weight: 700;
        border-radius: 999px;
        color: #fff;
        background: var(--brand-warning);
        box-shadow: 0 0 0 2px var(--bs-body-bg);
    }

    /* 12) Toggle button */
    .app-sidebar-toggle {
        background: var(--brand-sidebar-bg);
        color: var(--brand-sidebar-fg);
        border: 1px solid var(--brand-sidebar-fg) !important;
    }

    /* 13) Keep existing “state” helpers consistent with palette */
    .menu-state-bg .menu-item.hover:not(.here) > .menu-link:not(.disabled):not(.active):not(.here),
    .menu-state-bg .menu-item:not(.here) .menu-link:hover:not(.disabled):not(.active):not(.here),
    .menu-active-bg .menu-item .menu-link.active {
        background-color: var(--bs-body-bg);
    }

    [data-bs-theme="dark"] .menu-state-bg .menu-item.hover:not(.here) > .menu-link:not(.disabled):not(.active):not(.here),
    [data-bs-theme="dark"] .menu-state-bg .menu-item:not(.here) .menu-link:hover:not(.disabled):not(.active):not(.here),
    [data-bs-theme="dark"] .menu-active-bg .menu-item .menu-link.active {
        background: #334155;
        color: #fff;
    }

    /* 14) Dropdowns (align with your dark palette) */
    [data-bs-theme="dark"] .dropdown-menu {
        background: #1e293b;
        color: #f1f5f9;
        border: 1px solid #334155;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .4);
    }

    [data-bs-theme="dark"] .dropdown-menu .dropdown-item {
        color: #f1f5f9;
    }

    [data-bs-theme="dark"] .dropdown-menu .dropdown-item:hover,
    [data-bs-theme="dark"] .dropdown-menu .dropdown-item:focus {
        background: #334155;
        color: #fff;
    }

    [data-bs-theme="dark"] .dropdown-menu .dropdown-item.active {
        background: #3daeff;
        color: #0f172a;
    }

    /* 15) Minor type tweaks */
    .menu-link .badge {
        font-size: 12px !important;
    }

    .menu-sub-indention .menu-sub:not([data-popper-placement]) {
        margin-left: 2.2rem;
    }

    .menu-title-gray-700 .menu-item .menu-link {
        color: var(--brand-sidebar-fg);
    }

    .menu-title-gray-700 .menu-item .menu-link:hover {
        color: var(--brand-primary) !important;
    }

    /* 16) Optional: smooth press feedback */
    .app-sidebar .menu .menu-link:active {
        transform: translateY(1px);
    }

    /* 17) Keep icons aligned tight */
    .app-navbar .btn.btn-icon .bi {
        line-height: 1;
    }

    /* 18) Select2 dark dropdown alignment with theme */
    [data-bs-theme="dark"] .select2-container--bootstrap5 .select2-dropdown {
        background: #334155;
        border: 2px solid #475569;
    }

    [data-bs-theme="dark"] .select2-dropdown .select2-results__option.select2-results__option--highlighted {
        background: var(--bs-body-bg) !important;
    }
</style>
