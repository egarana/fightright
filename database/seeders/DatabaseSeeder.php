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
                'name' => 'Ega Rana',
                'email' => 'bimansaegarana@gmail.com',
                'password' => Hash::make('Letdareca1#8'),
            ]);

        $user->assignRole('super-admin');
    }
}
