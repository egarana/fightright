<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'max_attendance_qty',
        'duration_days',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_attendance_qty' => 'integer',
            'duration_days' => 'integer',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all members with this membership.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_memberships')
            ->withPivot([
                'id',
                'snapshot_membership_name',
                'snapshot_max_attendance_qty',
                'snapshot_duration_days',
                'snapshot_price',
                'started_at',
                'expired_at',
                'status',
            ])
            ->withTimestamps();
    }

    /**
     * Get all member_membership records.
     */
    public function memberMemberships(): HasMany
    {
        return $this->hasMany(MemberMembership::class);
    }

    /**
     * Check if this membership has unlimited attendance.
     */
    public function isUnlimited(): bool
    {
        return $this->max_attendance_qty === null;
    }

    /**
     * Scope for active memberships only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
