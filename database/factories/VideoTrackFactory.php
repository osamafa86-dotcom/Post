<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VideoTrack;
use App\Models\VideoAlbum;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VideoTrack>
 */
class VideoTrackFactory extends Factory
{

    protected $model = VideoTrack::class;

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
            'video_album_id' => VideoAlbum::factory(),
        ];
    }
}
