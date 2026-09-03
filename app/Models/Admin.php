<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * Guard used by Spatie roles/permissions for this model.
     * Must match `guard_name` on roles/permissions and the `admin` guard in auth.php.
     */
    protected string $guard_name = 'admin';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'mobile_number',
        'avatar',
        'status',
        'login',
        'is_super_admin',
        'email_notifications',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'login' => 'boolean',
        'is_super_admin' => 'boolean',
        'email_notifications' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Super admins bypass every permission check.
     * AdminsController and every future controller should prefer
     * `$this->user->can(...)` — Spatie's `can()` already respects this
     * via the Gate::before hook registered in AuthServiceProvider.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }
}
