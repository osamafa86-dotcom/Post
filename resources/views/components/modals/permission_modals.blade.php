<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm permissions-manager">
                    <div class="card-header p-4">
                        <h3 class="card-title">{{__('messages.permissions.manage_permissions')}}</h3>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="{{$showEdit ? 'updatePermission' : 'createPermission'}}">
                            <div class="row mb-4">
                                <div class="form-group col-md-6 mb-3">
                                    <label for="title"
                                           class="form-label">{{__('messages.permissions.permission_title')}}</label>
                                    @can($showEdit ? 'permissions_edit' : 'permissions_create')
                                        <div class="d-flex align-items-start position-relative">
                                            <div class="flex-grow-1 me-3"> {{-- إضافة div عشان يحوي الـ input ورسالة الخطأ --}}
                                                <input wire:model.live="data.title" type="text"
                                                       class="form-control @error('title') is-invalid @enderror" id="title">
                                                @error('title')
                                                <div class="invalid-feedback d-block" style="font-size: 13px; position: absolute; bottom: -20px; left: 0;">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-primary">{{__('messages.save')}}</button>
                                        </div>

                                    @endcan
                                </div>

                            </div>

                            {{-- Tabs Navigation --}}
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6 custom-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-uppercase fw-bold fs-7 active"
                                       id="resources-tab-link" data-bs-toggle="tab"
                                       href="#resources-tab-pane" role="tab"
                                       aria-controls="resources-tab-pane" aria-selected="true">
                                        {{ __("messages.permission_categories.resources") }}
                                        <span
                                            class="badge ms-2">{{ count($permissions) }}</span> {{-- سيعرض العدد الكلي هنا مؤقتاً --}}
                                    </a>
                                </li>
{{--                                <li class="nav-item" role="presentation">--}}
{{--                                    <a class="nav-link text-uppercase fw-bold fs-7"--}}
{{--                                       id="pages-tab-link" data-bs-toggle="tab"--}}
{{--                                       href="#pages-tab-pane" role="tab"--}}
{{--                                       aria-controls="pages-tab-pane" aria-selected="false">--}}
{{--                                        {{ __("messages.permission_categories.pages") }}--}}
{{--                                        <span class="badge ms-2">0</span> --}}{{-- ثابت 0 لعدم وجود بيانات مصنفة بعد --}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                                <li class="nav-item" role="presentation">--}}
{{--                                    <a class="nav-link text-uppercase fw-bold fs-7"--}}
{{--                                       id="widgets-tab-link" data-bs-toggle="tab"--}}
{{--                                       href="#widgets-tab-pane" role="tab"--}}
{{--                                       aria-controls="widgets-tab-pane" aria-selected="false">--}}
{{--                                        {{ __("messages.permission_categories.widgets") }}--}}
{{--                                        <span class="badge ms-2">0</span> --}}{{-- ثابت 0 لعدم وجود بيانات مصنفة بعد --}}
{{--                                    </a>--}}
{{--                                </li>--}}
                                {{-- أضف المزيد من التبويبات هنا بنفس النمط --}}
                            </ul>

                            <div class="mb-4">
                                <div class="form-check form-check-solid form-check-dark mb-3">
                                    <input class="form-check-input" wire:model.live="all_permissions" type="checkbox"
                                           id="all_permissions_global">
                                    <label for="all_permissions_global"
                                           class="form-check-label fw-bold fs-5">{{__('messages.permissions.select_all_permissions')}}</label>
                                </div>
                            </div>

                            {{-- Tab Content --}}
                            <div class="tab-content" id="permissionsTabContent">
                                {{-- Resources Tab Pane: يحتوي على جميع الصلاحيات مؤقتاً --}}
                                <div class="tab-pane fade show active"
                                     id="resources-tab-pane" role="tabpanel"
                                     aria-labelledby="resources-tab-link">
                                    <div class="accordion accordion-icon-toggle" id="accordion-resources">
                                        @forelse($permissions as $modelName => $modelDisplayName)
                                            {{-- لا يوجد شرط if(in_array) هنا - يتم عرض كل شيء --}}
                                            <div class="accordion-item mb-3 shadow-sm rounded">
                                                <h2 class="accordion-header"
                                                    id="heading-resources-{{ Str::slug($modelName) }}">
                                                    <button class="accordion-button fs-5 fw-bold" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse-resources-{{ Str::slug($modelName) }}"
                                                            aria-expanded="true"
                                                            aria-controls="collapse-resources-{{ Str::slug($modelName) }}">
                                                        {{ $modelDisplayName }}
                                                        <span
                                                            class="text-muted ms-2 fw-normal fs-6">({{ $modelName }})</span>
                                                    </button>
                                                </h2>
                                                <div id="collapse-resources-{{ Str::slug($modelName) }}"
                                                     class="accordion-collapse show"
                                                     aria-labelledby="heading-resources-{{ Str::slug($modelName) }}"
                                                     data-bs-parent="#accordion-resources">
                                                    <div class="accordion-body">
                                                        <div class="permission-select-all-container">
                                                            <a href="#"
                                                               wire:click.prevent="toggleSelectAll('{{ $modelName }}')"
                                                               class="permission-select-all-link">
        <span class="permission-select-all-text">
            {{ $this->areAllPermissionsSelected($modelName) ? __('messages.permissions.deselect_all') : __('messages.permissions.select_all') }}
        </span>
                                                                <span class="permission-model-display-name">
          (  {{ $modelDisplayName }} )
        </span>
                                                            </a>
                                                        </div>
                                                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-4">
                                                            @foreach($permission_actions as $action)
                                                                @php
                                                                    $permissionId = Str::slug($modelName) . '-' . $action . '-resources';
                                                                    $canApplyAction = in_array(
                                                                        $action,
                                                                        in_array($modelName, array_keys($this->model_permissions))
                                                                            ? $model_permissions[$modelName]
                                                                            : $standers_actions
                                                                    );
                                                                    $colors = [
                                                                        'view' => 'primary',
                                                                        'view_any' => 'primary',
                                                                        'create' => 'success',
                                                                        'update' => 'warning',
                                                                        'replicate' => 'info',
                                                                        'reorder' => 'dark',
                                                                        'restore' => 'secondary',
                                                                        'restore_any' => 'secondary',
                                                                        'delete' => 'danger',
                                                                        'delete_any' => 'danger',
                                                                        'force_delete' => 'danger',
                                                                        'force_delete_any' => 'danger',
                                                                    ];
                                                                    $color = $colors[$action] ?? 'primary';
                                                                @endphp

                                                                @if($canApplyAction)
                                                                    <div class="col">
                                                                        <div class="form-check form-check-solid form-check-{{$color}} form-check-custom">
                                                                            <input class="form-check-input"
                                                                                   wire:model.live="data.{{$modelName}}.custom.{{$action}}"
                                                                                   type="checkbox"
                                                                                   id="{{$permissionId}}">
                                                                            <label class="form-check-label" for="{{$permissionId}}">
                                                                                {{ __('messages.'.$action) }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state-card">
                                                <img src="{{ asset('assets/dashboard/images/empty.svg') }}"
                                                     alt="{{ __('messages.no_links_icon_alt') }}"
                                                     class="empty-state-icon">
                                                <p class="empty-state-title">{{ __('messages.no_models_found') }}</p>
                                                <p class="empty-state-description">{{ __('messages.add_models_to_manage_permissions') }}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Pages Tab Pane: فارغ الآن --}}
                                <div class="tab-pane fade"
                                     id="pages-tab-pane" role="tabpanel"
                                     aria-labelledby="pages-tab-link">
                                    <div class="empty-state-card">
                                        <img src="{{ asset('assets/dashboard/images/empty.svg') }}"
                                             alt="{{ __('messages.no_links_icon_alt') }}" class="empty-state-icon">
                                        <p class="empty-state-title">{{ __('messages.permissions.no_models_in_category') }}</p>
                                        <p class="empty-state-description">{{ __('messages.permissions.check_another_tab_or_add_models') }}</p>
                                    </div>
                                </div>

                                {{-- Widgets Tab Pane: فارغ الآن --}}
                                <div class="tab-pane fade"
                                     id="widgets-tab-pane" role="tabpanel"
                                     aria-labelledby="widgets-tab-link">
                                    <div class="empty-state-card">
                                        <img src="{{ asset('assets/dashboard/images/empty.svg') }}"
                                             alt="{{ __('messages.no_links_icon_alt') }}" class="empty-state-icon">
                                        <p class="empty-state-title">{{ __('messages.permissions.no_models_in_category') }}</p>
                                        <p class="empty-state-description">{{ __('messages.permissions.check_another_tab_or_add_models') }}</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
