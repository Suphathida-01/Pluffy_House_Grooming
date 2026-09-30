<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,confirmed,completed'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $bookings = DB::table('bookings as b')
            ->join('customers as c', 'b.customer_id', '=', 'c.customer_id')
            ->join('users as u', 'c.user_id', '=', 'u.user_id')
            ->join('pets as p', 'b.pet_id', '=', 'p.id')
            ->join('pet_breeds as breed', 'p.pet_breed_id', '=', 'breed.pet_breed_id')
            ->join('services as s', 'b.service_id', '=', 's.service_id')
            ->leftJoin('admins as a', 'b.admin_id', '=', 'a.admin_id')
            ->leftJoin('users as au', 'a.user_id', '=', 'au.user_id')
            ->whereNull('b.deleted_at')
            ->whereNull('c.deleted_at')
            ->whereNull('u.deleted_at')
            ->whereNull('p.deleted_at')
            ->whereNull('s.deleted_at')
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('b.booking_status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->where('b.booking_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->where('b.booking_date', '<=', $date))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('u.user_name', 'like', '%'.$search.'%')
                        ->orWhere('u.user_email', 'like', '%'.$search.'%')
                        ->orWhere('p.pet_name', 'like', '%'.$search.'%')
                        ->orWhere('s.service_name', 'like', '%'.$search.'%');
                });
            })
            ->select([
                'b.booking_id',
                'b.booking_date',
                'b.booking_start_time',
                'b.booking_end_time',
                'b.booking_total_price',
                'b.booking_status',
                'b.customer_id',
                'u.user_name as customer_name',
                'u.user_phone as customer_phone',
                'p.pet_name',
                'breed.pet_breed_name',
                's.service_name',
                'au.user_name as admin_name',
            ])
            ->orderByDesc('b.booking_date')
            ->orderBy('b.booking_start_time')
            ->paginate(15)
            ->withQueryString();

        $bookingStats = [
            'pending' => DB::table('bookings')->whereNull('deleted_at')->where('booking_status', 'pending')->count(),
            'confirmed' => DB::table('bookings')->whereNull('deleted_at')->where('booking_status', 'confirmed')->count(),
            'completed' => DB::table('bookings')->whereNull('deleted_at')->where('booking_status', 'completed')->count(),
        ];

        return view('bookings.index', compact('bookings', 'bookingStats', 'filters'));
    }

    public function create(Request $request)
    {
        $filters = $request->validate([
            'customer_id' => ['nullable', 'integer'],
            'service_id' => ['nullable', 'integer'],
            'booking_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);

        $customers = DB::table('customers as c')
            ->join('users as u', 'c.user_id', '=', 'u.user_id')
            ->whereNull('c.deleted_at')
            ->whereNull('u.deleted_at')
            ->where('u.user_role', 'customer')
            ->orderBy('u.user_name')
            ->select('c.customer_id', 'u.user_name', 'u.user_email', 'u.user_phone')
            ->get();

        $services = DB::table('services')
            ->where('service_status', 'active')
            ->whereNull('deleted_at')
            ->orderBy('service_name')
            ->get();

        $selectedCustomerId = $filters['customer_id'] ?? null;
        $selectedServiceId = $filters['service_id'] ?? $services->first()?->service_id;
        $selectedDate = $filters['booking_date'] ?? today()->toDateString();
        $selectedService = $services->firstWhere('service_id', (int) $selectedServiceId);

        $pets = collect();
        if ($selectedCustomerId) {
            $pets = DB::table('pets as p')
                ->join('pet_breeds as breed', 'p.pet_breed_id', '=', 'breed.pet_breed_id')
                ->join('pet_types as type', 'breed.pet_type_id', '=', 'type.pet_type_id')
                ->where('p.customer_id', $selectedCustomerId)
                ->whereNull('p.deleted_at')
                ->orderBy('p.pet_name')
                ->select('p.id', 'p.pet_name', 'breed.pet_breed_name', 'type.pet_type_name')
                ->get();
        }

        $availableSlots = $selectedService
            ? $this->availableSlots($selectedDate, $selectedService)
            : [];

        return view('bookings.create', compact(
            'customers',
            'services',
            'pets',
            'availableSlots',
            'selectedCustomerId',
            'selectedServiceId',
            'selectedDate',
            'selectedService'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,customer_id'],
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'service_id' => ['required', 'integer', 'exists:services,service_id'],
            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'schedule_slot' => ['required', 'string', 'max:40'],
        ]);

        $bookingId = DB::transaction(function () use ($validated) {
            $customer = DB::table('customers as c')
                ->join('users as u', 'c.user_id', '=', 'u.user_id')
                ->where('c.customer_id', $validated['customer_id'])
                ->where('u.user_role', 'customer')
                ->whereNull('c.deleted_at')
                ->whereNull('u.deleted_at')
                ->select('c.customer_id')
                ->first();

            if (!$customer) {
                throw ValidationException::withMessages([
                    'customer_id' => 'ไม่พบข้อมูลลูกค้าที่ใช้งานได้',
                ]);
            }

            $pet = DB::table('pets')
                ->where('id', $validated['pet_id'])
                ->where('customer_id', $customer->customer_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$pet) {
                throw ValidationException::withMessages([
                    'pet_id' => 'กรุณาเลือกสัตว์เลี้ยงของลูกค้าที่เลือก',
                ]);
            }

            $service = DB::table('services')
                ->where('service_id', $validated['service_id'])
                ->where('service_status', 'active')
                ->whereNull('deleted_at')
                ->first();

            if (!$service) {
                throw ValidationException::withMessages([
                    'service_id' => 'บริการนี้ไม่พร้อมให้จองแล้ว กรุณาเลือกบริการใหม่',
                ]);
            }

            $availableSlots = $this->availableSlots($validated['booking_date'], $service);
            $selectedSlot = collect($availableSlots)->firstWhere('value', $validated['schedule_slot']);

            if (!$selectedSlot) {
                throw ValidationException::withMessages([
                    'schedule_slot' => 'เวลานี้ถูกจองไปแล้วหรือไม่อยู่ในเวลาทำงาน กรุณาเลือกเวลาใหม่',
                ]);
            }

            $startTime = $selectedSlot['start_time'];
            $endTime = Carbon::parse($validated['booking_date'].' '.$startTime)
                ->addMinutes((int) $service->service_duration_minutes)
                ->format('H:i:s');
            $now = now();

            return DB::table('bookings')->insertGetId([
                'booking_date' => $validated['booking_date'],
                'booking_start_time' => $startTime,
                'booking_end_time' => $endTime,
                'booking_total_price' => $service->service_price,
                'booking_status' => 'pending',
                'customer_id' => $customer->customer_id,
                'admin_id' => $selectedSlot['admin_id'],
                'service_id' => $service->service_id,
                'pet_id' => $pet->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return redirect()
            ->route('bookings.index')
            ->with('success', 'สร้างรายการจองหมายเลข '.$bookingId.' เรียบร้อยแล้ว');
    }

    public function updateStatus(Request $request, int $bookingId)
    {
        $validated = $request->validate([
            'booking_status' => ['required', 'in:pending,confirmed,completed'],
        ]);

        $bookingExists = DB::table('bookings')
            ->where('booking_id', $bookingId)
            ->whereNull('deleted_at')
            ->exists();

        if (!$bookingExists) {
            return back()->withErrors(['booking' => 'ไม่พบรายการจองนี้']);
        }

        DB::table('bookings')
            ->where('booking_id', $bookingId)
            ->whereNull('deleted_at')
            ->update([
                'booking_status' => $validated['booking_status'],
                'updated_at' => now(),
            ]);

        return back()->with('success', 'อัปเดตสถานะการจองเรียบร้อยแล้ว');
    }

    private function availableSlots(string $date, object $service): array
    {
        $duration = (int) $service->service_duration_minutes;
        if ($duration < 1) {
            return [];
        }

        $schedules = DB::table('admin_schedules as schedule')
            ->leftJoin('admins as admin', 'schedule.admin_id', '=', 'admin.admin_id')
            ->leftJoin('users as user', 'admin.user_id', '=', 'user.user_id')
            ->where('schedule.work_date', $date)
            ->where('schedule.status', 'available')
            ->whereNull('admin.deleted_at')
            ->select(
                'schedule.schedule_id',
                'schedule.admin_id',
                'schedule.start_time',
                'schedule.end_time',
                'user.user_name as admin_name'
            )
            ->orderBy('schedule.start_time')
            ->get();

        $slots = [];
        foreach ($schedules as $schedule) {
            $cursor = Carbon::parse($date.' '.$schedule->start_time);
            $shiftEnd = Carbon::parse($date.' '.$schedule->end_time);

            while ($cursor->copy()->addMinutes($duration)->lte($shiftEnd)) {
                $startTime = $cursor->format('H:i:s');
                $endTime = $cursor->copy()->addMinutes($duration)->format('H:i:s');
                $isPast = $date === today()->toDateString() && $cursor->lte(now());
                $hasConflict = DB::table('bookings')
                    ->where('admin_id', $schedule->admin_id)
                    ->where('booking_date', $date)
                    ->whereIn('booking_status', ['pending', 'confirmed', 'completed'])
                    ->whereNull('deleted_at')
                    ->where('booking_start_time', '<', $endTime)
                    ->where('booking_end_time', '>', $startTime)
                    ->exists();

                if (!$isPast && !$hasConflict) {
                    $adminName = $schedule->admin_name ?: 'ช่าง #'.$schedule->admin_id;
                    $slots[] = [
                        'value' => $schedule->schedule_id.'|'.$startTime,
                        'start_time' => $startTime,
                        'admin_id' => $schedule->admin_id,
                        'label' => Carbon::parse($startTime)->format('H:i').' - '.Carbon::parse($endTime)->format('H:i').' น. ('.$adminName.')',
                    ];
                }

                $cursor->addMinutes(30);
            }
        }

        return $slots;
    }
}
