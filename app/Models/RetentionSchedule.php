<?php
// app/Models/RetentionSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetentionSchedule extends Model
{
    protected $fillable = [
        'record_type',
        'record_label',
        'retention_category',
        'retention_years',
        'legal_basis',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function forType(string $recordType): ?self
    {
        return static::where('record_type', $recordType)->where('is_active', true)->first();
    }

    public function calculateExpiryDate(): ?string
    {
        if ($this->retention_category === Archive::RETENTION_PERMANENT) {
            return null; // Never expires
        }
        return now()->addYears($this->retention_years)->toDateString();
    }
}

// -------------------------------------------------------------------
// database/seeders/RetentionScheduleSeeder.php
// -------------------------------------------------------------------
// Run: php artisan db:seed --class=RetentionScheduleSeeder
// -------------------------------------------------------------------

namespace Database\Seeders;

use App\Models\RetentionSchedule;
use App\Models\Archive;
use Illuminate\Database\Seeder;

class RetentionScheduleSeeder extends Seeder
{
    /**
     * Government ISO 15489 Retention Schedules
     * Based on Philippine National Archives guidelines and relevant RAs
     */
    public function run(): void
    {
        $schedules = [
            [
                'record_type'       => 'rsbsa',
                'record_label'      => 'RSBSA Registrations',
                'retention_category'=> Archive::RETENTION_PERMANENT,
                'retention_years'   => 0,
                'legal_basis'       => 'RA 10000 (Agri-Agra Reform Credit Act), Executive Order on RSBSA',
                'description'       => 'Registry of Basic Sectors in Agriculture - Permanent due to government property rights, 4Ps, subsidies, and insurance program linkage.',
            ],
            [
                'record_type'       => 'fishr',
                'record_label'      => 'FishR Registrations',
                'retention_category'=> Archive::RETENTION_LONG_TERM,
                'retention_years'   => 25,
                'legal_basis'       => 'RA 8550 (Philippine Fisheries Code), RA 10654',
                'description'       => 'Fisheries registration records required for regulatory compliance and historical fisheries data.',
            ],
            [
                'record_type'       => 'fishr_annex',
                'record_label'      => 'FishR Annexes',
                'retention_category'=> Archive::RETENTION_LONG_TERM,
                'retention_years'   => 25,
                'legal_basis'       => 'RA 8550 (Philippine Fisheries Code)',
                'description'       => 'Supporting annexes for FishR registrations.',
            ],
            [
                'record_type'       => 'boatr',
                'record_label'      => 'BoatR Registrations',
                'retention_category'=> Archive::RETENTION_LONG_TERM,
                'retention_years'   => 25,
                'legal_basis'       => 'RA 8550 (Philippine Fisheries Code), MARINA regulations',
                'description'       => 'Boat registration records for municipal fisheries vessels.',
            ],
            [
                'record_type'       => 'boatr_annex',
                'record_label'      => 'BoatR Annexes',
                'retention_category'=> Archive::RETENTION_LONG_TERM,
                'retention_years'   => 25,
                'legal_basis'       => 'RA 8550 (Philippine Fisheries Code)',
                'description'       => 'Supporting annexes for BoatR registrations.',
            ],
            [
                'record_type'       => 'training',
                'record_label'      => 'Training Requests',
                'retention_category'=> Archive::RETENTION_STANDARD,
                'retention_years'   => 10,
                'legal_basis'       => 'CSC Memorandum Circular on Training Records',
                'description'       => 'Agricultural training and capacity-building records.',
            ],
            [
                'record_type'       => 'seedlings',
                'record_label'      => 'Supply/Seedling Requests',
                'retention_category'=> Archive::RETENTION_STANDARD,
                'retention_years'   => 10,
                'legal_basis'       => 'COA Circular 2009-006 (Government Accounting Manual)',
                'description'       => 'Supply and seedling distribution records, required for COA audit purposes.',
            ],
            [
                'record_type'       => 'user_registration',
                'record_label'      => 'User Registrations',
                'retention_category'=> Archive::RETENTION_STANDARD,
                'retention_years'   => 7,
                'legal_basis'       => 'RA 10173 (Data Privacy Act), NPC Advisory',
                'description'       => 'User account and registration data. Subject to Data Privacy Act compliance.',
            ],
            [
                'record_type'       => 'category_item',
                'record_label'      => 'Supply Items',
                'retention_category'=> Archive::RETENTION_SHORT_TERM,
                'retention_years'   => 5,
                'legal_basis'       => 'Internal Records Management Policy',
                'description'       => 'Supply item catalog records.',
            ],
            [
                'record_type'       => 'request_category',
                'record_label'      => 'Supply Categories',
                'retention_category'=> Archive::RETENTION_SHORT_TERM,
                'retention_years'   => 5,
                'legal_basis'       => 'Internal Records Management Policy',
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