<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\UserRegistration;

class UserRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'juan_dela_cruz',
                'contact_number' => '09171234501',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'maria_santos',
                'contact_number' => '09171234502',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'pedro_reyes',
                'contact_number' => '09171234503',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'rosa_garcia',
                'contact_number' => '09171234504',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'andres_lim',
                'contact_number' => '09171234505',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'jerald',
                'contact_number' => '09171234506',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'jasper',
                'contact_number' => '09171234507',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'arvy',
                'contact_number' => '09171234508',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'user',
                'contact_number' => '09171234509',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
            [
                'username' => 'lex',
                'contact_number' => '09217861710',
                'password' => 'password123',
                'status' => UserRegistration::STATUS_UNVERIFIED,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ],
        ];

        foreach ($users as $user) {
            UserRegistration::create($user);
        }

        $this->command->info('10 unverified users seeded successfully.');
    }
}