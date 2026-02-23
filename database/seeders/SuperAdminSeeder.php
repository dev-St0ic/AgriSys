<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin (no email verification needed - bypassed by role)
        User::updateOrCreate(
            ['role' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'reyes05jerald@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
            ]
        );

        // Create Regular Admin
        User::updateOrCreate(
            ['role' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'agrisys0@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Default users created successfully!');
        $this->command->info('Super Admin: reyes05jerald@gmail.com / password123');
        $this->command->info('Admin: agrisys0@gmail.com / password123');
    }
}
