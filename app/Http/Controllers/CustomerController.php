<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
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
