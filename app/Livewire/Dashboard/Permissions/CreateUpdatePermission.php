<?php

namespace App\Livewire\Dashboard\Permissions;

use App\Traits\PermissionsTrait;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateUpdatePermission extends Component
{
    use PermissionsTrait;
    public ?object $role;
    public bool $showEdit = false;
    public bool $all_permissions = false;

    public array $permissions = [];

    public array $permission_actions = ['show', 'create', 'edit', 'delete', 'publish','general_settings','extra_codes','custom_tags'];

    public array $standers_actions = ['show', 'create', 'edit', 'delete'];

    public array $model_permissions = [
        'posts' => ['show', 'create', 'edit', 'delete', 'publish'],
//        'podcast_tracks' => ['show', 'create', 'edit', 'delete', 'publish'],
//        'video_tracks' => ['show', 'create', 'edit', 'delete', 'publish'],
        'pending_actions' => ['show', 'edit', 'delete'],
        'sort_contents' => ['show', 'edit'],
        'alerts' => ['show', 'edit', 'delete'],
        'settings' => ['general_settings', 'extra_codes', 'custom_tags'],
        'news_letter_emails' => ['show', 'edit', 'delete'],
        'send_news' => ['show', 'edit', 'delete'],
        'contact_us' => ['show', 'edit', 'delete'],
        'import' => ['show'],
    ];

    public array $data = [];

    public function mount($role_id = null): void
    {
        $enabledPermissions = collect(config('features.permissions'))
            ->filter()
            ->keys();
        $name = Auth::user()?->default_dashboard_language == 'en' ? 'en_name' : 'name';
        $this->permissions = collect($this->listPermissions())
            ->only($enabledPermissions)
            ->mapWithKeys(fn ($permission, $key) => [$key => $permission[$name]])
            ->toArray();
        if ($role_id) {
            $this->showEdit = true;
            $this->role = Role::find($role_id);

            if ($this->role) {
                $this->edit($this->role);
            }

            $this->all_permissions = true;
            foreach ($this->permissions as $key => $list) {
                $permissions = in_array($key, array_keys($this->model_permissions))
                    ? $this->model_permissions[$key]
                    : $this->standers_actions;
                $prefixed_array = preg_filter('/^/', $key . '_', $permissions);
                $this_all = count(array_intersect($prefixed_array, $this->role->permissions->pluck('name')->toArray())) == count($prefixed_array);
                $this->all_permissions &= $this_all;

                $this->data[$key] = [
                    'all' => $this_all,
                    'custom' => [
                        'show' => in_array($key . '_show', $this->role->permissions->pluck('name')->toArray()),
                        'create' => in_array($key . '_create', $this->role->permissions->pluck('name')->toArray()),
                        'edit' => in_array($key . '_edit', $this->role->permissions->pluck('name')->toArray()),
                        'delete' => in_array($key . '_delete', $this->role->permissions->pluck('name')->toArray()),
                        'publish' => in_array($key . '_publish', $this->role->permissions->pluck('name')->toArray()),
                        'general_settings' => in_array($key . '_general_settings', $this->role->permissions->pluck('name')->toArray()),
                        'extra_codes' => in_array($key . '_extra_codes', $this->role->permissions->pluck('name')->toArray()),
                        'custom_tags' => in_array($key . '_custom_tags', $this->role->permissions->pluck('name')->toArray()),
                    ]
                ];
            }
        }
    }

    #[Layout('components.layouts.dashboard.app')]
    public function render(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.dashboard.permissions.create-update-permission');
    }

    public function updatedAllPermissions($value): void
    {
        foreach ($this->permissions as $key => $list) {
            $permissions = in_array($key, array_keys($this->model_permissions))
                ? $this->model_permissions[$key]
                : $this->standers_actions;
            $this->data[$key] = [
                'all' => $value,
                'custom' => array_fill_keys($permissions, $value)
            ];
        }
        $this->all_permissions = $value;
    }

    public function updatedData($value, $key): void
    {
        if ($key === 'title') {
            return;
        }
        gettype($value) == "array" && key($value) == "all" ? $final = $value['all'] : $final = $value;
        $checkAll = strpos($key, '.all');
        $checkCustom = strpos($key, '.custom');
        $checkNormal = false;
        if ($checkAll) {
            $check = str_replace('.all', '', $key);
        } elseif ($checkCustom) {
            $check = strstr($key, '.', true);
        } else {
            $check = $key;
            $checkNormal = true;
        }
        if (!$final) {
            if ($checkAll || $checkNormal) {
                $this->data[$check]['all'] = false;
                $this->data[$check]['custom'] = [
                    'show' => false,
                    'create' => false,
                    'edit' => false,
                    'delete' => false,
                    'publish' => false,
                    'general_settings' => false,
                    'extra_codes' => false,
                    'custom_tags' => false,
                ];
            }
            if ($checkCustom) {
                $this->data[$check]['all'] = false;
            }
            $this->all_permissions = false;

        } else {
            if ($checkAll || $checkNormal) {
                $this->data[$check]['all'] = true;
                $this->data[$check]['custom'] = [
                    'show' => true,
                    'create' => true,
                    'edit' => true,
                    'delete' => true,
                    'publish' => true,
                    'general_settings' => true,
                    'extra_codes' => true,
                    'custom_tags' => true,
                ];
            }
            if ($checkCustom) {
                if (count(array_filter($this->data[$check]['custom'])) === 5) {
                    $this->data[$check]['all'] = true;
                }
            }
            $this->all_permissions = count(array_filter($this->data, fn($v) => is_array($v) && isset($v['all']) && $v['all'])) === count($this->permissions);
        }
    }

    /**
     * @throws ValidationException
     */
    public function syncPermissions_(array $data): void
    {
        $uniqueRule = $this->showEdit && $this->role ? 'unique:roles,name,' . $this->role->id : 'unique:roles,name';

        $validatedData = Validator::make($data, [
            'title' => ['required', 'string', $uniqueRule],
        ])->validate();

        $role = $this->showEdit
            ? tap($this->role)->update(['name' => $validatedData['title']])
            : Role::create(['name' => $validatedData['title']]);

        $this->updateRolePermissions($role, $data);
    }

    private function updateRolePermissions(Role $role, array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value['custom'] as $action => $granted) {
                    if ($granted) {
                        $permissionName = "{$key}_{$action}";
                        $permission = Permission::firstOrCreate(['name' => $permissionName]);
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }
    }

    public function revokePermissions_(array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value['custom'] as $action => $granted) {
                    if (!$granted) {
                        $permission = Permission::where('name', "{$key}_{$action}")->first();
                        if ($permission) {
                            $this->role->revokePermissionTo($permission);
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws ValidationException
     */
    public function createPermission(): void
    {
        $this->syncPermissions_($this->data);
        $this->dispatch('permissionCreated');

    }

    public function edit($role): void
    {
        $permissions = $role->permissions;
        $setup = [];

        foreach ($permissions as $permission) {
            $baseName = preg_replace('/(_create|_edit|_show|_delete|_publish)$/', '', $permission->name);
            $setup[$baseName]['custom'][str_replace("{$baseName}_", '', $permission->name)] = true;
        }

        foreach ($setup as $name => &$value) {
            $value['all'] = count($value['custom']) === 5;
        }

        $this->data = ['title' => $role->name] + $setup;
        $this->all_permissions = count(array_filter($setup, fn($v) => $v['all'])) === count($this->permissions);

    }

    /**
     * @throws ValidationException
     */
    public function updatePermission(): void
    {
        $this->revokePermissions_($this->data);
        $this->syncPermissions_($this->data);
        $this->dispatch('permissionCreated');

    }

    // داخل الكلاس بتاع Livewire Component CreateUpdatePermission

    public function toggleSelectAll($modelName)
    {
        $allPermissionsSelected = $this->areAllPermissionsSelected($modelName);

        foreach ($this->permission_actions as $action) {
            $canApplyAction = in_array($action, array_key_exists($modelName, $this->model_permissions) ? $this->model_permissions[$modelName] : $this->standers_actions);
            if ($canApplyAction) {
                $this->data[$modelName]['custom'][$action] = !$allPermissionsSelected;
            }
        }
        // تأكد من تحديث الـ Livewire component بعد التغييرات
        $this->dispatch('permissions-updated'); // يمكنك استخدام هذا الـ dispatch إذا كنت تحتاج لتحديث الواجهة بناءً على التغيير
    }

    #[Computed]
    public function areAllPermissionsSelected($modelName)
    {
        foreach ($this->permission_actions as $action) {
            $canApplyAction = in_array($action, array_key_exists($modelName, $this->model_permissions) ? $this->model_permissions[$modelName] : $this->standers_actions);
            // تحقق مما إذا كانت الصلاحية موجودة في $this->data قبل محاولة الوصول إليها
            if ($canApplyAction && !($this->data[$modelName]['custom'][$action] ?? false)) {
                return false; // لو في أي صلاحية مش محددة، يبقى مش كلهم محددين
            }
        }
        return true; // كلهم محددين
    }
}
