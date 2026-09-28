<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * แสดงหน้า login-register (view เดียว มี tab สลับ)
     */
    public function show()
    {
        return view('login');
    }

    /**
     * POST /login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user_email'    => ['required', 'email'],
            'user_password' => ['required', 'string'],
        ]);

        // key ต้องชื่อ 'password' เพื่อให้ตรงกับ getAuthPassword() ใน User model
        $attempt = [
            'user_email' => $credentials['user_email'],
            'password'   => $credentials['user_password'],
        ];

        if (! Auth::attempt($attempt, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'user_email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->intended(
            $user->isAdmin() ? route('admin.dashboard') : route('dashboard')
        );
    }

    /**
     * POST /register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'user_name'             => ['required', 'string', 'max:255'],
            'user_email'            => ['required', 'email', Rule::unique('users', 'user_email')],
            'user_phone'            => ['required', 'string', 'max:20'],
            'user_password'         => ['required', 'string', 'min:8', 'confirmed'],
            // ต้อง field ชื่อ user_password_confirmation คู่กันในฟอร์ม
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'user_name'     => $validated['user_name'],
                'user_email'    => $validated['user_email'],
                'user_phone'    => $validated['user_phone'],
                'user_password' => Hash::make($validated['user_password']),
                'user_role'     => 'customer',
            ]);

            Customer::create([
                'user_id' => $user->user_id,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}