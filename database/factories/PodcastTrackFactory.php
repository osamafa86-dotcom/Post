<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PodcastTrack;
use App\Models\PodcastAlbum;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PodcastTrack>
 */
class PodcastTrackFactory extends Factory
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
            'image' => $this->faker->imageUrl,
            'description' => $this->faker->paragraph,
            'url' => $this->faker->url,
            'podcast_album_id' => PodcastAlbum::factory(),
        ];
    }
}
