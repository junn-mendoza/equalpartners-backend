<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class invitation extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientName;
    public $playStoreLink;
    public $appStoreLink;

    /**
     * Create a new message instance.
     *
     * @param string $recipientName
     * @param string $playStoreLink
     * @param string $appStoreLink
     */
    public function __construct($recipientName, $playStoreLink, $appStoreLink)
    {
        $this->recipientName = $recipientName;
        $this->playStoreLink = $playStoreLink;
        $this->appStoreLink = $appStoreLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation to Join Equal Partners',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.invite', // Update with the correct path to the email template
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
}
