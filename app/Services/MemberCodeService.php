<?php

namespace App\Services;

use App\Models\Member;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Service for encoding/decoding member IDs for URL obfuscation.
 * 
 * Uses Hashids to convert member IDs to unguessable URL-safe strings.
 * Without knowing the salt, attackers cannot:
 * - Decode the hash to get the real ID
 * - Enumerate other member URLs by incrementing IDs
 */
class MemberCodeService
{
    /**
     * Encode a member ID to an obfuscated URL-safe string.
     */
    public static function encode(int $memberId): string
    {
        return Hashids::encode($memberId);
    }

    /**
     * Decode an obfuscated string back to member ID.
     * Returns null if the hash is invalid.
     */
    public static function decode(string $hash): ?int
    {
        $decoded = Hashids::decode($hash);

        if (empty($decoded)) {
            return null;
        }

        return $decoded[0];
    }

    /**
     * Find a member by their encoded URL hash.
     * Returns null if hash is invalid or member not found.
     */
    public static function findByHash(string $hash): ?Member
    {
        $memberId = self::decode($hash);

        if ($memberId === null) {
            return null;
        }

        return Member::find($memberId);
    }
}
