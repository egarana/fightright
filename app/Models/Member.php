<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_code',
        'name',
        'email',
        'phone',
        'address',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'phone' => 'array',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            if (empty($member->member_code)) {
                $member->member_code = self::generateMemberCode();
            }
        });
    }

    /**
     * Generate a unique member code.
     * Format: 10 uppercase alphanumeric characters (e.g., HMJHXPFN54)
     */
    public static function generateMemberCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
            // Ensure it's alphanumeric only (no special characters)
            $code = preg_replace('/[^A-Z0-9]/', '', $code . Str::random(2));
            $code = substr($code, 0, 10);
        } while (self::where('member_code', $code)->exists());

        return $code;
    }


    /**
     * Get all memberships for this member (many-to-many via pivot).
     */
    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(Membership::class, 'member_memberships')
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
     * Get active memberships only.
     */
    public function activeMemberships(): HasMany
    {
        return $this->memberMemberships()
            ->where('status', 'active')
            ->where('expired_at', '>', now());
    }
}
