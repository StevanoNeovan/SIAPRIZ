<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AdminVerifiedMail extends Mailable
{
    public function build()
    {
        return $this->subject('Akun SIAPRIZ Anda Aktif')
            ->markdown('emails.admin-verified');
    }
}
