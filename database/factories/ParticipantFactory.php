<?php

namespace Database\Factories;

use App\Enums\ParticipantTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'image' => null,
            'work' => fake()->word(),
            'description' => fake()->text(),
            'type' => ParticipantTypeEnum::available()[array_rand(ParticipantTypeEnum::available())]->value,
            'team_id' => null,
        ];
    }
}
