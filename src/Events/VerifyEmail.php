<?php

namespace WalkerChiu\Site\Events;

use Illuminate\Auth\Events\Registered as BaseClass;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use WalkerChiu\Site\Events\EventTrait;

class VerifyEmail extends BaseClass
{
    use EventTrait;

    /**
     * Site.
     *
     * @var Site
     */
    public $site;

    /**
     * Verification URL.
     *
     * @var String
     */
    public $link;



    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->link = $this->verificationUrl($user, config('wk-site.client.mode'));

        $this->site = $this->getSite();
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param Mixed   $notifiable
     * @param String  $mode
     * @return String
     */
    protected function verificationUrl($notifiable, $client = null)
    {
        if (empty($mode)) {
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        } elseif ($mode == 'lighthouse-graphql-passport') {
            $payload = $this->getToken($notifiable);
    
            if (empty(config('lighthouse-graphql-passport.verify_email.base_url')))
                return config('wk-site.client.url') .'/'. config('wk-site.client.link.email-verify') .'?token='. $payload;

            return config('lighthouse-graphql-passport.verify_email.base_url') .'?token='. $payload;
        } else {
            return config('wk-site.client.url') .'/'. config('wk-site.client.link.email-verify') .'?token='. sha1($notifiable->getEmailForVerification());
        }
    }

    /**
     * Get a token for the given notifiable.
     *
     * @param Mixed  $notifiable
     *
     * @return String
     */
    protected function getToken($notifiable)
    {
        return base64_encode(json_encode([
            'id'         => $notifiable->getKey(),
            'hash'       => encrypt($notifiable->getEmailForVerification()),
            'expiration' => encrypt(Carbon::now()->addMinutes(10)->toIso8601String()),
        ]));
    }
}
