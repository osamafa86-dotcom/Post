<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'site_name' => null,
            'light_site_name' => null,
            'site_logo' => null,
//            'footer_logo' => null,
//            'main_news_background' => null,
            'footer_description' => null,
//            'site_description' => null,
            'copyright_text' => null,
            'site_email' => null,
            'address' => null,
            'phone' => null,
            'tags' => null,
            'open_graph_image' => null,
            'open_graph_description' => null,
            'open_graph_local' => null,
            'open_graph_author' => null,
            'open_graph_twitter_creator' => null,
            'open_graph_twitter_site' => null,
            'header_code' => null,
            'footer_code' => null,
            'water_mark_image' => null,
            'water_mark_place' => null,
//            'favicon' => null,
            'website_active' => true,
        ]);
    }
}
