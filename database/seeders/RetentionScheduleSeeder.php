<?php

namespace Database\Seeders;

use App\Models\RetentionSchedule;
use App\Models\Archive;
use Illuminate\Database\Seeder;

class RetentionScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = [
            [
                'record_type'       => 'rsbsa',
                'record_label'      => 'RSBSA Registrations',
                'retention_category'=> Archive::RETENTION_PERMANENT,
                'retention_years'   => 0,
                'legal_basis'       => 'RA 8435 (Agriculture and Fisheries Modernization Act), RSBSA Administrative Order',
                'description'       => 'Registry of Basic Sectors in Agriculture - Permanent due to government property rights, 4Ps, subsidies, and insurance program linkage.',
            ],
            [
                'record_type'       => 'fishr',
                'record_label'      => 'FishR Registrations',
                'retention_category'=> Archive::RETENTION_LONG_TERM,
                'retention_years'   => 25,
                'legal_basis'       => 'RA 8550 (Philippine Fisheries Code of 1998), RA 10654 (Amendment)',
                'description'       => 'Fisheries registration records required for regulatory compliance and historical fisheries data.',
            ],
            [
                'record_type'       => 'fishr_annex',
                'record_label'      => 'FishR Annexes',
                'retention_category'=> Archive::RETENTION_LONG_TERM,
                'retention_years'   => 25,
                'legal_basis'       => 'RA 8550 (Philippine Fisheries Code of 1998)',
                'description'       => 'Supporting annexes for FishR registrations.',
            ],
            [
                'record_type'       => 'boatr',
                'record_label'      => 'BoatR Registrations',
                'retention_category'=> Archive::RETENTION_LONG_TERM,
                'retention_years'   => 25,
                'legal_basis'       => 'RA 8550 (Philippine Fisheries Code), MARINA Regulations',
                'description'       => 'Boat registration records for municipal fisheries vessels.',
            ],
            [
                'record_type'       => 'boatr_annex',
                'record_label'      => 'BoatR Annexes',
                'retention_category'=> Archive::RETENTION_LONG_TERM,
                'retention_years'   => 25,
                'legal_basis'       => 'RA 8550 (Philippine Fisheries Code of 1998)',
                'description'       => 'Supporting annexes for BoatR registrations.',
            ],
            [
                'record_type'       => 'training',
                'record_label'      => 'Training Requests',
                'retention_category'=> Archive::RETENTION_STANDARD,
                'retention_years'   => 10,
                'legal_basis'       => 'CSC MC No. 6, s. 2017 (Records Retention for LGUs)',
                'description'       => 'Agricultural training and capacity-building records.',
            ],
            [
                'record_type'       => 'seedlings',
                'record_label'      => 'Supply/Seedling Requests',
                'retention_category'=> Archive::RETENTION_STANDARD,
                'retention_years'   => 10,
                'legal_basis'       => 'COA Circular 2009-006 (Government Accounting Manual, Vol. 1)',
                'description'       => 'Supply and seedling distribution records, required for COA audit purposes.',
            ],
            [
                'record_type'       => 'user_registration',
                'record_label'      => 'User Registrations',
                'retention_category'=> Archive::RETENTION_STANDARD,
                'retention_years'   => 7,
                'legal_basis'       => 'RA 10173 (Data Privacy Act of 2012), NPC Circular 16-01',
                'description'       => 'User account and registration data. Subject to Data Privacy Act compliance.',
            ],
            [
                'record_type'        => 'admin_user',
                'record_label'       => 'Admin User',
                'retention_category' => 'standard',
                'retention_years'    => 10,
                'legal_basis'        => 'COA Circular 2009-006',
                'description'        => 'Admin account records retained for audit and accountability purposes.',
            ],
            [
                'record_type'       => 'category_item',
                'record_label'      => 'Supply Items',
                'retention_category'=> Archive::RETENTION_SHORT_TERM,
                'retention_years'   => 5,
                'legal_basis'       => 'LGU Internal Records Management Policy',
                'description'       => 'Supply item catalog records.',
            ],
            [
                'record_type'       => 'request_category',
                'record_label'      => 'Supply Categories',
                'retention_category'=> Archive::RETENTION_SHORT_TERM,
                'retention_years'   => 5,
                'legal_basis'       => 'LGU Internal Records Management Policy',
                'description'       => 'Supply category configuration records.',
            ],
        ];

        foreach ($schedules as $schedule) {
            RetentionSchedule::updateOrCreate(
                ['record_type' => $schedule['record_type']],
                $schedule
            );
        }

        $this->command->info('Retention schedules seeded successfully (ISO 15489 compliant)');
    }
}