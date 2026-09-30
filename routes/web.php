<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::view('/', 'services.index')->name('home');

$serviceExamples = [
    'spa-bath' => [
        'service_name' => 'อาบน้ำสปาถนอมผิว',
        'service_price' => 400,
        'service_description' => 'อาบน้ำ เป่าขน ตัดเล็บ เช็ดหู พร้อมบำรุงเส้นขนและผิวหนังให้นุ่ม ชุ่มชื้น ไม่ระคายเคือง',
        'duration_label' => '1 ชม.',
        'badge' => 'POPULAR',
        'hero_image' => 'service-bath.jpg',
        'gallery_images' => ['service-spa.jpg', 'service-nail.jpg', 'course-1.jpg', 'service-bath.jpg'],
        'price_note' => 'ราคาอาจปรับตามขน้ำหนักของสัตว์เลี้ยง',
        'benefits' => [
            'อาบน้ำด้วยแชมพูสูตรอ่อนโยน เหมาะกับผิวสัตว์เลี้ยง',
            'เป่าขนให้แห้งและหวีจัดทรงอย่างเบามือ',
            'ตัดและตะไบเล็บให้เรียบร้อย',
            'เช็ดทำความสะอาดใบหูอย่างอ่อนโยน',
            'บำรุงผิวและเส้นขนให้นุ่ม ชุ่มชื้น',
        ],
    ],
    'full-course' => [
        'service_name' => 'อาบน้ำ + ตัดขน Full Course (หมาและแมว)',
        'service_price' => 700,
        'service_description' => 'ดีไซน์ความน่ารักให้น้องเต็มร้อยด้วยแพ็กเกจพรีเมียมที่รวบรวมทั้งการบำรุงดูแลสุขอนามัยแบบล้ำลึก และสไตล์แต่งขนสุดแสนเก๋ ช่างของเรารู้วิธีสื่อสารและสร้างความคุ้นเคยกับน้องๆ',
        'duration_label' => '2 ชม.',
        'badge' => 'FULL COURSE',
        'hero_image' => 'course-main.jpg',
        'gallery_images' => ['course-1.jpg', 'course-2.jpg', 'course-3.jpg', 'course-4.jpg'],
        'price_note' => 'ราคาอาจปรับตามขนาดและสภาพขนของสัตว์เลี้ยง',
        'benefits' => [
            'อาบน้ำและบำรุงเส้นขนอย่างครบขั้นตอน',
            'ตัดแต่งเส้นขนทั่วร่างกายโดยช่างผู้มีประสบการณ์',
            'ตัดเล็บและทำความสะอาดใบหู',
            'เป่าขนและตรวจความเรียบร้อยก่อนกลับบ้าน',
        ],
    ],
    'tick-prevention' => [
        'service_name' => 'กำจัดเห็บหมัด + ป้องกัน',
        'service_price' => 400,
        'service_description' => 'อาบน้ำด้วยผลิตภัณฑ์กำจัดเห็บหมัดที่เหมาะกับสัตว์เลี้ยง พร้อมดูแลและให้คำแนะนำการป้องกันหลังใช้บริการ',
        'duration_label' => '1 ชม.',
        'badge' => 'CARE & PROTECTION',
        'hero_image' => 'service-tick.jpg',
        'gallery_images' => ['service-bath.jpg', 'service-full-course.jpg', 'service-spa.jpg', 'service-tick.jpg'],
        'price_note' => 'โปรดแจ้งอายุ น้ำหนัก และประวัติแพ้ผลิตภัณฑ์ของน้องก่อนรับบริการ',
        'benefits' => [
            'ตรวจสภาพผิวและขนเบื้องต้นก่อนเริ่มบริการ',
            'อาบน้ำด้วยผลิตภัณฑ์สำหรับดูแลปัญหาเห็บหมัด',
            'ล้างและเป่าขนให้แห้งอย่างทั่วถึง',
            'ให้คำแนะนำการดูแลและป้องกันเห็บหมัดหลังใช้บริการ',
        ],
    ],
   
];

$weightPriceTiers = [
    ['label' => 'ไม่เกิน 5 กก.', 'extra' => 0],
    ['label' => 'มากกว่า 5–10 กก.', 'extra' => 100],
    ['label' => 'มากกว่า 10–20 กก.', 'extra' => 200],
    ['label' => 'มากกว่า 20–30 กก.', 'extra' => 300],
    ['label' => 'มากกว่า 30 กก.', 'extra' => 400],
];

