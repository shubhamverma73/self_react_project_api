<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreated extends Mailable
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
        return $this->view('email.accountcreate', ['data' => $this->return_array])
                    ->from('reactapp@gmail.com', 'Reat App')
                    ->subject('Your Account Created')
                    ->replyTo('reactapp@gmail.com', 'Reat App');
    }
}
