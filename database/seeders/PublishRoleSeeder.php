<?php

namespace Database\Seeders;

use App\Models\PublishRole;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PublishRoleSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualRoles();
        } else {
            $this->createSingleLanguageRoles();
        }
    }

    protected function createMultilingualRoles(): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $roles = $this->createRolesForLocale($locale);
            $this->createRoleHierarchy($roles, $locale);
        }

        $this->command->info('Multilingual publish role hierarchy created successfully.');
    }

    protected function createSingleLanguageRoles(): void
    {
        $locale = $this->getWebsiteLocale();
        $roles = $this->createRolesForLocale($locale);
        $this->createRoleHierarchy($roles, $locale);

        $this->command->info('Single language publish role hierarchy created successfully.');
    }

    protected function createRolesForLocale(string $locale): array
    {
        $roleNames = $this->getLocalizedRoleNames($locale);

        $roles = [
            'admin' => Role::firstOrCreate([
                'name' => $roleNames['admin'],
                'guard_name' => 'web'
            ]),
            'editor' => Role::firstOrCreate([
                'name' => $roleNames['editor'],
                'guard_name' => 'web'
            ]),
            'author' => Role::firstOrCreate([
                'name' => $roleNames['author'],
                'guard_name' => 'web'
            ]),
        ];


        return $roles;
    }

    protected function getLocalizedRoleNames(string $locale): array
    {
        if ($locale === 'ar') {
            return [
                'admin' => 'مسؤول',
                'editor' => 'محرر',
                'author' => 'كاتب',
                'reviewer' => 'مراجع',
                'publisher' => 'ناشر',
                'contributor' => 'مساهم'
            ];
        }

        return [
            'admin' => 'Administrator',
            'editor' => 'Editor',
            'author' => 'Author',
            'reviewer' => 'Reviewer',
            'publisher' => 'Publisher',
            'contributor' => 'Contributor'
        ];
    }

    protected function createRoleHierarchy(array $roles, string $locale): void
    {
        $pairs = [
            [$roles['author']->id, $roles['editor']->id],
            [$roles['editor']->id, $roles['admin']->id],
        ];

        foreach ($pairs as [$from, $to]) {
            PublishRole::firstOrCreate([
                'from_role_id' => $from,
                'to_role_id' => $to,
            ], [
                'team_id' => null,
            ]);
        }
    }
}
