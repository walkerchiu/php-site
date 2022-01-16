<?php

namespace WalkerChiu\Site\Models\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use WalkerChiu\Site\Models\Services\EmailSender;

class EmailSenderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $parameters;
    protected $receiver;
    protected $sender;
    protected $replyTo;
    protected $cc;
    protected $bcc;



    /**
     * Create a new job instance.
     *
     * @param Array  $parameters
     * @param Array  $receiver
     * @param Array  $sender
     * @param Array  $replyTo
     * @param Array  $cc
     * @param Array  $bcc
     * @return void
     */
    public function __construct(array $parameters, array $receiver, array $sender, $replyTo = [], $cc = [], $bcc = [])
    {
        $this->parameters = $this->substitute($parameters);
        $this->receiver   = $receiver;
        $this->sender     = $sender;
        $this->replyTo    = $replyTo;
        $this->cc         = $cc;
        $this->bcc        = $bcc;
    }

    /**
     * Substitute variables
     *
     * @param Array  $parameters
     * @return Array
     */
    public function substitute(array $parameters): array
    {
        $items = ['subject', 'header', 'content', 'footer'];
        foreach ($parameters['variables'] as $key => $value) {
            if (is_array($value))
                $value = json_encode($value);

            foreach ($items as $item) {
                $parameters[$item] = preg_replace('/\{\{(\s*)\$'.$key.'(\s*)\}\}/is', $value, $parameters[$item]);
            }
        }

        return $parameters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new EmailSender($this->parameters, $this->receiver, $this->sender, $this->replyTo, $this->cc, $this->bcc);
        Mail::send($email);
    }
}
