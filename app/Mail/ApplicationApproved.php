<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $applicationType;

    /**
     * Create a new message instance.
     */
    public function __construct($application, $applicationType)
    {
        $this->application = $application;
        $this->applicationType = $applicationType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Approved - ' . $this->getApplicationTitle(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-approved',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get the application title based on type
     */
    private function getApplicationTitle(): string
    {
        switch ($this->applicationType) {
            case 'seedling':
                return 'Seedling Request';
            case 'rsbsa':
                return 'RSBSA Registration';
            case 'fishr':
                return 'FishR Registration';
            case 'boatr':
                return 'BoatR Registration';
            default:
                return 'Application';
        }
    }
}
