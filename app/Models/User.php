<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['user_name', 'user_email', 'user_phone', 'user_password', 'user_role'])]
#[Hidden(['user_password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected function casts(): array
    {
        return [
            'user_password' => 'hashed',
        ];
    }

    // ให้ Auth::attempt เทียบรหัสผ่านกับคอลัมน์ user_password
    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function isAdmin(): bool
    {
        return $this->user_role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->user_role === 'customer';
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id', 'user_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id', 'user_id');
    }

    public function initials(): string
    {
        $initials = Str::initials($this->user_name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
