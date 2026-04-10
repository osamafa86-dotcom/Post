<?php

namespace Database\Factories;

use App\Enums\CategoryTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_title' => fake()->name(),
            'category_description' => fake()->text(),
            'category_type' =>CategoryTypeEnum::cases()[array_rand(CategoryTypeEnum::cases())]->value,
        ];
    }
}
