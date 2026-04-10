<?php

namespace Database\Factories;

use App\Enums\ParticipantTypeEnum;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quote_from' => $this->faker->company,
            'quote_text' => $this->faker->text(200),
            'author_id' => Participant::where('type',ParticipantTypeEnum::AUTHORS->value)->get()->random()?->id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
