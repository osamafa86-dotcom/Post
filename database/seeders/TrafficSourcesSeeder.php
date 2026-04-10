<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisitorLog;
use Carbon\Carbon;

class TrafficSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample referrer sources
        $referrers = [
            'https://www.facebook.com/sharer/sharer.php?u=example',
            'https://www.facebook.com/pages/example',
            'https://twitter.com/user/status/123456789',
            'https://t.co/abc123',
            'https://www.google.com/search?q=news',
            'https://www.google.com/search?q=latest+updates',
            'https://www.instagram.com/p/abc123/',
            'https://www.linkedin.com/feed/',
            'https://www.youtube.com/watch?v=abc123',
            'https://www.reddit.com/r/news/',
            'https://wa.me/?text=check+this+out',
            'https://t.me/channel/post',
            'https://www.bing.com/search?q=news',
            'https://www.tiktok.com/@user/video/123',
            null, // Direct traffic
            null, // Direct traffic
            null, // Direct traffic
        ];

        $devices = ['Windows', 'Mac OS', 'Android', 'iOS', 'Linux'];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
        ];

        echo "Generating sample traffic data...\n";

        // Generate traffic for the last 30 days
        for ($day = 0; $day < 30; $day++) {
            $date = Carbon::now()->subDays($day);
            
            // Generate 20-50 visits per day
            $visitsPerDay = rand(20, 50);
            
            for ($i = 0; $i < $visitsPerDay; $i++) {
                $randomHour = rand(0, 23);
                $randomMinute = rand(0, 59);
                
                VisitorLog::create([
                    'ip' => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'device_type' => $devices[array_rand($devices)],
                    'referrer' => $referrers[array_rand($referrers)],
                    'visited_at' => $date->copy()->setTime($randomHour, $randomMinute),
                ]);
            }
            
            echo "Day " . ($day + 1) . " completed with {$visitsPerDay} visits\n";
        }

        echo "\nSample traffic data generated successfully!\n";
        echo "Total records: " . VisitorLog::count() . "\n";
    }
}
