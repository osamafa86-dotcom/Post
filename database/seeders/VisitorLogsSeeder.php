<?php

namespace Database\Seeders;

use App\Models\VisitorLog;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VisitorLogsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        VisitorLog::truncate();

        // Sample countries with visit counts
        $countries = [
            'US' => 556,  // United States
            'SA' => 342,  // Saudi Arabia
            'EG' => 289,  // Egypt
            'AE' => 234,  // UAE
            'GB' => 198,  // United Kingdom
            'DE' => 176,  // Germany
            'FR' => 154,  // France
            'IN' => 143,  // India
            'CA' => 128,  // Canada
            'AU' => 112,  // Australia
            'IT' => 98,   // Italy
            'ES' => 87,   // Spain
            'BR' => 76,   // Brazil
            'MX' => 65,   // Mexico
            'NL' => 54,   // Netherlands
            'TR' => 48,   // Turkey
            'SE' => 42,   // Sweden
            'PL' => 38,   // Poland
            'JP' => 35,   // Japan
            'KR' => 32,   // South Korea
            'AR' => 28,   // Argentina
            'CL' => 25,   // Chile
            'CO' => 22,   // Colombia
            'PE' => 19,   // Peru
            'ZA' => 17,   // South Africa
            'NG' => 15,   // Nigeria
            'KE' => 13,   // Kenya
            'MA' => 45,   // Morocco
            'DZ' => 38,   // Algeria
            'TN' => 32,   // Tunisia
            'JO' => 56,   // Jordan
            'LB' => 48,   // Lebanon
            'IQ' => 67,   // Iraq
            'KW' => 78,   // Kuwait
            'QA' => 89,   // Qatar
            'BH' => 54,   // Bahrain
            'OM' => 43,   // Oman
            'YE' => 21,   // Yemen
            'SY' => 18,   // Syria
            'PS' => 34,   // Palestine
        ];

        $devices = ['Windows', 'Mac OS', 'Linux', 'Android', 'iOS', 'Mobile'];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)',
            'Mozilla/5.0 (Linux; Android 11; SM-G991B)',
        ];

        foreach ($countries as $countryCode => $count) {
            // Distribute visits over the last 30 days
            for ($i = 0; $i < $count; $i++) {
                VisitorLog::create([
                    'ip' => $this->generateRandomIP(),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'device_type' => $devices[array_rand($devices)],
                    'country' => $countryCode,
                    'referrer' => rand(0, 1) ? 'https://google.com' : null,
                    'visited_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'created_at' => Carbon::now()->subDays(rand(0, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                ]);
            }
        }

        $this->command->info('✅ Created ' . array_sum($countries) . ' visitor logs across ' . count($countries) . ' countries');
    }

    private function generateRandomIP(): string
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255);
    }
}
