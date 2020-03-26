<?php

namespace App\Modules\Event\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Modules\Event\Models\Registration;

class EventRegistered extends Mailable
{
    use Queueable, SerializesModels;

    private $registration;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(option('from_email'), option('from_name'))
                ->subject('Registration for '.$this->registration->event->name)
                ->markdown('emails.event.registered')
                ->with('registration', $this->registration);
    }
}
