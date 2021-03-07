<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable //implements ShouldQueue ##Queueing By Default
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $return_array;
    public function __construct($return_array)
    {
        $this->return_array = $return_array;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->markdown('email.welcome', ['data' => $return_array] );
        return $this->from('from_email@gmail.com', 'coder aweso.me')->subject('Welcome new user!')->view('email.welcome', ['data' => $this->return_array]); //->attach('/path/to/file') 
    }
}
