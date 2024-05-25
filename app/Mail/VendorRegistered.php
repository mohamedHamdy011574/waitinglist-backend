<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class VendorRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('vendors.email_messages.subject'))
                    ->view('emails.vendor_registered');
                        // ->view('resources/views/vendor/mail/html/default');

        // // return $this->view('vendor.mail.html.default');
        // return $this->from(Setting::get('from_email'))
        //             ->view('mails.demo')
        //             ->text('mails.demo_plain')
        //             ->with(
        //               [
        //                     'testVarOne' => '1',
        //                     'testVarTwo' => '2',
        //               ]);
    }
}
