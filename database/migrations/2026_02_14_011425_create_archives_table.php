<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ISO 15489 Compliant Archive Table
     * Records Management Standard for Government Systems
     */
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();

            // Record Identity (ISO 15489: Authenticity)
            $table->string('archive_reference_number')->unique(); // e.g. ARCH-2024-00001
            $table->string('model_type');                          // Original model class
            $table->unsignedBigInteger('model_id');               // Original record ID
            $table->string('item_name');                           // Human-readable name
            $table->string('record_type');                        // fishr, boatr, rsbsa, etc.

            // Record Content (ISO 15489: Integrity)
            $table->longText('data');                             // Full serialized record (JSON)
            $table->string('data_checksum');                      // SHA-256 hash for integrity verification
            $table->string('data_version')->default('1.0');       // Record version

            // Retention Policy (ISO 15489: Retention Schedule)
            $table->string('retention_category');                 // permanent, long_term, standard, short_term
            $table->integer('retention_years');                   // Number of years to retain
            $table->date('retention_expires_at')->nullable();     // When retention expires (null = permanent)
            $table->string('disposal_authority')->nullable();     // Legal/regulatory basis for disposal
            $table->text('retention_justification')->nullable();  // Why this retention period

            // Archive Metadata (ISO 15489: Context)
            $table->text('archive_reason')->nullable();           // Why archived
            $table->string('archive_source');                     // recycle_bin, direct, migration
            $table->string('classification')->default('internal'); // public, internal, confidential, restricted
            $table->json('tags')->nullable();                     // Searchable tags

            // Provenance (ISO 15489: Authenticity Chain)
            $table->unsignedBigInteger('archived_from_recycle_bin_id')->nullable(); // Source recycle bin ID
            $table->unsignedBigInteger('archived_by');            // Admin who archived
            $table->string('archived_by_ip')->nullable();         // IP address for audit trail
            $table->timestamp('archived_at');                     // When archived

            // Disposal Tracking (ISO 15489: Disposal)
            $table->string('disposal_status')->default('retained'); // retained, approved_for_disposal, disposed
            $table->unsignedBigInteger('disposal_approved_by')->nullable();
            $table->timestamp('disposal_approved_at')->nullable();
            $table->text('disposal_notes')->nullable();
            $table->unsignedBigInteger('disposed_by')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->string('disposed_by_ip')->nullable();

            // Immutability Lock (SuperAdmin Only)
            $table->boolean('is_locked')->default(true);          // Locked by default - cannot be edited
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->text('lock_reason')->nullable();

            // Access Control
            $table->boolean('is_restricted')->default(false);     // Extra restricted access
            $table->json('allowed_roles')->nullable();             // Roles that can view

            // Audit trail
            $table->timestamps();

            // Indexes
            $table->index(['model_type', 'model_id']);
            $table->index('archive_reference_number');
            $table->index('retention_category');
            $table->index('disposal_status');
            $table->index('archived_at');
            $table->index('retention_expires_at');
            $table->index('record_type');

            // Foreign keys
            $table->foreign('archived_by')->references('id')->on('users');
            $table->foreign('disposal_approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('disposed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('locked_by')->references('id')->on('users')->nullOnDelete();
        });

        // Archive Audit Log - Immutable log of all actions on archives
        Schema::create('archive_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('archive_id');
            $table->string('action');                             // viewed, exported, disposal_approved, disposed, unlocked
            $table->unsignedBigInteger('performed_by');
            $table->string('performed_by_ip')->nullable();
            $table->string('performed_by_role')->nullable();
            $table->json('metadata')->nullable();                 // Extra context
            $table->timestamp('performed_at');

            $table->index('archive_id');
            $table->index('performed_by');
            $table->index('performed_at');

            $table->foreign('archive_id')->references('id')->on('archives')->cascadeOnDelete();
            $table->foreign('performed_by')->references('id')->on('users');
        });

        // Retention Schedules - Configurable per record type
        Schema::create('retention_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('record_type')->unique();              // fishr, boatr, rsbsa, etc.
            $table->string('record_label');                       // Human-readable label
            $table->string('retention_category');                 // permanent, long_term, standard, short_term
            $table->integer('retention_years');
            $table->string('legal_basis')->nullable();            // RA XXXX, Executive Order, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archive_audit_logs');
        Schema::dropIfExists('archives');
        Schema::dropIfExists('retention_schedules');
    }
};