<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class CeoAccountCreated extends Mailable
{
    public $ceo;
    public $password;

    public function __construct($ceo, $password)
    {
        $this->ceo = $ceo;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Akun CEO SIAPRIZ Anda')
            ->view('emails.ceo-created');
    }
}
