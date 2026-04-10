@php use App\Enums\UserStatusEnum; @endphp
    <!--Create/Edit Modal -->
<div class="modal fade" id="userForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header {{$showEdit ? "bg-warning" : "bg-primary"}}">
                <h5 class="modal-title text-white"
                    id="staticBackdropLabel">{{$showEdit ? __("messages.users.edit_user") : __("messages.users.add_user")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="{{$showEdit ? 'updateUser' : 'createUser'}}">
                    <div class="row">
{{--                        <div class="form-group col-md-12 mb-3">--}}
{{--                            <label for="team_id">{{__('messages.teams.team')}}</label>--}}
{{--                            <select class="form-control @error('team_id') is-invalid @enderror"--}}
{{--                                    wire:model="state.team_id"--}}
{{--                                    id="team_id">--}}
{{--                                <option>{{__('messages.choose')}}</option>--}}
{{--                                @foreach($this->teams as $row)--}}
{{--                                    <option value="{{$row->id}}">{{$row->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                            @error('team_id')--}}
{{--                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}



                        <div class="form-group col-md-12 mb-3">
                            <label for="first_name">{{__('messages.users.first_name')}}</label>
                            <input wire:model="state.first_name" type="text"
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   id="first_name"
                                   placeholder="{{__('messages.users.first_name')}}">
                            @error('first_name')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="last_name">{{__('messages.users.last_name')}}</label>
                            <input wire:model="state.last_name" type="text"
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   id="last_name"
                                   placeholder="{{__('messages.users.last_name')}}">
                            @error('last_name')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="email">{{__('messages.users.email')}}</label>
                            <input wire:model="state.email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email">
                            @error('email')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="role_id">{{__('messages.users.status')}}</label>
                            <select class="form-control @error('role_id') is-invalid @enderror"
                                    wire:model="state.role_id"
                                    id="role_id">
                                <option>{{__('messages.choose')}}</option>
                                @foreach($this->roles as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
{{--                        <div class="form-group col-md-12 mb-3">--}}
{{--                            <label for="user_status">{{__('messages.users.manager')}}</label>--}}
{{--                            <select class="form-control @error('role_id') is-invalid @enderror"--}}
{{--                                    wire:model="state.manager_id"--}}
{{--                                    id="manager_id">--}}
{{--                                <option>{{__('messages.choose')}}</option>--}}
{{--                                @foreach($this->roles as $row)--}}
{{--                                    <option value="{{$row->id}}">{{$row->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                            @error('manager_id')--}}
{{--                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
                        <div class="form-group col-md-12 mb-3">
                            <label for="password">{{__('messages.users.password')}}</label>
                            <input wire:model="state.password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password">
                            @error('password')
                            <div class="invalid-feedback" style="font-size: 15px">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label for="password_confirmation">{{__('messages.users.password_confirmation')}}</label>
                            <input wire:model.live="state.password_confirmation" type="password"
                                   class="form-control"
                                   id="password_confirmation">
                        </div>
                    </div>
                    <div class="modal-footer p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                        <button type="submit" class="btn {{$showEdit ? "btn-warning" : "btn-primary"}}">{{__('messages.confirm')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--Delete Modal -->
<div class="modal fade" id="userDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="staticBackdropLabel">{{__('messages.users.delete_user')}}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{__('messages.users.confirm_delete_user')}}</h4>
                <h6>{{__('messages.users.delete_user_message')}}: {{$Users_->full_name ?? __('messages.not_available')}}</h6>
                <div class="modal-footer p-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancel')}}</button>
                    <button wire:click="userDeleteConfirm" class="btn btn-danger">{{__('messages.confirm')}}</button>
                </div>
            </div>

        </div>
    </div>
</div>
