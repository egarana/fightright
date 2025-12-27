<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberIdCard extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct($member, $pdfContent)
    {
        $this->member = $member;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Fight Right Digital ID Card',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.member-id-card', // We might need a simple email view too
            with: ['member' => $this->member],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn() => $this->pdfContent,
                'Member-ID-Card.pdf'
            )
                ->withMime('application/pdf'),
        ];
    }
}
