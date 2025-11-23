<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChanged
{
    use Dispatchable, SerializesModels;

    public $application;
    public string $applicationType;
    public string $previousStatus;
    public string $newStatus;
    public ?string $reason;
    public ?string $applicantPhone;
    public ?string $applicantName;

    /**
     * Create a new event instance.
     *
     * @param mixed $application - The application model instance
     * @param string $applicationType - Type of application (e.g., 'RSBSA', 'Seedling Request', etc.)
     * @param string $previousStatus
     * @param string $newStatus
     * @param string|null $reason
     * @param string|null $applicantPhone
     * @param string|null $applicantName
     */
    public function __construct($application, string $applicationType, string $previousStatus, string $newStatus, ?string $reason = null, ?string $applicantPhone = null, ?string $applicantName = null)
    {
        $this->application = $application;
        $this->applicationType = $applicationType;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->reason = $reason;
        $this->applicantPhone = $applicantPhone;
        $this->applicantName = $applicantName;
    }
}
