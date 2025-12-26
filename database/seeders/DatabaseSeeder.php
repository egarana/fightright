<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DummySeeder::class,
        ]);

        $user = User::factory()
            ->withoutTwoFactor()
            ->create([
                'name' => 'Ega Rana Bimansa',
                'email' => 'bimansaegarana@gmail.com',
                'password' => Hash::make('Letdareca1#8'),
            ]);

        $user->assignRole('super-admin');

        $owner = User::factory()
            ->withoutTwoFactor()
            ->create([
                'name' => 'Gungde Gotama',
                'email' => 'gungdegotama@frgym.com',
                'password' => Hash::make('frgym1234'),
            ]);

        $owner->assignRole('owner');

        $manager = User::factory()
            ->withoutTwoFactor()
            ->create([
                'name' => 'Made Suartana',
                'email' => 'madesuartana@frgym.com',
                'password' => Hash::make('frgym1234'),
            ]);

        $manager->assignRole('manager');

        $staff = User::factory()
            ->withoutTwoFactor()
            ->create([
                'name' => 'Wayan Kadek',
                'email' => 'wayankadek@frgym.com',
                'password' => Hash::make('frgym1234'),
            ]);

        $staff->assignRole('staff');
    }
}
