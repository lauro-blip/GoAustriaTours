<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnquiryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New enquiry: '.$this->enquiry->full_name.' ('.$this->enquiry->form_name.')',
            replyTo: [$this->enquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-enquiry');
    }
}
