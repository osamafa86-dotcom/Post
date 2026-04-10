<?php

namespace Database\Seeders;

use App\Models\MaterialAlbum;
use App\Models\User;
use Database\Factories\PostFactory;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
//            FileLibrarySeeder::class,
//            CategorySeeder::class,
//            ParticipantSeeder::class,
//            TagSeeder::class,
//            TypeSeeder::class,
//            SpecialFileSeeder::class,
//            AdvertisementSeeder::class,
//            BreakingNewsSeeder::class,
//            AlertSeeder::class,
//            PostSeeder::class,
//            QuoteSeeder::class,
//            MaterialAlbumSeeder::class,
//            MaterialSeeder::class,
//            IconSeeder::class,
//            NavbarLinkSeeder::class,
//            PodcastAlbumSeeder::class,
//            VideoAlbumSeeder::class,
//            ContactUsSeeder::class,
//            PublishRoleSeeder::class,
//            LiveStreamSeeder::class,
//            EventSeeder::class,
//            EventDatesSeeder::class,
//            EventRelationSeeder::class,
//            PendingActionSeeder::class,
//            TrafficSourcesSeeder::class,
        ]);
    }
}
