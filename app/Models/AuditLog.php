<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'admin_id', 'admin_name', 'action', 'module',
        'subject_type', 'subject_id', 'description',
        'old_values', 'new_values', 'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Single entry point every controller should call after a mutation.
     *
     * AuditLog::record(
     *     action: 'update',
     *     module: 'Spare Parts',
     *     subject: $sparePart,
     *     description: "Updated price ₹700 -> ₹750",
     *     old: ['price' => 700],
     *     new: ['price' => 750],
     * );
     *
     * This is the same pattern Phase 3's `stock_movements` table will use for
     * every stock-changing operation — one write path, fully reconstructable
     * history, nothing edited in place.
     */
    public static function record(
        string $action,
        string $module,
        ?Model $subject = null,
        ?string $description = null,
        ?array $old = null,
        ?array $new = null
    ): self {
        $admin = Auth::guard('admin')->user();

        return self::create([
            'admin_id' => $admin?->id,
            'admin_name' => $admin?->name,
            'action' => $action,
            'module' => $module,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'description' => $description,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
        ]);
    }
}
