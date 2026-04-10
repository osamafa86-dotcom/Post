<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventRelation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();
        if ($events->isEmpty()) {
            return;
        }

        $categoryClass = 'App\\Models\\Category';
        $tagClass = 'App\\Models\\Tag';
        $participantClass = 'App\\Models\\Participant';

        $mainCategory = class_exists($categoryClass) ? $categoryClass::first() : null;
        $secondCategory = class_exists($categoryClass) ? $categoryClass::skip(1)->first() : null;
        $mainTag = class_exists($tagClass) ? $tagClass::first() : null;
        $secondTag = class_exists($tagClass) ? $tagClass::skip(1)->first() : null;
        $mainPresenter = class_exists($participantClass) ? $participantClass::first() : null;

        foreach ($events as $event) {
            if ($mainCategory) {
                EventRelation::firstOrCreate([
                    'event_id' => $event->id,
                    'relationable_type' => $categoryClass,
                    'relationable_id' => $mainCategory->id,
                    'relationable_is_main' => 1,
                ], [
                    'team_id' => null,
                ]);
            }

            if ($secondCategory) {
                EventRelation::firstOrCreate([
                    'event_id' => $event->id,
                    'relationable_type' => $categoryClass,
                    'relationable_id' => $secondCategory->id,
                    'relationable_is_main' => 0,
                ], [
                    'team_id' => null,
                ]);
            }

            if ($mainTag) {
                EventRelation::firstOrCreate([
                    'event_id' => $event->id,
                    'relationable_type' => $tagClass,
                    'relationable_id' => $mainTag->id,
                    'relationable_is_main' => 1,
                ], [
                    'team_id' => null,
                ]);
            }

            if ($secondTag) {
                EventRelation::firstOrCreate([
                    'event_id' => $event->id,
                    'relationable_type' => $tagClass,
                    'relationable_id' => $secondTag->id,
                    'relationable_is_main' => 0,
                ], [
                    'team_id' => null,
                ]);
            }

            if ($mainPresenter) {
                EventRelation::firstOrCreate([
                    'event_id' => $event->id,
                    'relationable_type' => $participantClass,
                    'relationable_id' => $mainPresenter->id,
                    'relationable_is_main' => 1,
                ], [
                    'team_id' => null,
                ]);
            }
        }
    }
}
