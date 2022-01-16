<?php

namespace WalkerChiu\Site\Events\Subscribers;

class UserEventSubscriber
{
    /**
     * Register the handlers for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        if (config('wk-site.register_event.VerifyEmail'))
            $events->listen(
                'WalkerChiu\Site\Events\VerifyEmail',
                'WalkerChiu\Site\Events\Handlers\EmailVerificationNotification'
            );

        if (config('wk-site.register_event.EmailVerified'))
            $events->listen(
                'WalkerChiu\Site\Events\EmailVerified',
                'WalkerChiu\Site\Events\Handlers\EmailVerificationNotification'
            );

        if (config('wk-site.register_event.PasswordForgot'))
            $events->listen(
                'WalkerChiu\Site\Events\PasswordForgot',
                'WalkerChiu\Site\Events\Handlers\EmailVerificationNotification'
            );

        if (config('wk-site.register_event.PasswordReset'))
            $events->listen(
                'WalkerChiu\Site\Events\PasswordReset',
                'WalkerChiu\Site\Events\Handlers\EmailVerificationNotification'
            );

        if (config('wk-site.register_event.Registered'))
            $events->listen(
                'WalkerChiu\Site\Events\Registered',
                'WalkerChiu\Site\Events\Handlers\RegisteredNotification'
            );

        if (config('wk-site.register_event.Authenticated'))
            $events->listen(
                'WalkerChiu\Site\Events\Authenticated',
                'WalkerChiu\Site\Events\Handlers\AuthenticatedNotification'
            );
    }
}
