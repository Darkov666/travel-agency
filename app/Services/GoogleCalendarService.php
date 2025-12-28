<?php

namespace App\Services;

use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;

class GoogleCalendarService
{
    public function getAvailableSlots($date, $duration = 60)
    {
        // For now, we'll just return some static slots as "available"
        // In a real scenario, we would query Google Calendar for busy times
        // and subtract them from working hours.

        // Simulated Available Hours: 9:00 to 18:00
        $startHour = 9;
        $endHour = 18;
        $slots = [];

        $current = Carbon::parse($date)->setHour($startHour)->setMinute(0)->setSecond(0);
        $endOfDay = Carbon::parse($date)->setHour($endHour)->setMinute(0)->setSecond(0);

        while ($current->copy()->addMinutes($duration)->lte($endOfDay)) {
            // Exclude lunch time maybe? (e.g., 14:00)
            if ($current->hour !== 14) {
                $slots[] = [
                    'time' => $current->format('H:i'),
                    'available' => true
                ];
            }
            $current->addMinutes($duration);
        }

        return $slots;
    }

    public function createEvent($title, $startDateTime, $endDateTime, $description = '')
    {
        // Simulate event creation
        $event = new \stdClass();
        $event->id = 'simulated_' . uniqid();
        $event->htmlLink = '#';

        // Only try real creation if configured (optional, but for now just simulate as requested)
        /*
        try {
            $realEvent = new Event;
            $realEvent->name = $title;
            $realEvent->startDateTime = Carbon::parse($startDateTime);
            $realEvent->endDateTime = Carbon::parse($endDateTime);
            $realEvent->description = $description;
            $realEvent->save();
            return $realEvent;
        } catch (\Exception $e) {
            // Fallback to simulation
        }
        */

        return $event;
    }
}
