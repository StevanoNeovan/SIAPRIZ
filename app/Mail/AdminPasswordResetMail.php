<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pengguna;

class AdminPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;
    public $resetUrl;

    public function __construct($token, Pengguna $user)
    {
        $this->token = $token;
        $this->user = $user;
        $this->resetUrl = route('admin.password.reset.form', ['token' => $token]);
    }

    public function build()
    {
        return $this->subject('Reset Password - SIAPRIZ Administrator')
                    ->view('auth.admin-password-reset');
    }
}