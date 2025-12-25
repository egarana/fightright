<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_membership_id',
        'snapshot_member_name',
        'snapshot_membership_name',
        'snapshot_remaining_before',
        'check_in_at',
        'check_out_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_remaining_before' => 'integer',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    /**
     * Get the member membership.
     */
    public function memberMembership(): BelongsTo
    {
        return $this->belongsTo(MemberMembership::class);
    }

    /**
     * Check if currently checked-in (no check-out yet).
     */
    public function isCheckedIn(): bool
    {
        return $this->check_out_at === null;
    }

    /**
     * Get duration in minutes (if checked out).
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if ($this->check_out_at === null) {
            return null;
        }

        return $this->check_in_at->diffInMinutes($this->check_out_at);
    }
}
