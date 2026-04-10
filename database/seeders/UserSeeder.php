<?php

namespace Database\Seeders;

use App\Enums\UserStatusEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//         User::factory(10)->create();

        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Asem',
                'last_name' => 'Abbas',
                'user_status' => \App\Enums\UserStatusEnum::ADMIN->value,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'remember_token' => Str::random(10),
                'profile_photo_path' => null,
                'current_team_id' => null,
            ]
        );

        $role = Role::firstOrCreate([
            'name' => 'مسؤول',
            'guard_name' => 'web',
        ]);

        $permissions = [
            'permissions_show',
            'permissions_create',
            'permissions_edit',
            'permissions_delete',
            'posts_show',
            'posts_create',
            'posts_edit',
            'posts_delete'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach ($permissions as $permission_) {
            $permission = Permission::where('name', $permission_)->first();
            $role->givePermissionTo($permission);
        }

        if ($user) {
            $user->assignRole($role);
        }
    }
}
