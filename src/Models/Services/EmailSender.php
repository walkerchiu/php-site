<?php

namespace WalkerChiu\Site\Models\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class EmailSender extends Mailable
{
    use Queueable, SerializesModels;



    /**
     * Create a new sender instance.
     *
     * @param Array  $parameters
     * @param Array  $receiver
     * @param Array  $sender
     * @param Array  $replyTo
     * @param Array  $cc
     * @param Array  $bcc
     * @return void
     */
    public function __construct(array $parameters, array $receiver, array $sender, array $replyTo, array $cc, array $bcc)
    {
        $this->subject  = $parameters['subject'];
        $this->viewData = [
            'email_theme'  => $parameters['email_theme'],
            'email_style'  => $parameters['email_style'],
            'email_header' => $parameters['email_header'],
            'email_footer' => $parameters['email_footer'],
            'style'   => $parameters['style'],
            'header'  => $parameters['header'],
            'content' => $parameters['content'],
            'footer'  => $parameters['footer']
        ];
        $this->to      = $receiver;
        $this->from    = $sender;
        $this->replyTo = $replyTo;
        $this->cc      = $cc;
        $this->bcc     = $bcc;

        config()->set($parameters['smtp']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (config('wk-site.smtp.fixed_sender.onoff'))
            $this->from['email'] = config('wk-site.smtp.fixed_sender.email');

        $email = $this->from($this->from['email'], $this->from['name']);

        return View::exists('vendor.php-site.emails.container') ?
                    $email->view('vendor.php-site.emails.container') :
                    $email->view('php-site::emails.container');
    }
}
