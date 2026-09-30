<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ===== Guest routes (ยังไม่ login) =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// ===== Authenticated routes =====
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('home');
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        return 'Admin dashboard';
    })->name('admin.dashboard');
});

// ===== Service routes (จากหน้าเพื่อน) =====
Route::view('/service', 'services.index')->name('services.index');

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

    return view('services.show', compact('service', 'durationLabel', 'serviceSlug'));
})->name('services.show');

Route::get('/booking/{serviceSlug}', function ($serviceSlug) use ($serviceExamples) {
    if (!isset($serviceExamples[$serviceSlug])) {
        abort(404);
    }

    $service = (object) $serviceExamples[$serviceSlug];

    // ข้อมูลสัตว์เลี้ยงนี้เป็นตัวอย่างสำหรับจัดหน้าเท่านั้น ยังไม่ได้อ่านจากฐานข้อมูล
    $pets = [
        (object) [
            'id' => 1,
            'name' => 'น้องพลัฟฟี่ (Pluffy)',
            'type' => 'สุนัข',
            'breed' => 'มอลทีส (Maltese)',
            'age' => '2.5 ปี',
            'weight' => '3.2 กก.',
            'image' => 'images/pet-dog.jpg',
        ],
        (object) [
            'id' => 2,
            'name' => 'น้องชาไทย (Chathai)',
            'type' => 'แมว',
            'breed' => 'เปอร์เซีย (Persian)',
            'age' => '1 ปี',
            'weight' => '4.0 กก.',
            'image' => 'images/pet-cat.jpg',
        ],
    ];

    return view('booking.create', compact('service', 'serviceSlug', 'pets'));
})->name('booking.create');