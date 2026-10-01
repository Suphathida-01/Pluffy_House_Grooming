<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminSchedule;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminController extends Controller
{
    public function services()
    {
        $services = Service::orderBy('service_id')->get();

        return view('services', compact('services'));
    }

    public function createService()
    {
        $service = new Service;

        return view('service-form', compact('service'));
    }

    public function editService(Service $service)
    {
        return view('service-form', compact('service'));
    }

    public function storeService(Request $request)
    {
        $data = $this->validateService($request);
        $data['service_price'] = $data['price_small'];

        Service::create($data);

        return redirect()->route('services')->with('success', 'เพิ่มบริการเรียบร้อยแล้ว');
    }

    public function updateService(Request $request, Service $service)
    {
        $data = $this->validateService($request);
        $data['service_price'] = $data['price_small'];

        $service->update($data);

        return redirect()->route('services')->with('success', 'บันทึกการแก้ไขเรียบร้อยแล้ว');
    }

    public function deleteService(Service $service)
    {
        $service->delete();

        return redirect()->route('services')->with('success', 'ลบบริการเรียบร้อยแล้ว');
    }

    private function validateService(Request $request): array
    {
        return $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'service_description' => ['required', 'string'],
            'service_duration_minutes' => ['required', 'integer', 'min:1'],
            'price_small' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'price_medium' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'price_large' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'service_status' => ['required', 'in:active,inactive'],
        ]);
    }

    public function payments(Request $request)
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:all,pending,completed,failed'],
        ]);

        $selectedDate = $filters['date'] ?? now()->toDateString();
        $selectedStatus = $filters['status'] ?? 'all';
        $dailyPayments = Payment::whereDate('payment_date', $selectedDate);

        $summary = [
            'dailyRevenue' => (clone $dailyPayments)
                ->where('payment_status', 'completed')
                ->sum('payment_amount'),
            'dailyCompleted' => (clone $dailyPayments)
                ->where('payment_status', 'completed')
                ->count(),
            'pendingCount' => Payment::where('payment_status', 'pending')->count(),
            'pendingTotal' => Payment::where('payment_status', 'pending')->sum('payment_amount'),
        ];

        $payments = Payment::with(['booking.customer.user', 'booking.service'])
            ->whereDate('payment_date', $selectedDate)
            ->when($selectedStatus !== 'all', function ($query) use ($selectedStatus) {
                $query->where('payment_status', $selectedStatus);
            })
            ->latest('payment_date')
            ->get();

        return view('payments', compact('payments', 'selectedDate', 'selectedStatus', 'summary'));
    }

    public function staff(Request $request)
    {
        $filters = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $selectedMonth = $filters['month'] ?? substr($filters['date'] ?? now()->toDateString(), 0, 7);
        $selectedDate = $filters['date'] ?? now()->toDateString();
        if (substr($selectedDate, 0, 7) !== $selectedMonth) {
            $selectedDate = $selectedMonth . '-01';
        }

        $monthStart = Carbon::createFromFormat('Y-m-d', $selectedMonth . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $schedules = AdminSchedule::with(['admin.user'])
            ->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();
        $bookings = Booking::with(['pet', 'service', 'admin.user'])
            ->whereBetween('booking_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();
        $closedDates = DB::table('store_closures')
            ->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->pluck('work_date')
            ->map(fn ($date) => (string) $date)
            ->all();

        $calendarDays = [];
        $day = $calendarStart->copy();
        while ($day->lte($calendarEnd)) {
            $date = $day->toDateString();
            $daySchedules = $schedules->where('work_date', $date);
            $workingStaffCount = $daySchedules
                ->where('status', 'available')
                ->pluck('admin_id')
                ->unique()
                ->count();
            $isClosed = in_array($date, $closedDates, true);

            $calendarDays[] = [
                'date' => $date,
                'day' => $day->day,
                'is_current_month' => $day->format('Y-m') === $selectedMonth,
                'is_selected' => $date === $selectedDate,
                'is_today' => $date === now()->toDateString(),
                'is_closed' => $isClosed,
                'working_staff_count' => $workingStaffCount,
                'booking_count' => $bookings->where('booking_date', $date)->count(),
            ];

            $day->addDay();
        }

        $selectedSchedules = $schedules
            ->where('work_date', $selectedDate)
            ->sortBy('start_time')
            ->values();
        $dailyBookings = $bookings
            ->where('booking_date', $selectedDate)
            ->sortBy('booking_start_time')
            ->values();

        $technicians = Admin::with('user')->orderBy('admin_id')->get();
        $dailyStaff = [];
        foreach ($technicians as $admin) {
            $shifts = $selectedSchedules
                ->where('admin_id', $admin->admin_id)
                ->where('status', 'available')
                ->values();

            if ($shifts->isEmpty()) {
                continue;
            }

            $staffBookings = $dailyBookings->where('admin_id', $admin->admin_id);
            $name = $admin->user?->user_name ?? ('ช่าง #' . $admin->admin_id);
            $dailyStaff[] = [
                'name' => $name,
                'initial' => mb_substr($name, 0, 1),
                'shifts' => $shifts,
                'booking_count' => $staffBookings->count(),
            ];
        }

        $staffOptions = $technicians->map(fn ($admin) => [
            'id' => $admin->admin_id,
            'name' => $admin->user?->user_name ?? ('ช่าง #' . $admin->admin_id),
        ]);
        $previousMonth = $monthStart->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthStart->copy()->addMonth()->format('Y-m');

        return view('staff', compact(
            'calendarDays',
            'dailyBookings',
            'dailyStaff',
            'nextMonth',
            'previousMonth',
            'selectedDate',
            'selectedMonth',
            'staffOptions',
        ));
    }

    public function storeStaff(Request $request)
    {
        $data = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,user_email',
            'user_phone' => 'required|string|max:20',
            'user_password' => 'required|string|min:8',
        ]);

        DB::beginTransaction();

        try {
            $userId = DB::table('users')->insertGetId([
                'user_name' => $data['user_name'],
                'user_email' => $data['user_email'],
                'user_phone' => $data['user_phone'],
                'user_password' => Hash::make($data['user_password']),
                'user_role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ], 'user_id');

            Admin::create(['user_id' => $userId]);
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return redirect()->route('staff')->with('success', 'เพิ่มช่างเรียบร้อยแล้ว');
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'admin_id' => 'required|exists:admins,admin_id',
            'work_date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:available,unavailable',
        ]);

        AdminSchedule::create($data);

        return redirect()->route('staff', [
            'month' => substr($data['work_date'], 0, 7),
            'date' => $data['work_date'],
        ])->with('success', 'บันทึกตารางงานช่างเรียบร้อยแล้ว');
    }

    public function toggleStoreClosure(Request $request)
    {
        $data = $request->validate([
            'work_date' => ['required', 'date_format:Y-m-d'],
            'closed' => ['required', 'boolean'],
        ]);

        $closureExists = DB::table('store_closures')
            ->where('work_date', $data['work_date'])
            ->exists();

        if ($request->boolean('closed') && ! $closureExists) {
            DB::table('store_closures')->insert([
                'work_date' => $data['work_date'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif (! $request->boolean('closed') && $closureExists) {
            DB::table('store_closures')->where('work_date', $data['work_date'])->delete();
        }

        $message = $request->boolean('closed') ? 'ปิดร้านในวันที่เลือกแล้ว' : 'เปิดร้านในวันที่เลือกแล้ว';

        return redirect()->route('staff', [
            'month' => substr($data['work_date'], 0, 7),
            'date' => $data['work_date'],
        ])->with('success', $message);
    }

    public function bookingsIndex(Request $request)
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


    public function customersIndex(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $customers = DB::table('customers as c')
            ->join('users as u', 'c.user_id', '=', 'u.user_id')
            ->whereNull('c.deleted_at')
            ->whereNull('u.deleted_at')
            ->where('u.user_role', 'customer')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('u.user_name', 'like', '%'.$search.'%')
                        ->orWhere('u.user_email', 'like', '%'.$search.'%')
                        ->orWhere('u.user_phone', 'like', '%'.$search.'%');
                });
            })
            ->select([
                'c.customer_id',
                'c.created_at as customer_since',
                'u.user_name',
                'u.user_email',
                'u.user_phone',
            ])
            ->selectSub(function ($query) {
                $query->from('pets as p')
                    ->whereColumn('p.customer_id', 'c.customer_id')
                    ->whereNull('p.deleted_at')
                    ->selectRaw('COUNT(*)');
            }, 'pet_count')
            ->selectSub(function ($query) {
                $query->from('bookings as b')
                    ->whereColumn('b.customer_id', 'c.customer_id')
                    ->whereNull('b.deleted_at')
                    ->selectRaw('COUNT(*)');
            }, 'booking_count')
            ->orderBy('u.user_name')
            ->paginate(15)
            ->withQueryString();

        $customerCount = DB::table('customers as c')
            ->join('users as u', 'c.user_id', '=', 'u.user_id')
            ->whereNull('c.deleted_at')
            ->whereNull('u.deleted_at')
            ->where('u.user_role', 'customer')
            ->count();
        $petCount = DB::table('pets')->whereNull('deleted_at')->count();
        $bookingCount = DB::table('bookings')->whereNull('deleted_at')->count();

        return view('customers.index', compact(
            'customers',
            'customerCount',
            'petCount',
            'bookingCount',
            'filters'
        ));
    }

    public function show(int $customerId)
    {
        $customer = DB::table('customers as c')
            ->join('users as u', 'c.user_id', '=', 'u.user_id')
            ->where('c.customer_id', $customerId)
            ->where('u.user_role', 'customer')
            ->whereNull('c.deleted_at')
            ->whereNull('u.deleted_at')
            ->select([
                'c.customer_id',
                'c.created_at as customer_since',
                'u.user_id',
                'u.user_name',
                'u.user_email',
                'u.user_phone',
            ])
            ->first();

        abort_unless($customer, 404);

        $pets = DB::table('pets as p')
            ->join('pet_breeds as breed', 'p.pet_breed_id', '=', 'breed.pet_breed_id')
            ->join('pet_types as type', 'breed.pet_type_id', '=', 'type.pet_type_id')
            ->where('p.customer_id', $customerId)
            ->whereNull('p.deleted_at')
            ->select([
                'p.id',
                'p.pet_name',
                'p.pet_birth',
                'p.pet_gender',
                'p.pet_weight',
                'p.pet_notes',
                'breed.pet_breed_name',
                'type.pet_type_name',
            ])
            ->orderBy('p.pet_name')
            ->get();

        $bookings = DB::table('bookings as b')
            ->join('pets as p', 'b.pet_id', '=', 'p.id')
            ->join('services as s', 'b.service_id', '=', 's.service_id')
            ->where('b.customer_id', $customerId)
            ->whereNull('b.deleted_at')
            ->whereNull('p.deleted_at')
            ->whereNull('s.deleted_at')
            ->select([
                'b.booking_id',
                'b.booking_date',
                'b.booking_start_time',
                'b.booking_total_price',
                'b.booking_status',
                'p.pet_name',
                's.service_name',
            ])
            ->orderByDesc('b.booking_date')
            ->orderByDesc('b.booking_start_time')
            ->take(20)
            ->get();

        return view('customers.show', compact('customer', 'pets', 'bookings'));
    }

    public function update(Request $request, int $customerId)
    {
        $customer = DB::table('customers as c')
            ->join('users as u', 'c.user_id', '=', 'u.user_id')
            ->where('c.customer_id', $customerId)
            ->where('u.user_role', 'customer')
            ->whereNull('c.deleted_at')
            ->whereNull('u.deleted_at')
            ->select('c.customer_id', 'u.user_id')
            ->first();

        abort_unless($customer, 404);

        $validated = $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'user_email')->ignore($customer->user_id, 'user_id'),
            ],
            'user_phone' => ['required', 'string', 'max:30'],
        ]);

        DB::table('users')
            ->where('user_id', $customer->user_id)
            ->update([
                'user_name' => $validated['user_name'],
                'user_email' => $validated['user_email'],
                'user_phone' => $validated['user_phone'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('customers.show', $customerId)
            ->with('success', 'บันทึกข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
}
