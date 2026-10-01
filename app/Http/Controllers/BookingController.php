<?php

namespace App\Http\Controllers;

use App\Services\ServiceCatalog;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    private array $samplePets = [
        [
            'id' => 1,
            'name' => 'น้องพลัฟฟี่ (Pluffy)',
            'type' => 'สุนัข',
            'breed' => 'มอลทีส (Maltese)',
            'age' => '2.5 ปี',
            'weight' => '3.2',
        ],
        [
            'id' => 2,
            'name' => 'น้องชาไทย (Chathai)',
            'type' => 'แมว',
            'breed' => 'เปอร์เซีย (Persian)',
            'age' => '1 ปี',
            'weight' => '4.0',
        ],
    ];

    private array $sampleTimeSlots = [
        ['value' => '09:00', 'available' => true],
        ['value' => '10:00', 'available' => true],
        ['value' => '13:00', 'available' => true],
        ['value' => '14:00', 'available' => false],
        ['value' => '16:00', 'available' => true],
        ['value' => '17:00', 'available' => false],
    ];

    private array $thaiMonthNames = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
    ];

    public function create(string $serviceSlug)
    {
        return $this->showBookingPage($serviceSlug, 1, '');
    }

    public function dateTime(string $serviceSlug)
    {
        return $this->showBookingPage($serviceSlug, 2, '10:00');
    }

    private function showBookingPage(string $serviceSlug, int $selectedStep, string $selectedTime)
    {
        $serviceData = ServiceCatalog::find($serviceSlug);

        if ($serviceData === null) {
            abort(404);
        }

        $service = (object) $serviceData;
        $pets = array_map(fn ($pet) => (object) $pet, $this->samplePets);
        $selectedDate = Carbon::now()->next(Carbon::SUNDAY)->addWeeks(2)->startOfDay();
        $calendarDates = $this->calendarFor($selectedDate);
        $calendarMonthLabel = $this->thaiMonthNames[$selectedDate->month - 1] . ' ' . ($selectedDate->year + 543);
        $selectedPetId = (int) request()->query('pet_id', $pets[0]->id);

        if (!in_array($selectedPetId, array_map(fn ($pet) => (int) $pet->id, $pets), true)) {
            $selectedPetId = $pets[0]->id;
        }

        $sampleTimeSlots = $this->sampleTimeSlots;

        return view('booking.create', compact(
            'service',
            'serviceSlug',
            'pets',
            'selectedPetId',
            'selectedStep',
            'selectedDate',
            'selectedTime',
            'calendarDates',
            'sampleTimeSlots',
            'calendarMonthLabel'
        ));
    }

    private function calendarFor(Carbon $selectedDate): array
    {
        $firstDate = $selectedDate->copy()->startOfMonth();
        $calendarStart = $firstDate->copy()->startOfWeek(Carbon::SUNDAY);
        $cellCount = (int) ceil(($firstDate->dayOfWeek + $firstDate->daysInMonth) / 7) * 7;
        $calendarDates = [];
        $today = Carbon::today();

        for ($index = 0; $index < $cellCount; $index++) {
            $date = $calendarStart->copy()->addDays($index);
            $calendarDates[] = (object) [
                'value' => $date->toDateString(),
                'day' => $date->day,
                'outside_month' => $date->month !== $selectedDate->month,
                'past' => $date->lt($today),
                'selected' => $date->isSameDay($selectedDate),
                'today' => $date->isSameDay($today),
            ];
        }

        return $calendarDates;
    }
}
