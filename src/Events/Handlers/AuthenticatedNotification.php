<?php

namespace WalkerChiu\Site\Events\Handlers;

use Carbon\Carbon;
use WalkerChiu\Site\Events\Authenticated;
use WalkerChiu\Site\Events\Handlers\Notification;

class AuthenticatedNotification extends Notification
{
    /**
     * Handle the event.
     *
     * @param \WalkerChiu\Site\Events\Authenticated  $event
     * @return void
     */
    public function handle(Authenticated $event)
    {
        $this->site = $event->site;
        $this->user = $event->user;

        if ($this->user->hasAttribute('login_at'))
            $this->user->update(['login_at' => Carbon::now()]);

        $nowtime = Carbon::now()->setTimezone($this->site->timezone);

        $parameters = [
            'login_at' => isset($this->user->login_at) ? $this->user->login_at : $nowtime
        ];

        if (config('wk-site.onoff.account')) {
            if ($this->user->profile->notice_login)
                $this->service->email($this->site, 'login', 'contact', $this->user, $parameters);
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \WalkerChiu\Site\Events\Authenticated  $event
     * @param \Exception                             $exception
     * @return void
     */
    public function failed(Authenticated $event, $exception)
    {
        //
    }
}
