<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VideoAlbum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PodcastAlbum>
 */
class PodcastAlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'small_image' => $this->faker->imageUrl,
            'background_image' => $this->faker->imageUrl,
            'description' => $this->faker->paragraph,
        ];
    }
}
