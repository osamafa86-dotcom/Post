<?php

namespace Database\Seeders;

use App\Models\VideoTrack;
use App\Models\VideoAlbum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء عدد معين من VideoAlbum أولًا
        $albums = VideoAlbum::factory(10)->create();

        // ثم إنشاء VideoTracks وربطها مع VideoAlbums
        VideoTrack::factory(100)->make()->each(function ($videoTrack) use ($albums) {
            $videoTrack->video_album_id = $albums->random()->id;
            $videoTrack->save();
        });
    }
}
