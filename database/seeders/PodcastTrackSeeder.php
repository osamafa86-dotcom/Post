<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PodcastTrack;
use App\Models\PodcastAlbum;

class PodcastTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $albums = PodcastAlbum::factory(10)->create();

        PodcastTrack::factory(100)->make()->each(function ($podcastTrack) use ($albums) {
            $podcastTrack->podcast_album_id = $albums->random()->id;
            $podcastTrack->save();
        });
    }
}
