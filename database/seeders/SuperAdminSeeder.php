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
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'agrisys0@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
        ]);

        // Create Regular Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@agrisys.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create Regular User
        User::create([
            'name' => 'Regular User',
            'email' => 'user@agrisys.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $this->command->info('Default users created successfully!');
        $this->command->info('Super Admin: superadmin@agrisys.com / password123');
        $this->command->info('Admin: admin@agrisys.com / password123');
        $this->command->info('User: user@agrisys.com / password123');
    }
}
