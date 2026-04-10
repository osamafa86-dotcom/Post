<?php

namespace Database\Factories;

use App\Enums\TagTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_name' => fake()->word(),
            'tag_type' => TagTypeEnum::cases()[array_rand(TagTypeEnum::cases())]->value,
        ];
    }
}