$samplePets = [
    (object) [
        'id' => 1,
        'name' => 'น้องพลัฟฟี่ (Pluffy)',
        'type' => 'สุนัข',
        'breed' => 'มอลทีส (Maltese)',
        'age' => '2.5 ปี',
        'weight' => '3.2',
    ],
    (object) [
        'id' => 2,
        'name' => 'น้องชาไทย (Chathai)',
        'type' => 'แมว',
        'breed' => 'เปอร์เซีย (Persian)',
        'age' => '1 ปี',
        'weight' => '4.0',
    ],
];

$sampleTimeSlots = [
    ['value' => '09:00', 'available' => true],
    ['value' => '10:00', 'available' => true],
    ['value' => '13:00', 'available' => true],
    ['value' => '14:00', 'available' => false],
    ['value' => '16:00', 'available' => true],
    ['value' => '17:00', 'available' => false],
];

$thaiMonthNames = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

$calendarFor = function (Carbon $selectedDate): array {
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
};

Route::get('/services/{serviceSlug}', function ($serviceSlug) use ($serviceExamples, $weightPriceTiers) {
    if (!isset($serviceExamples[$serviceSlug])) {
        abort(404);
    }

    $example = $serviceExamples[$serviceSlug];
    $service = (object) $example;
    $durationLabel = $example['duration_label'];
    $priceTiers = array_map(function ($tier) use ($example) {
        return [
            'label' => $tier['label'],
            'price' => $example['service_price'] + $tier['extra'],
        ];
    }, $weightPriceTiers);

    return view('services.show', compact('service', 'durationLabel', 'serviceSlug', 'priceTiers'));
})->name('services.show');

Route::get('/booking/{serviceSlug}/date-time', function ($serviceSlug) use ($serviceExamples, $samplePets, $sampleTimeSlots, $calendarFor, $thaiMonthNames) {
    if (!isset($serviceExamples[$serviceSlug])) {
        abort(404);
    }

    $service = (object) $serviceExamples[$serviceSlug];
    $pets = $samplePets;
    $selectedDate = Carbon::now()->next(Carbon::SUNDAY)->addWeeks(2)->startOfDay();
    $calendarDates = $calendarFor($selectedDate);
    $calendarMonthLabel = $thaiMonthNames[$selectedDate->month - 1] . ' ' . ($selectedDate->year + 543);
    $selectedPetId = (int) request()->query('pet_id', $pets[0]->id);
    if (!in_array($selectedPetId, array_map(fn ($pet) => (int) $pet->id, $pets), true)) {
        $selectedPetId = $pets[0]->id;
    }
    $selectedStep = 2;
    $selectedTime = '10:00';

    return view('booking.create', compact('service', 'serviceSlug', 'pets', 'selectedPetId', 'selectedStep', 'selectedDate', 'selectedTime', 'calendarDates', 'sampleTimeSlots', 'calendarMonthLabel'));
})->name('booking.datetime');

Route::get('/booking/{serviceSlug}', function ($serviceSlug) use ($serviceExamples, $samplePets, $sampleTimeSlots, $calendarFor, $thaiMonthNames) {
    if (!isset($serviceExamples[$serviceSlug])) {
        abort(404);
    }

    $service = (object) $serviceExamples[$serviceSlug];
    $pets = $samplePets;
    $selectedDate = Carbon::now()->next(Carbon::SUNDAY)->addWeeks(2)->startOfDay();
    $calendarDates = $calendarFor($selectedDate);
    $calendarMonthLabel = $thaiMonthNames[$selectedDate->month - 1] . ' ' . ($selectedDate->year + 543);
    $selectedPetId = (int) request()->query('pet_id', $pets[0]->id);
    if (!in_array($selectedPetId, array_map(fn ($pet) => (int) $pet->id, $pets), true)) {
        $selectedPetId = $pets[0]->id;
    }
    $selectedStep = 1;
    $selectedTime = '';

    return view('booking.create', compact('service', 'serviceSlug', 'pets', 'selectedPetId', 'selectedStep', 'selectedDate', 'selectedTime', 'calendarDates', 'sampleTimeSlots', 'calendarMonthLabel'));
})->name('booking.create');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
