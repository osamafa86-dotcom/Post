<?php

namespace Database\Seeders;

use App\Models\Icon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $icons = [
            'fa-solid fa-house',
            'fa-solid fa-newspaper',
            'fa-solid fa-podcast',
            'fa-solid fa-video',
            'fa-solid fa-tags',
            'fa-solid fa-link',
        ];

        foreach ($icons as $iconPath) {
            Icon::firstOrCreate([
                'icon_path' => $iconPath,
            ]);
        }
    }
}
