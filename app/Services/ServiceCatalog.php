<?php

namespace App\Services;

class ServiceCatalog
{
    public static function all(): array
    {
        return [
            'spa-bath' => [
                'service_name' => 'อาบน้ำสปาถนอมผิว',
                'service_price' => 300,
                'size_prices' => ['S' => 300, 'M' => 400, 'L' => 500],
                'service_description' => 'อาบน้ำ เป่าขน ตัดเล็บ เช็ดหู พร้อมบำรุงเส้นขนและผิวหนังให้นุ่ม ชุ่มชื้น ไม่ระคายเคือง',
                'duration_label' => '1 ชม.',
                'badge' => 'POPULAR',
                'hero_image' => 'service-bath.jpg',
                'gallery_images' => ['service-spa.jpg', 'service-nail.jpg', 'course-1.jpg', 'service-bath.jpg'],
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
                'size_prices' => ['S' => 700, 'M' => 900, 'L' => 1100],
                'service_description' => 'ดีไซน์ความน่ารักให้น้องเต็มร้อยด้วยแพ็กเกจพรีเมียมที่รวบรวมทั้งการบำรุงดูแลสุขอนามัยแบบล้ำลึก และสไตล์แต่งขนสุดแสนเก๋ ช่างของเรารู้วิธีสื่อสารและสร้างความคุ้นเคยกับน้องๆ',
                'duration_label' => '2 ชม.',
                'badge' => 'FULL COURSE',
                'hero_image' => 'course-main.jpg',
                'gallery_images' => ['course-1.jpg', 'course-2.jpg', 'course-3.jpg', 'course-4.jpg'],
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
                'size_prices' => ['S' => 400, 'M' => 500, 'L' => 600],
                'service_description' => 'อาบน้ำด้วยผลิตภัณฑ์กำจัดเห็บหมัดที่เหมาะกับสัตว์เลี้ยง พร้อมดูแลและให้คำแนะนำการป้องกันหลังใช้บริการ',
                'duration_label' => '1 ชม.',
                'badge' => 'CARE & PROTECTION',
                'hero_image' => 'service-tick.jpg',
                'gallery_images' => ['service-bath.jpg', 'service-full-course.jpg', 'service-spa.jpg', 'service-tick.jpg'],
                'benefits' => [
                    'ตรวจสภาพผิวและขนเบื้องต้นก่อนเริ่มบริการ',
                    'อาบน้ำด้วยผลิตภัณฑ์สำหรับดูแลปัญหาเห็บหมัด',
                    'ล้างและเป่าขนให้แห้งอย่างทั่วถึง',
                    'ให้คำแนะนำการดูแลและป้องกันเห็บหมัดหลังใช้บริการ',
                ],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    public static function sizeLabels(): array
    {
        return [
            ['size' => 'S', 'weight' => 'ไม่เกิน 5 กก.'],
            ['size' => 'M', 'weight' => 'มากกว่า 5–15 กก.'],
            ['size' => 'L', 'weight' => 'มากกว่า 15 กก.'],
        ];
    }
}
