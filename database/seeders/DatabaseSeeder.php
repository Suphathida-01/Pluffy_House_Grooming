<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();

            // Create demo accounts using the column names defined by the users table.
            $seedUser = function (array $user) use ($now): int {
                DB::table('users')->updateOrInsert(
                    ['user_email' => $user['user_email']],
                    [
                        'user_name' => $user['user_name'],
                        'user_phone' => $user['user_phone'],
                        'user_password' => Hash::make('password123'),
                        'user_role' => $user['user_role'],
                        'deleted_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                return (int) DB::table('users')
                    ->where('user_email', $user['user_email'])
                    ->value('user_id');
            };

            $adminUserId = $seedUser([
                'user_name' => 'Admin Test',
                'user_email' => 'admin@example.com',
                'user_phone' => '0812345678',
                'user_role' => 'admin',
            ]);

            $customerUsers = [
                'customer@example.com' => $seedUser([
                    'user_name' => 'Customer Test',
                    'user_email' => 'customer@example.com',
                    'user_phone' => '0898765432',
                    'user_role' => 'customer',
                ]),
                'nicha@example.com' => $seedUser([
                    'user_name' => 'ณิชา ใจดี',
                    'user_email' => 'nicha@example.com',
                    'user_phone' => '0891112233',
                    'user_role' => 'customer',
                ]),
                'thanat@example.com' => $seedUser([
                    'user_name' => 'ธนัท รักสัตว์',
                    'user_email' => 'thanat@example.com',
                    'user_phone' => '0862223344',
                    'user_role' => 'customer',
                ]),
            ];

            $seedProfile = function (string $table, int $userId) use ($now): int {
                DB::table($table)->updateOrInsert(
                    ['user_id' => $userId],
                    ['deleted_at' => null, 'created_at' => $now, 'updated_at' => $now]
                );

                $primaryKey = $table === 'admins' ? 'admin_id' : 'customer_id';

                return (int) DB::table($table)
                    ->where('user_id', $userId)
                    ->value($primaryKey);
            };

            $adminId = $seedProfile('admins', $adminUserId);

            $customerIds = [];
            foreach ($customerUsers as $email => $userId) {
                $customerIds[$email] = $seedProfile('customers', $userId);
            }

            $services = [
                'อาบน้ำและตัดขน' => ['อาบน้ำและตัดแต่งขนตามทรงที่ต้องการ', 120, 850],
                'อาบน้ำสปาถนอมผิว' => ['อาบน้ำพร้อมสปาบำรุงและถนอมผิวหนัง', 90, 700],
                'กำจัดเห็บหมัด' => ['ทำความสะอาดและดูแลปัญหาเห็บหมัดสำหรับสัตว์เลี้ยง', 30, 250],
            ];

            // Rename the former demo services while keeping their booking links intact.
            $legacyServiceNames = [
                'อาบน้ำและเป่าขน' => 'อาบน้ำและตัดขน',
                'อาบน้ำตัดแต่งขน' => 'อาบน้ำและตัดขน',
                'ตัดเล็บและทำความสะอาดหู' => 'กำจัดเห็บหมัด',
                'สปาบำรุงขน' => 'อาบน้ำสปาถนอมผิว',
            ];

            foreach ($legacyServiceNames as $legacyName => $currentName) {
                $legacyIds = DB::table('services')
                    ->where('service_name', $legacyName)
                    ->pluck('service_id');

                foreach ($legacyIds as $legacyId) {
                    $currentId = DB::table('services')
                        ->where('service_name', $currentName)
                        ->where('service_id', '!=', $legacyId)
                        ->value('service_id');

                    if ($currentId) {
                        DB::table('bookings')
                            ->where('service_id', $legacyId)
                            ->update(['service_id' => $currentId, 'updated_at' => $now]);
                        DB::table('services')->where('service_id', $legacyId)->delete();
                    } else {
                        DB::table('services')
                            ->where('service_id', $legacyId)
                            ->update(['service_name' => $currentName, 'updated_at' => $now]);
                    }
                }
            }

            $serviceIds = [];
            foreach ($services as $name => [$description, $duration, $price]) {
                DB::table('services')->updateOrInsert(
                    ['service_name' => $name],
                    [
                        'service_description' => $description,
                        'service_duration_minutes' => $duration,
                        'service_price' => $price,
                        'service_status' => 'active',
                        'deleted_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $serviceIds[$name] = (int) DB::table('services')
                    ->where('service_name', $name)
                    ->value('service_id');
            }

            DB::table('services')
                ->whereNotIn('service_name', array_keys($services))
                ->update(['service_status' => 'inactive', 'updated_at' => $now]);

            $petTypes = [];
            foreach (['สุนัข', 'แมว'] as $typeName) {
                DB::table('pet_types')->updateOrInsert(
                    ['pet_type_name' => $typeName],
                    ['created_at' => $now, 'updated_at' => $now]
                );
                $petTypes[$typeName] = (int) DB::table('pet_types')
                    ->where('pet_type_name', $typeName)
                    ->value('pet_type_id');
            }

            $breeds = [];
            foreach ([
                'พุดเดิ้ล' => $petTypes['สุนัข'],
                'โกลเด้นรีทรีฟเวอร์' => $petTypes['สุนัข'],
                'บริติชชอร์ตแฮร์' => $petTypes['แมว'],
            ] as $breedName => $petTypeId) {
                DB::table('pet_breeds')->updateOrInsert(
                    ['pet_breed_name' => $breedName, 'pet_type_id' => $petTypeId],
                    ['created_at' => $now, 'updated_at' => $now]
                );
                $breeds[$breedName] = (int) DB::table('pet_breeds')
                    ->where('pet_breed_name', $breedName)
                    ->where('pet_type_id', $petTypeId)
                    ->value('pet_breed_id');
            }

            $seedPet = function (array $pet): int {
                $existingPet = DB::table('pets')
                    ->where('customer_id', $pet['customer_id'])
                    ->where('pet_name', $pet['pet_name'])
                    ->first();

                $values = [
                    'pet_birth' => $pet['pet_birth'],
                    'pet_gender' => $pet['pet_gender'],
                    'pet_weight' => $pet['pet_weight'],
                    'pet_notes' => $pet['pet_notes'],
                    'pet_breed_id' => $pet['pet_breed_id'],
                    'deleted_at' => null,
                    'updated_at' => now(),
                ];

                if ($existingPet) {
                    DB::table('pets')->where('id', $existingPet->id)->update($values);

                    return (int) $existingPet->id;
                }

                return (int) DB::table('pets')->insertGetId(array_merge($values, [
                    'pet_name' => $pet['pet_name'],
                    'customer_id' => $pet['customer_id'],
                    'created_at' => now(),
                ]));
            };

            $pets = [
                'customer@example.com' => $seedPet([
                    'pet_name' => 'โมจิ',
                    'pet_birth' => '2022-04-12',
                    'pet_gender' => 'female',
                    'pet_weight' => 4.20,
                    'pet_notes' => 'ขนสีขาว ชอบอาบน้ำอุ่น',
                    'customer_id' => $customerIds['customer@example.com'],
                    'pet_breed_id' => $breeds['พุดเดิ้ล'],
                ]),
                'nicha@example.com' => $seedPet([
                    'pet_name' => 'โกโก้',
                    'pet_birth' => '2021-08-20',
                    'pet_gender' => 'male',
                    'pet_weight' => 28.50,
                    'pet_notes' => 'สุนัขขนาดใหญ่ ใจเย็น',
                    'customer_id' => $customerIds['nicha@example.com'],
                    'pet_breed_id' => $breeds['โกลเด้นรีทรีฟเวอร์'],
                ]),
                'thanat@example.com' => $seedPet([
                    'pet_name' => 'ลูน่า',
                    'pet_birth' => '2023-01-05',
                    'pet_gender' => 'female',
                    'pet_weight' => 4.80,
                    'pet_notes' => 'แมวขนสั้น ไม่ชอบเสียงดัง',
                    'customer_id' => $customerIds['thanat@example.com'],
                    'pet_breed_id' => $breeds['บริติชชอร์ตแฮร์'],
                ]),
            ];

            // Add one completed booking and payment in each of the latest six months.
            $completedSamples = [
                ['customer@example.com', 'customer@example.com', 'อาบน้ำและตัดขน', 0],
                ['nicha@example.com', 'nicha@example.com', 'อาบน้ำและตัดขน', 1],
                ['thanat@example.com', 'thanat@example.com', 'อาบน้ำสปาถนอมผิว', 2],
                ['customer@example.com', 'customer@example.com', 'อาบน้ำและตัดขน', 3],
                ['nicha@example.com', 'nicha@example.com', 'กำจัดเห็บหมัด', 4],
                ['thanat@example.com', 'thanat@example.com', 'อาบน้ำและตัดขน', 5],
            ];

            foreach ($completedSamples as [$email, $petEmail, $serviceName, $monthsAgo]) {
                $bookingDate = now()->startOfMonth()->subMonths($monthsAgo)->toDateString();
                $bookingTime = '10:00:00';
                $service = $services[$serviceName];
                $bookingKey = [
                    'customer_id' => $customerIds[$email],
                    'pet_id' => $pets[$petEmail],
                    'service_id' => $serviceIds[$serviceName],
                    'booking_date' => $bookingDate,
                    'booking_start_time' => $bookingTime,
                ];
                $bookingValues = [
                    'booking_end_time' => Carbon::parse($bookingTime)->addMinutes($service[1])->format('H:i:s'),
                    'booking_total_price' => $service[2],
                    'booking_status' => 'completed',
                    'admin_id' => $adminId,
                    'deleted_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                DB::table('bookings')->updateOrInsert($bookingKey, $bookingValues);
                $bookingId = (int) DB::table('bookings')->where($bookingKey)->value('booking_id');
                $paymentDate = now()->startOfMonth()->subMonths($monthsAgo)->setTime(12, 0);
                $transactionRef = 'DEMO-BOOKING-'.$bookingId;

                DB::table('payments')->updateOrInsert(
                    ['transaction_ref' => $transactionRef],
                    [
                        'payment_date' => $paymentDate,
                        'payment_amount' => $service[2],
                        'payment_method' => $monthsAgo % 2 === 0 ? 'qrCode' : 'creditCard',
                        'payment_status' => 'completed',
                        'booking_id' => $bookingId,
                        'deleted_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            // Include two current-month bookings in different workflow states.
            foreach ([
                ['customer@example.com', 'customer@example.com', 'อาบน้ำและตัดขน', 'pending', '14:00:00'],
                ['nicha@example.com', 'nicha@example.com', 'อาบน้ำและตัดขน', 'confirmed', '15:30:00'],
            ] as [$email, $petEmail, $serviceName, $status, $bookingTime]) {
                $service = $services[$serviceName];
                $bookingKey = [
                    'customer_id' => $customerIds[$email],
                    'pet_id' => $pets[$petEmail],
                    'service_id' => $serviceIds[$serviceName],
                    'booking_date' => now()->toDateString(),
                    'booking_start_time' => $bookingTime,
                ];

                DB::table('bookings')->updateOrInsert($bookingKey, [
                    'booking_end_time' => Carbon::parse($bookingTime)->addMinutes($service[1])->format('H:i:s'),
                    'booking_total_price' => $service[2],
                    'booking_status' => $status,
                    'admin_id' => $adminId,
                    'deleted_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Create one available work shift for the demo administrator.
            DB::table('admin_schedules')->updateOrInsert(
                ['admin_id' => $adminId, 'work_date' => now()->toDateString(), 'start_time' => '09:00:00'],
                ['end_time' => '18:00:00', 'status' => 'available', 'created_at' => $now, 'updated_at' => $now]
            );
        });
    }
}
