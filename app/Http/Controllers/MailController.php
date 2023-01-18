<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Mail;

class MailController extends Controller
{
    /**
     * Send HTML email
     */
    public $email;
    public function htmlmail($email,$verification_code)
    {
        $this->email=$email;
        $data = array('name'=>"TaxiApp");
        $data = array('verification_code'=>$verification_code);
        // Path or name to the blade template to be rendered
        $template_path = 'email_template';

        Mail::send($template_path, $data, function($message) {
            // Set the receiver and subject of the mail.
            $message->to($this->email, 'Receiver Name')->subject('TaxiApp Verification Email');
            // Set the sender
            $message->from('taxiappverify@outlook.com','TaxiApp');
        });

        return "Basic email sent, check your inbox.";
    }
}
