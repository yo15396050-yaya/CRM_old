<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmationTicket extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticketData;
    public $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct($ticketData, $pdf)
    {
        $this->ticketData = $ticketData;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🎫 Votre ticket de formation - ' . $this->ticketData['ticket_number'])
                    ->view('emails.confirmation_ticket')
                    ->with($this->ticketData)
                    ->attachData(
                        $this->pdf->output(), 
                        'ticket-' . $this->ticketData['ticket_number'] . '.pdf',
                        [
                            'mime' => 'application/pdf',
                        ]
                    );
    }
}