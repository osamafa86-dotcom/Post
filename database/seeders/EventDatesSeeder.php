<?php

namespace Database\Seeders;

use App\Enums\DateTypeEnum;
use App\Enums\DayEnum;
use App\Models\Event;
use App\Models\EventDates;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EventDatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timeEvents = Event::where('date_type', DateTypeEnum::TIME->value)->get();

        foreach ($timeEvents as $event) {
            $days = collect(DayEnum::cases())->random(4);

            foreach ($days as $dayEnum) {
                $startHour = rand(8, 16);
                $duration = rand(1, 3);
                $start = sprintf('%02d:00:00', $startHour);
                $end = sprintf('%02d:00:00', $startHour + $duration);

                EventDates::firstOrCreate([
                    'event_id' => $event->id,
                    'day' => $dayEnum->value,
                    'start_time' => $start,
                    'end_time' => $end,
                ], [
                    'team_id' => null,
                ]);
            }
        }

        $dateTimeEvents = Event::where('date_type', DateTypeEnum::DATE_TIME->value)->get();

        foreach ($dateTimeEvents as $event) {
            $start = Carbon::now()->addDays(rand(3, 14))->startOfHour();
            $end = (clone $start)->addHours(rand(2, 6));

            EventDates::firstOrCreate([
                'event_id' => $event->id,
                'start_date_time' => $start,
                'end_date_time' => $end,
            ], [
                'team_id' => null,
            ]);
        }
    }
}
