<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminSchedule;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedServices();

        DB::transaction(function () {
            $timestamp = now();
            $staffIds = [];

            $staff = [
                ['name' => 'ช่างแพร', 'email' => 'prae.demo@pluffyhouse.test', 'phone' => '0810000001'],
                ['name' => 'ช่างเอ็ม', 'email' => 'em.demo@pluffyhouse.test', 'phone' => '0810000002'],
                ['name' => 'ช่างบอย', 'email' => 'boy.demo@pluffyhouse.test', 'phone' => '0810000003'],
            ];

            foreach ($staff as $person) {
                $userId = $this->findOrCreateUser(
                    $person['name'],
                    $person['email'],
                    $person['phone'],
                    'admin',
                    $timestamp,
                );
                $admin = Admin::firstOrCreate(['user_id' => $userId]);
                $staffIds[] = $admin->admin_id;
            }

            $customerIds = [];
            $customers = [
                ['name' => 'คุณสมหญิง ใจดี', 'email' => 'somying.demo@pluffyhouse.test', 'phone' => '0820000001'],
                ['name' => 'คุณวิชัย รักหมา', 'email' => 'wichai.demo@pluffyhouse.test', 'phone' => '0820000002'],
            ];

            foreach ($customers as $person) {
                $userId = $this->findOrCreateUser(
                    $person['name'],
                    $person['email'],
                    $person['phone'],
                    'customer',
                    $timestamp,
                );
                $customerId = DB::table('customers')->where('user_id', $userId)->value('customer_id');

                if (! $customerId) {
                    $customerId = DB::table('customers')->insertGetId([
                        'user_id' => $userId,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ], 'customer_id');
                }

                $customerIds[] = (int) $customerId;
            }

            $dogTypeId = $this->findOrCreatePetType('สุนัข', $timestamp);
            $catTypeId = $this->findOrCreatePetType('แมว', $timestamp);
            $smallBreedId = $this->findOrCreateBreed('ชิห์สุ', $dogTypeId, $timestamp);
            $largeBreedId = $this->findOrCreateBreed('โกลเด้น รีทรีฟเวอร์', $dogTypeId, $timestamp);
            $catBreedId = $this->findOrCreateBreed('เปอร์เซีย', $catTypeId, $timestamp);

            $petSamples = [
                ['name' => 'มะลิ', 'customer_id' => $customerIds[0], 'breed_id' => $smallBreedId, 'gender' => 'female', 'weight' => 5.5],
                ['name' => 'ทอง', 'customer_id' => $customerIds[1], 'breed_id' => $largeBreedId, 'gender' => 'male', 'weight' => 28],
                ['name' => 'ขนุน', 'customer_id' => $customerIds[0], 'breed_id' => $catBreedId, 'gender' => 'male', 'weight' => 4.2],
            ];
            $pets = [];

            foreach ($petSamples as $pet) {
                $pets[] = $this->findOrCreatePet(
                    $pet['name'],
                    $pet['customer_id'],
                    $pet['breed_id'],
                    $pet['gender'],
                    $pet['weight'],
                    $timestamp,
                );
            }

            $demoDate = now()->toDateString();
            $weekStart = now()->startOfWeek();

            for ($day = 0; $day < 7; $day++) {
                $workDate = $weekStart->copy()->addDays($day)->toDateString();

                foreach ($staffIds as $index => $staffId) {
                    AdminSchedule::firstOrCreate(
                        ['admin_id' => $staffId, 'work_date' => $workDate],
                        [
                            'start_time' => '09:00',
                            'end_time' => '17:00',
                            'status' => $index === 2 && $workDate === $demoDate ? 'unavailable' : 'available',
                        ],
                    );
                }
            }

            $services = Service::where('service_status', 'active')->orderBy('service_id')->get();

            if ($services->isEmpty()) {
                return;
            }

            $bookingSamples = [
                [
                    'day_offset' => 0, 'staff_index' => 0, 'pet_index' => 0, 'service_index' => 0,
                    'start' => '09:00', 'end' => '10:30', 'booking_status' => 'completed',
                    'payment_status' => 'completed', 'payment_method' => 'qrCode',
                ],
                [
                    'day_offset' => 1, 'staff_index' => 1, 'pet_index' => 1, 'service_index' => 1,
                    'start' => '11:00', 'end' => '12:00', 'booking_status' => 'confirmed',
                    'payment_status' => 'pending', 'payment_method' => 'creditCard',
                ],
                [
                    'day_offset' => 2, 'staff_index' => 0, 'pet_index' => 2, 'service_index' => 2,
                    'start' => '13:00', 'end' => '14:00', 'booking_status' => 'pending',
                    'payment_status' => 'failed', 'payment_method' => 'qrCode',
                ],
                [
                    'day_offset' => 3, 'staff_index' => 1, 'pet_index' => 0, 'service_index' => 0,
                    'start' => '15:00', 'end' => '16:30', 'booking_status' => 'completed',
                    'payment_status' => 'completed', 'payment_method' => 'creditCard',
                ],
            ];

            foreach ($bookingSamples as $sample) {
                $bookingDate = $sample['day_offset'] === 0
                    ? $demoDate
                    : $weekStart->copy()->addDays($sample['day_offset'])->toDateString();
                $adminId = $staffIds[$sample['staff_index']];
                $pet = $pets[$sample['pet_index']];
                $service = $services[$sample['service_index'] % $services->count()];

                $booking = Booking::firstOrCreate(
                    [
                        'admin_id' => $adminId,
                        'booking_date' => $bookingDate,
                        'booking_start_time' => $sample['start'],
                    ],
                    [
                        'booking_end_time' => $sample['end'],
                        'booking_total_price' => $service->priceForWeight((float) $pet['weight']),
                        'booking_status' => $sample['booking_status'],
                        'customer_id' => $pet['customer_id'],
                        'service_id' => $service->service_id,
                        'pet_id' => $pet['id'],
                    ],
                );

                $paymentExists = DB::table('payments')->where('booking_id', $booking->booking_id)->exists();
                if (! $paymentExists) {
                    DB::table('payments')->insert([
                        'payment_date' => $bookingDate . ' ' . $sample['start'] . ':00',
                        'payment_amount' => $booking->booking_total_price,
                        'payment_method' => $sample['payment_method'],
                        'transaction_ref' => 'DEMO-' . str_pad((string) $booking->booking_id, 5, '0', STR_PAD_LEFT),
                        'payment_status' => $sample['payment_status'],
                        'booking_id' => $booking->booking_id,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);
                }
            }
        });
    }

    private function seedServices(): void
    {
        $services = [
            [
                'service_name' => 'อาบน้ำและเป่าขน',
                'service_description' => 'อาบน้ำ เป่าขน แปรงขน ตัดเล็บ และทำความสะอาดหู',
                'service_duration_minutes' => 45,
                'service_price' => 250,
                'price_small' => 250,
                'price_medium' => 350,
                'price_large' => 450,
                'service_status' => 'active',
            ],
            [
                'service_name' => 'ตัดแต่งขนสไตล์ญี่ปุ่น',
                'service_description' => 'ตัดแต่งทรงขน พร้อมอาบน้ำและเป่าขน',
                'service_duration_minutes' => 90,
                'service_price' => 550,
                'price_small' => 550,
                'price_medium' => 700,
                'price_large' => 850,
                'service_status' => 'active',
            ],
            [
                'service_name' => 'สปาน้ำแร่บำรุงผิว',
                'service_description' => 'แช่น้ำแร่และบำรุงผิวกับเส้นขน',
                'service_duration_minutes' => 60,
                'service_price' => 400,
                'price_small' => 400,
                'price_medium' => 450,
                'price_large' => 550,
                'service_status' => 'active',
            ],
            [
                'service_name' => 'กำจัดเห็บหมัด',
                'service_description' => 'อาบน้ำด้วยผลิตภัณฑ์กำจัดเห็บหมัดและตรวจผิวหนังเบื้องต้น',
                'service_duration_minutes' => 50,
                'service_price' => 300,
                'price_small' => 300,
                'price_medium' => 400,
                'price_large' => 500,
                'service_status' => 'inactive',
            ],
            [
                'service_name' => 'ตัดเล็บและเช็ดหู',
                'service_description' => 'ดูแลเล็บและทำความสะอาดหูอย่างเบามือ',
                'service_duration_minutes' => 30,
                'service_price' => 150,
                'price_small' => 150,
                'price_medium' => 150,
                'price_large' => 180,
                'service_status' => 'active',
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['service_name' => $service['service_name']],
                $service,
            );
        }
    }

    private function findOrCreateUser(string $name, string $email, string $phone, string $role, $timestamp): int
    {
        $userId = DB::table('users')->where('user_email', $email)->value('user_id');
        if ($userId) {
            return (int) $userId;
        }

        return DB::table('users')->insertGetId([
            'user_name' => $name,
            'user_email' => $email,
            'user_phone' => $phone,
            'user_password' => Hash::make('password123'),
            'user_role' => $role,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ], 'user_id');
    }

    private function findOrCreatePetType(string $name, $timestamp): int
    {
        $typeId = DB::table('pet_types')->where('pet_type_name', $name)->value('pet_type_id');
        if ($typeId) {
            return (int) $typeId;
        }

        return DB::table('pet_types')->insertGetId([
            'pet_type_name' => $name,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ], 'pet_type_id');
    }

    private function findOrCreateBreed(string $name, int $typeId, $timestamp): int
    {
        $breedId = DB::table('pet_breeds')
            ->where('pet_breed_name', $name)
            ->where('pet_type_id', $typeId)
            ->value('pet_breed_id');
        if ($breedId) {
            return (int) $breedId;
        }

        return DB::table('pet_breeds')->insertGetId([
            'pet_breed_name' => $name,
            'pet_type_id' => $typeId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ], 'pet_breed_id');
    }

    private function findOrCreatePet(string $name, int $customerId, int $breedId, string $gender, float $weight, $timestamp): array
    {
        $petId = DB::table('pets')
            ->where('pet_name', $name)
            ->where('customer_id', $customerId)
            ->value('id');

        if (! $petId) {
            $petId = DB::table('pets')->insertGetId([
                'pet_name' => $name,
                'pet_birth' => now()->subYears(3)->toDateString(),
                'pet_gender' => $gender,
                'pet_weight' => $weight,
                'customer_id' => $customerId,
                'pet_breed_id' => $breedId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ], 'id');
        }

        return ['id' => (int) $petId, 'customer_id' => $customerId, 'weight' => $weight];
    }
}