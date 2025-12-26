<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'membership_id',
        'snapshot_membership_name',
        'snapshot_max_attendance_qty',
        'snapshot_duration_days',
        'snapshot_price',
        'started_at',
        'expired_at',
        'status',
    ];

    /**
     * Accessors to append to model's array/JSON form.
     */
    protected $appends = ['remaining_qty', 'used_qty'];

    protected function casts(): array
    {
        return [
            'snapshot_max_attendance_qty' => 'integer',
            'snapshot_duration_days' => 'integer',
            'snapshot_price' => 'decimal:2',
            'started_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Get the member.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the membership.
     */
    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    /**
     * Get all attendances for this membership.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get remaining attendance quota (calculated from attendances count).
     * Returns null if unlimited.
     */
    public function getRemainingQtyAttribute(): ?int
    {
        if ($this->snapshot_max_attendance_qty === null) {
            return null; // Unlimited
        }

        $usedQty = $this->attendances()->count();

        return max(0, $this->snapshot_max_attendance_qty - $usedQty);
    }

    /**
     * Get used attendance count.
     */
    public function getUsedQtyAttribute(): int
    {
        return $this->attendances()->count();
    }

    /**
     * Check if this membership is expired.
     */
    public function isExpired(): bool
    {
        return $this->expired_at->isPast();
    }

    /**
     * Check if member can check-in with this membership.
     */
    public function canCheckIn(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        // Check if quota available (null = unlimited)
        $remaining = $this->remaining_qty;
        if ($remaining !== null && $remaining <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active memberships.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for non-expired memberships.
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expired_at', '>', now());
    }
}
