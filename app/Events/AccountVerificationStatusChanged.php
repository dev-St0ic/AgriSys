<?php

namespace App\Events;

use App\Models\UserRegistration;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountVerificationStatusChanged
{
    use Dispatchable, SerializesModels;

    public UserRegistration $userRegistration;
    public string $previousStatus;
    public string $newStatus;
    public ?string $reason;

    /**
     * Create a new event instance.
     *
     * @param UserRegistration $userRegistration
     * @param string $previousStatus
     * @param string $newStatus
     * @param string|null $reason
     */
    public function __construct(UserRegistration $userRegistration, string $previousStatus, string $newStatus, ?string $reason = null)
    {
        $this->userRegistration = $userRegistration;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->reason = $reason;
    }
}
