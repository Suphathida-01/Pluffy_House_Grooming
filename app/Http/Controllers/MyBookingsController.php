<?php

namespace App\Http\Controllers;

class MyBookingsController extends Controller
{
    public function index()
    {
        $bookings = [
            [
                'id' => 'PH-20261018-042',
                'status' => 'upcoming',
                'status_label' => 'ยืนยันแล้ว (ตัวอย่าง)',
                'pet_name' => 'น้องพลัฟฟี่ (Pluffy)',
                'pet_type' => 'สุนัข',
                'pet_breed' => 'มอลทีส (Maltese)',
                'service_name' => 'อาบน้ำ + ตัดขน Full Course',
                'service_slug' => 'full-course',
                'date' => 'วันอาทิตย์ที่ 18 ตุลาคม 2569',
                'time' => '10:00 น.',
                'duration' => '2 ชม.',
                'price' => 700,
            ],
            [
                'id' => 'PH-20260927-015',
                'status' => 'completed',
                'status_label' => 'เสร็จสิ้น (ตัวอย่าง)',
                'pet_name' => 'น้องชาไทย (Chathai)',
                'pet_type' => 'แมว',
                'pet_breed' => 'เปอร์เซีย (Persian)',
                'service_name' => 'อาบน้ำสปาถนอมผิว',
                'service_slug' => 'spa-bath',
                'date' => 'วันอาทิตย์ที่ 27 กันยายน 2569',
                'time' => '13:00 น.',
                'duration' => '1 ชม.',
                'price' => 300,
            ],
        ];

        return view('bookings.index', compact('bookings'));
    }
}
