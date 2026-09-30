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
}