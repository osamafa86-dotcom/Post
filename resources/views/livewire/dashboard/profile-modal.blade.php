<div>
    <div class="modal fade" id="profileForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"
                        id="staticBackdropLabel">{{ __('messages.profile.profile') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
{{--test--}}
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            <!-- Avatar Upload -->
                            <div class="form-group text-center mb-4">
                                <label for="avatar" class="cursor-pointer">
                                    <div class="position-relative d-inline-block">
                                        @if($avatar)
                                            <img class="img-fluid rounded-circle" src="{{ $avatar }}"
                                                 width="120" height="120" style="object-fit: cover;">
                                        @elseif($user_avatar)
                                            <img class="img-fluid rounded-circle" src="{{ $user_avatar }}"
                                                 width="120" height="120" style="object-fit: cover;">
                                        @else
                                            <img class="img-fluid rounded-circle" src="{{ asset('assets/dashboard/images/user.webp') }}"
                                                 width="120" height="120" style="object-fit: cover;">
                                        @endif

                                        <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2">
                                            <i class="bi bi-camera"></i>
                                        </div>

                                        @if($avatar)
                                            <button type="button" class="btn-close position-absolute top-0 start-0 bg-white rounded-circle p-1"
                                                    wire:click.prevent="clearColumn"
                                                    style="border: 1px solid #ccc;"></button>
                                        @endif
                                    </div>
                                </label>
                                <input id="avatar" type="file" wire:model="avatar" hidden accept="image/*">
                                @error('avatar')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Form Fields -->
                            <div class="form-group col-12 mb-3">
                                <label for="first_name" class="form-label">{{ __('messages.profile.first_name') }}</label>
                                <input wire:model="state.first_name" type="text"
                                       class="form-control @error('state.first_name') is-invalid @enderror"
                                       id="first_name">
                                @error('state.first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-12 mb-3">
                                <label for="last_name" class="form-label">{{ __('messages.profile.last_name') }}</label>
                                <input wire:model="state.last_name" type="text"
                                       class="form-control @error('state.last_name') is-invalid @enderror"
                                       id="last_name">
                                @error('state.last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-12 mb-3">
                                <label for="email" class="form-label">{{ __('messages.profile.email') }}</label>
                                <input wire:model="state.email" type="email"
                                       class="form-control @error('state.email') is-invalid @enderror"
                                       id="email">
                                @error('state.email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password Change -->
                            <div class="col-12 mb-3">
                                <a class="btn btn-outline-primary btn-sm" wire:click="changePassword">
                                    @if($is_change_password)
                                        {{ __('messages.profile.cancel_password_change') }}
                                    @else
                                        {{ __('messages.profile.password_title') }}
                                    @endif
                                </a>
                            </div>

                            @if($is_change_password)
                                <div class="form-group col-12 mb-3">
                                    <label for="password" class="form-label">{{ __('messages.profile.password') }}</label>
                                    <input wire:model="state.password" type="password"
                                           class="form-control @error('state.password') is-invalid @enderror"
                                           id="password">
                                    @error('state.password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-12 mb-3">
                                    <label for="confirm_password" class="form-label">{{ __('messages.profile.confirm_password') }}</label>
                                    <input wire:model="state.confirm_password" type="password"
                                           class="form-control @error('state.confirm_password') is-invalid @enderror"
                                           id="confirm_password">
                                    @error('state.confirm_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <!-- Messages -->
                        @if (session()->has('message'))
                            <div class="alert alert-success mt-3">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger mt-3">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Footer -->
                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('messages.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('messages.confirm') }}</span>
                                <span wire:loading>{{ __('messages.saving') }}...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
