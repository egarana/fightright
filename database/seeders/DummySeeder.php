<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\Membership;
use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedMemberships();
        $members = $this->seedMembers();
        $this->seedMemberMemberships($members);
    }

    /**
     * Seed realistic gym membership packages.
     */
    private function seedMemberships(): void
    {
        $memberships = [
            [
                'name' => 'Bronze - Pemula',
                'description' => 'Paket untuk pemula yang baru memulai perjalanan kebugaran. Cocok untuk latihan ringan 2x seminggu.',
                'max_attendance_qty' => 8,
                'duration_days' => 30,
                'price' => 200000,
                'is_active' => true,
            ],
            [
                'name' => 'Silver - Regular',
                'description' => 'Paket regular untuk member aktif. Ideal untuk latihan 3x seminggu dengan akses penuh ke fasilitas gym.',
                'max_attendance_qty' => 12,
                'duration_days' => 30,
                'price' => 350000,
                'is_active' => true,
            ],
            [
                'name' => 'Gold - Premium',
                'description' => 'Paket premium dengan kuota lebih banyak. Termasuk akses ke kelas grup dan konsultasi trainer dasar.',
                'max_attendance_qty' => 20,
                'duration_days' => 30,
                'price' => 500000,
                'is_active' => true,
            ],
            [
                'name' => 'Platinum - Unlimited',
                'description' => 'Paket unlimited tanpa batas kunjungan. Termasuk semua fasilitas, kelas grup, dan 2x konsultasi personal trainer.',
                'max_attendance_qty' => null,
                'duration_days' => 30,
                'price' => 800000,
                'is_active' => true,
            ],
            [
                'name' => 'Student Pass',
                'description' => 'Paket khusus pelajar dan mahasiswa dengan harga terjangkau. Wajib menunjukkan kartu mahasiswa.',
                'max_attendance_qty' => 10,
                'duration_days' => 30,
                'price' => 175000,
                'is_active' => true,
            ],
            [
                'name' => 'Weekend Warrior',
                'description' => 'Paket khusus akhir pekan (Sabtu-Minggu). Cocok untuk yang sibuk di hari kerja.',
                'max_attendance_qty' => 8,
                'duration_days' => 30,
                'price' => 150000,
                'is_active' => true,
            ],
            [
                'name' => 'Corporate Partner',
                'description' => 'Paket kerjasama untuk karyawan perusahaan mitra. Harga spesial dengan benefit tambahan.',
                'max_attendance_qty' => 16,
                'duration_days' => 30,
                'price' => 400000,
                'is_active' => true,
            ],
            [
                'name' => 'Annual Gold',
                'description' => 'Paket Gold tahunan dengan diskon 20%. Hemat Rp 1.200.000 per tahun.',
                'max_attendance_qty' => 20,
                'duration_days' => 365,
                'price' => 4800000,
                'is_active' => true,
            ],
            [
                'name' => 'Trial Pass',
                'description' => 'Paket percobaan untuk calon member baru. Berlaku 7 hari.',
                'max_attendance_qty' => 3,
                'duration_days' => 7,
                'price' => 50000,
                'is_active' => true,
            ],
            [
                'name' => 'Legacy - Silver Classic',
                'description' => 'Paket lama yang sudah tidak dijual. Hanya untuk perpanjangan member existing.',
                'max_attendance_qty' => 10,
                'duration_days' => 30,
                'price' => 300000,
                'is_active' => false,
            ],
        ];

        foreach ($memberships as $membership) {
            Membership::create($membership);
        }
    }

    /**
     * Seed realistic Indonesian members.
     */
    private function seedMembers(): array
    {
        $members = [
            // Active gym enthusiasts
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '81234567890'], 'address' => 'Jl. Sudirman No. 45, Jakarta Pusat'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@yahoo.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '82345678901'], 'address' => 'Jl. Gatot Subroto No. 12, Jakarta Selatan'],
            ['name' => 'Andi Wijaya', 'email' => 'andi.wijaya@outlook.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '83456789012'], 'address' => 'Jl. HR Rasuna Said Kav. 5, Jakarta Selatan'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.lestari@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '84567890123'], 'address' => 'Jl. Kemang Raya No. 88, Jakarta Selatan'],
            ['name' => 'Rudi Hartono', 'email' => 'rudi.hartono@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '85678901234'], 'address' => 'Jl. Pluit Karang No. 21, Jakarta Utara'],

            // Regular members
            ['name' => 'Maya Putri', 'email' => 'maya.putri@hotmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '86789012345'], 'address' => 'Jl. Kelapa Gading Boulevard, Jakarta Utara'],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '87890123456'], 'address' => 'Jl. Pramuka No. 55, Jakarta Timur'],
            ['name' => 'Linda Susanti', 'email' => 'linda.susanti@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '88901234567'], 'address' => 'Jl. Cikini Raya No. 30, Jakarta Pusat'],
            ['name' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@yahoo.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '89012345678'], 'address' => 'Jl. Wolter Monginsidi No. 18, Jakarta Selatan'],
            ['name' => 'Ratna Sari', 'email' => 'ratna.sari@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '81123456789'], 'address' => 'Jl. Tebet Raya No. 99, Jakarta Selatan'],

            // Students
            ['name' => 'Dimas Pratama', 'email' => 'dimas.pratama@student.ui.ac.id', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '82234567890'], 'address' => 'Kost Margonda No. 15, Depok'],
            ['name' => 'Anisa Rahma', 'email' => 'anisa.rahma@student.itb.ac.id', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '83345678901'], 'address' => 'Kost Dago Atas No. 8, Bandung'],

            // Corporate members
            ['name' => 'Yoga Permana', 'email' => 'yoga.permana@tokopedia.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '84456789012'], 'address' => 'Tokopedia Tower, Jakarta Selatan'],
            ['name' => 'Fitri Handayani', 'email' => 'fitri.handayani@gojek.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '85567890123'], 'address' => 'Pasaraya Blok M, Jakarta Selatan'],
            ['name' => 'Rizky Fadillah', 'email' => 'rizky.fadillah@grab.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '86678901234'], 'address' => 'Lippo Kuningan, Jakarta Selatan'],

            // Weekend warriors
            ['name' => 'Tono Sucipto', 'email' => 'tono.sucipto@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '87789012345'], 'address' => 'Jl. Pondok Indah No. 77, Jakarta Selatan'],
            ['name' => 'Wulan Maharani', 'email' => 'wulan.maharani@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '88890123456'], 'address' => 'Jl. Pantai Indah Kapuk, Jakarta Utara'],

            // Casual/Trial members
            ['name' => 'Bagus Setiawan', 'email' => 'bagus.setiawan@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '89901234567'], 'address' => 'Jl. Menteng Raya No. 40, Jakarta Pusat'],
            ['name' => 'Citra Dewi', 'email' => 'citra.dewi@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '81012345678'], 'address' => 'Jl. Senopati No. 25, Jakarta Selatan'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar.nugroho@gmail.com', 'phone' => ['country' => ['country' => 'ID', 'countryName' => 'Indonesia', 'code' => '+62'], 'number' => '82123456789'], 'address' => 'Jl. Cilandak Tengah No. 60, Jakarta Selatan'],
        ];

        $createdMembers = [];
        foreach ($members as $member) {
            $createdMembers[] = Member::create($member);
        }

        return $createdMembers;
    }

    /**
     * Seed member memberships with various scenarios.
     */
    private function seedMemberMemberships(array $members): void
    {
        $memberships = Membership::all()->keyBy('name');

        // Active memberships with various packages
        $this->assignMembership($members[0], $memberships['Gold - Premium'], now()->subDays(10)); // Active Gold
        $this->assignMembership($members[1], $memberships['Platinum - Unlimited'], now()->subDays(5)); // Active Unlimited
        $this->assignMembership($members[2], $memberships['Silver - Regular'], now()->subDays(15)); // Active Silver
        $this->assignMembership($members[3], $memberships['Gold - Premium'], now()->subDays(3)); // Active Gold (new)
        $this->assignMembership($members[4], $memberships['Platinum - Unlimited'], now()->subDays(20)); // Active Unlimited

        // Regular members
        $this->assignMembership($members[5], $memberships['Silver - Regular'], now()->subDays(7));
        $this->assignMembership($members[6], $memberships['Bronze - Pemula'], now()->subDays(12));
        $this->assignMembership($members[7], $memberships['Silver - Regular'], now()->subDays(8));
        $this->assignMembership($members[8], $memberships['Gold - Premium'], now()->subDays(18));
        $this->assignMembership($members[9], $memberships['Silver - Regular'], now()->subDays(4));

        // Students
        $this->assignMembership($members[10], $memberships['Student Pass'], now()->subDays(14));
        $this->assignMembership($members[11], $memberships['Student Pass'], now()->subDays(20));

        // Corporate members
        $this->assignMembership($members[12], $memberships['Corporate Partner'], now()->subDays(6));
        $this->assignMembership($members[13], $memberships['Corporate Partner'], now()->subDays(10));
        $this->assignMembership($members[14], $memberships['Corporate Partner'], now()->subDays(2));

        // Weekend warriors
        $this->assignMembership($members[15], $memberships['Weekend Warrior'], now()->subDays(9));
        $this->assignMembership($members[16], $memberships['Weekend Warrior'], now()->subDays(16));

        // Trial members
        $this->assignMembership($members[17], $memberships['Trial Pass'], now()->subDays(2));
        $this->assignMembership($members[18], $memberships['Trial Pass'], now()->subDays(5));

        // New trial (just started)
        $this->assignMembership($members[19], $memberships['Trial Pass'], now());

        // Expired memberships (untuk history)
        $expiredMembership1 = $this->assignMembership($members[0], $memberships['Silver - Regular'], now()->subDays(60), true);
        $expiredMembership2 = $this->assignMembership($members[1], $memberships['Gold - Premium'], now()->subDays(45), true);
        $expiredMembership3 = $this->assignMembership($members[2], $memberships['Bronze - Pemula'], now()->subDays(90), true);

        // Almost expired (untuk testing warning)
        $this->assignMembership($members[5], $memberships['Bronze - Pemula'], now()->subDays(28), false, true);

        // Fully used membership (semua kuota terpakai tapi masih aktif)
        $fullyUsedMembership = $this->createFullyUsedMembership($members[6], $memberships['Bronze - Pemula']);
    }

    /**
     * Helper to assign membership with realistic snapshots.
     */
    private function assignMembership(
        Member $member,
        Membership $membership,
        $startedAt,
        bool $isExpired = false,
        bool $isAlmostExpired = false
    ): MemberMembership {
        $startedAt = is_string($startedAt) ? now()->parse($startedAt) : $startedAt;

        if ($isExpired) {
            $expiredAt = $startedAt->copy()->addDays($membership->duration_days);
            $status = 'expired';
        } elseif ($isAlmostExpired) {
            $expiredAt = now()->addDays(2);
            $status = 'active';
        } else {
            $expiredAt = $startedAt->copy()->addDays($membership->duration_days);
            $status = 'active';
        }

        $memberMembership = MemberMembership::create([
            'member_id' => $member->id,
            'membership_id' => $membership->id,
            'snapshot_membership_name' => $membership->name,
            'snapshot_max_attendance_qty' => $membership->max_attendance_qty,
            'snapshot_duration_days' => $membership->duration_days,
            'snapshot_price' => $membership->price,
            'started_at' => $startedAt,
            'expired_at' => $expiredAt,
            'status' => $status,
        ]);

        // Generate realistic attendances
        if (!$isExpired) {
            $this->generateAttendances($memberMembership, $member);
        } else {
            // Expired memberships have complete attendance history
            $this->generateExpiredAttendances($memberMembership, $member);
        }

        return $memberMembership;
    }

    /**
     * Generate realistic attendance records for active memberships.
     */
    private function generateAttendances(MemberMembership $mm, Member $member): void
    {
        $maxQty = $mm->snapshot_max_attendance_qty;
        $daysSinceStart = now()->diffInDays($mm->started_at);

        // Simulate realistic attendance patterns
        if ($maxQty === null) {
            // Unlimited: heavy users visit frequently
            $attendanceCount = min($daysSinceStart, rand(intval($daysSinceStart * 0.5), $daysSinceStart));
        } else {
            // Limited: use proportionally based on days elapsed
            $progress = min(1, $daysSinceStart / $mm->snapshot_duration_days);
            $attendanceCount = min($maxQty - 1, intval($maxQty * $progress * rand(60, 100) / 100));
        }

        $usedDates = [];
        for ($i = 0; $i < $attendanceCount; $i++) {
            // Random day within the membership period
            $randomDays = rand(0, $daysSinceStart);
            $date = $mm->started_at->copy()->addDays($randomDays);

            // Avoid duplicate dates
            $dateKey = $date->format('Y-m-d');
            if (isset($usedDates[$dateKey])) continue;
            $usedDates[$dateKey] = true;

            // Realistic check-in times
            $checkInHour = $this->getRandomCheckInHour();
            $checkInAt = $date->setTime($checkInHour, rand(0, 59));

            // Session duration 45-120 minutes
            $duration = rand(45, 120);
            $checkOutAt = $checkInAt->copy()->addMinutes($duration);

            // Calculate remaining at that point
            $remainingBefore = $maxQty === null ? null : $maxQty - $i;

            Attendance::create([
                'member_membership_id' => $mm->id,
                'snapshot_member_name' => $member->name,
                'snapshot_membership_name' => $mm->snapshot_membership_name,
                'snapshot_remaining_before' => $remainingBefore,
                'check_in_at' => $checkInAt,
                'check_out_at' => $checkOutAt,
                'notes' => $this->getRandomNote(),
            ]);
        }
    }

    /**
     * Generate complete attendance history for expired memberships.
     */
    private function generateExpiredAttendances(MemberMembership $mm, Member $member): void
    {
        $maxQty = $mm->snapshot_max_attendance_qty ?? 15;
        // Use about 70-100% of the quota
        $attendanceCount = intval($maxQty * rand(70, 100) / 100);

        $periodDays = $mm->started_at->diffInDays($mm->expired_at);
        $usedDates = [];

        for ($i = 0; $i < $attendanceCount; $i++) {
            $randomDays = rand(0, $periodDays);
            $date = $mm->started_at->copy()->addDays($randomDays);

            $dateKey = $date->format('Y-m-d');
            if (isset($usedDates[$dateKey])) continue;
            $usedDates[$dateKey] = true;

            $checkInHour = $this->getRandomCheckInHour();
            $checkInAt = $date->setTime($checkInHour, rand(0, 59));
            $duration = rand(45, 120);
            $checkOutAt = $checkInAt->copy()->addMinutes($duration);

            $remainingBefore = $mm->snapshot_max_attendance_qty === null
                ? null
                : $mm->snapshot_max_attendance_qty - $i;

            Attendance::create([
                'member_membership_id' => $mm->id,
                'snapshot_member_name' => $member->name,
                'snapshot_membership_name' => $mm->snapshot_membership_name,
                'snapshot_remaining_before' => $remainingBefore,
                'check_in_at' => $checkInAt,
                'check_out_at' => $checkOutAt,
                'notes' => $this->getRandomNote(),
            ]);
        }
    }

    /**
     * Create a membership with all quota used (but still active).
     */
    private function createFullyUsedMembership(Member $member, Membership $membership): MemberMembership
    {
        $startedAt = now()->subDays(25);
        $expiredAt = $startedAt->copy()->addDays($membership->duration_days);
        $maxQty = $membership->max_attendance_qty;

        $mm = MemberMembership::create([
            'member_id' => $member->id,
            'membership_id' => $membership->id,
            'snapshot_membership_name' => $membership->name,
            'snapshot_max_attendance_qty' => $maxQty,
            'snapshot_duration_days' => $membership->duration_days,
            'snapshot_price' => $membership->price,
            'started_at' => $startedAt,
            'expired_at' => $expiredAt,
            'status' => 'active',
        ]);

        // Create exactly max_qty attendances
        for ($i = 0; $i < $maxQty; $i++) {
            $randomDays = intval($i * (25 / $maxQty));
            $date = $startedAt->copy()->addDays($randomDays);
            $checkInHour = $this->getRandomCheckInHour();
            $checkInAt = $date->setTime($checkInHour, rand(0, 59));
            $duration = rand(45, 120);
            $checkOutAt = $checkInAt->copy()->addMinutes($duration);

            Attendance::create([
                'member_membership_id' => $mm->id,
                'snapshot_member_name' => $member->name,
                'snapshot_membership_name' => $mm->snapshot_membership_name,
                'snapshot_remaining_before' => $maxQty - $i,
                'check_in_at' => $checkInAt,
                'check_out_at' => $checkOutAt,
                'notes' => null,
            ]);
        }

        return $mm;
    }

    /**
     * Get realistic check-in hour based on gym peak times.
     */
    private function getRandomCheckInHour(): int
    {
        $weights = [
            6 => 10,  // 6 AM - early birds
            7 => 15,  // 7 AM - morning rush
            8 => 12,  // 8 AM - still busy
            9 => 8,   // 9 AM
            10 => 5,  // 10 AM - quiet
            11 => 4,  // 11 AM - quiet
            12 => 6,  // 12 PM - lunch crowd
            13 => 5,  // 1 PM
            14 => 4,  // 2 PM - quietest
            15 => 5,  // 3 PM
            16 => 8,  // 4 PM - after work starts
            17 => 15, // 5 PM - peak
            18 => 18, // 6 PM - busiest
            19 => 15, // 7 PM - still very busy
            20 => 10, // 8 PM - evening
            21 => 5,  // 9 PM - winding down
        ];

        $total = array_sum($weights);
        $random = rand(1, $total);
        $cumulative = 0;

        foreach ($weights as $hour => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $hour;
            }
        }

        return 18; // Default to 6 PM
    }

    /**
     * Get random workout notes.
     */
    private function getRandomNote(): ?string
    {
        $notes = [
            null,
            null,
            null,
            null,
            null, // 50% no notes
            'Fokus upper body hari ini',
            'Leg day 💪',
            'Cardio 30 menit + core',
            'Personal training session',
            'Recovery day - light workout',
            'HIIT circuit',
            'Chest & triceps',
            'Back & biceps',
            'Full body workout',
            'Spinning class',
            'Yoga session',
            'Boxing training',
            'CrossFit WOD',
            'Stretching & mobility',
            'Agak capek, workout ringan aja',
        ];

        return $notes[array_rand($notes)];
    }
}
