<?php

namespace Database\Seeders;

use App\Models\PostRelation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PostRelation::factory()->count(50)->create();

    }
}
