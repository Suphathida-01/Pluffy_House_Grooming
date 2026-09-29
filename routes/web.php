<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'services.index')->name('home');

$serviceExamples = [
    'spa-bath' => [
        'service_name' => 'อาบน้ำสปาถนอมผิว',
        'service_price' => 300,
        'service_description' => 'อาบน้ำ เป่าขน ตัดเล็บ เช็ดหู พร้อมบำรุงเส้นขนและผิวหนังให้นุ่ม ชุ่มชื้น ไม่ระคายเคือง',
        'duration_label' => '1 ชม.',
    ],
    'standard-trim' => [
        'service_name' => 'ตัดขนสุนัข/แมวมาตรฐาน',
        'service_price' => 500,
        'service_description' => 'ตัดแต่งทรงขนและปรับเส้นขนตามความต้องการ ด้วยมาตรฐานมืออาชีพและประสบการณ์ดูแลสัตว์เลี้ยง',
        'duration_label' => '1 ชม.',
    ],
    'full-course' => [
        'service_name' => 'อาบน้ำ + ตัดขน Full Course (หมาและแมว)',
        'service_price' => 700,
        'service_description' => 'ดีไซน์ความน่ารักให้น้องเต็มร้อยด้วยแพ็กเกจพรีเมียมที่รวบรวมทั้งการบำรุงดูแลสุขอนามัยแบบล้ำลึก และสไตล์แต่งขนสุดแสนเก๋ ช่างของเรารู้วิธีสื่อสารและสร้างความคุ้นเคยกับน้องๆ',
        'duration_label' => '2 ชม.',
    ],
];

Route::get('/services/{serviceSlug}', function ($serviceSlug) use ($serviceExamples) {
    if (!isset($serviceExamples[$serviceSlug])) {
        abort(404);
    }

    $example = $serviceExamples[$serviceSlug];
    $service = (object) $example;
    $durationLabel = $example['duration_label'];

    return view('services.show', compact('service', 'durationLabel'));
})->name('services.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
