<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Participant;
use App\Models\SpecialFile;
use App\Models\Tag;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostRelation>
 */
class PostRelationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => rand(1, 20),
            'relationable_id' => rand(1, 20),
            'relationable_type' => fake()->randomElement([Participant::class, Category::class,Type::class,Tag::class, SpecialFile::class]),
            'relationable_is_main' => fake()->boolean(),
        ];
    }
}
