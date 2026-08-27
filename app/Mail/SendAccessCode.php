<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendAccessCode extends Mailable
{
    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Kode Akses Event')
                    ->view('emails.access-code');
    }
}