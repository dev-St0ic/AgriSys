<?php

namespace App\Traits;

use App\Events\ApplicationStatusChanged;

trait SendsApplicationSms
{
    /**
     * Fire application status changed event for SMS notification
     *
     * @param string $applicationType
     * @param string $previousStatus
     * @param string $newStatus
     * @param string|null $reason
     * @param string|null $phoneNumber
     * @param string|null $applicantName
     * @return void
     */
    public function fireApplicationStatusChanged(
        string $applicationType,
        string $previousStatus,
        string $newStatus,
        ?string $reason = null,
        ?string $phoneNumber = null,
        ?string $applicantName = null
    ): void {
        // Auto-detect phone and name if not provided
        if (is_null($phoneNumber)) {
            $phoneNumber = $this->getApplicantPhone();
        }

        if (is_null($applicantName)) {
            $applicantName = $this->getApplicantName();
        }

        // Only fire if we have required data and status actually changed
        if ($previousStatus !== $newStatus && !empty($phoneNumber)) {
            \Log::info('Firing ApplicationStatusChanged event', [
                'application_id' => $this->id ?? 'unknown',
                'application_type' => $applicationType,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'phone' => $phoneNumber,
                'timestamp' => now()->toDateTimeString()
            ]);

            event(new ApplicationStatusChanged(
                $this,
                $applicationType,
                $previousStatus,
                $newStatus,
                $reason,
                $phoneNumber,
                $applicantName
            ));
        }
    }

    /**
     * Get applicant phone number - to be implemented by models using this trait
     *
     * @return string|null
     */
    abstract protected function getApplicantPhone(): ?string;

    /**
     * Get applicant name - to be implemented by models using this trait
     *
     * @return string|null
     */
    abstract protected function getApplicantName(): ?string;

    /**
     * Get application type name - to be implemented by models using this trait
     *
     * @return string
     */
    abstract protected function getApplicationTypeName(): string;

    /**
     * Helper method to update status and send SMS
     *
     * @param string $newStatus
     * @param string|null $reason
     * @return void
     */
    public function updateStatusWithNotification(string $newStatus, ?string $reason = null): void
    {
        $previousStatus = $this->status ?? 'pending';

        // Update the status
        $this->update(['status' => $newStatus]);

        // Send SMS notification
        $this->fireApplicationStatusChanged(
            $this->getApplicationTypeName(),
            $previousStatus,
            $newStatus,
            $reason
        );
    }

    /**
     * Approve application with SMS notification
     *
     * @param string|null $reason
     * @return void
     */
    public function approveWithNotification(?string $reason = null): void
    {
        $this->updateStatusWithNotification('approved', $reason);
    }

    /**
     * Reject application with SMS notification
     *
     * @param string|null $reason
     * @return void
     */
    public function rejectWithNotification(?string $reason = null): void
    {
        $this->updateStatusWithNotification('rejected', $reason);
    }
}
