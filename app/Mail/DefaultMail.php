<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class DefaultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "";
    public $greeting = "";
    public $body = "";
    public $salutation = "";
    public $headingtable = "";
    public $bodytable = "";

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $greeting, $body, $attachment = null)
    {
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->body = $body;
        $this->attachment = $attachment;
        // $this->headingtable = $headingtable;
        // $this->bodytable = $bodytable;
    }

    public function build()
    {
        $subject = $this->subject;
        $greeting = $this->greeting;
        $body = $this->body;
        $salutation = $this->salutation;
        $attachment = $this->attachment;

        $email = $this->view('emails.defaultemail')
        ->with(compact('subject', 'greeting', 'body'));

        if (!empty($this->attachment)) {
            // Full correct path for attachment in `storage/app/public/images/user_emails`
            $fullPath = storage_path('app/public/images/user_emails/' . $this->attachment);
    
            \Log::channel('emaillog')->info('Trying to attach file from: ' . $fullPath);
    
            if (file_exists($fullPath)) {
                $email->attach($fullPath);
                \Log::channel('emaillog')->info('Attachment attached successfully.');
            } else {
                \Log::channel('emaillog')->warning('Attachment file not found at: ' . $fullPath);
            }
        }
    
        return $email;
        // $headingtable = $this->headingtable;
        // $bodytable = $this->bodytable;
        // return $this->view('emails.defaultemail')
        // ->with(compact('subject', 'greeting', 'body'));
        //public_path('path_to_file2.png')
        // return $this->view(
        //     'emails.defaultemail',
        //     compact('subject', 'greeting', 'body', 'headingtable', 'bodytable'));
    }
}
