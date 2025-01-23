<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct($code, $code_expire_time)
    {
        $this->code = $code;
        $this->code_expire_time = $code_expire_time;
    }

    public function build()
    {
        return $this->subject('Your Verification Code')
                    ->view('emails.mail_verification_code')
                    ->with([
                        'code' => $this->code,
                        'code_expire_time' => $this->code_expire_time]);
    }
}
