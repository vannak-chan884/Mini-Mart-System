<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'vannakchan884@gmail.com'],
            [
                'name'              => 'Administrator',
                'role'              => 'admin',
                'password'          => Hash::make('VannakChan@*6240'),
                'email_verified_at' => now(),
            ]
        );
    }
}