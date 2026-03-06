<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;

    public function __construct($client)
    {
        //se a mensagem for no watshapp melhor para mim
        $this->client = $client;
    }

    public function build()
    {
        return $this->subject('Bem-vindo à WawaBusiness!')
                    ->view('emails.welcome');
    }
}
