<?php

namespace WalkerChiu\Site\Events\Handlers;

use WalkerChiu\Site\Events\Registered;
use WalkerChiu\Site\Events\Handlers\Notification;

class RegisteredNotification extends Notification
{
    /**
     * Handle the event.
     *
     * @param \WalkerChiu\Site\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $this->site = $event->site;
        $this->user = $event->user;

        $parameters = [
            'created_at' => $this->user->created_at
        ];

        $this->service->email($this->site, 'registered', 'user', $this->user, $parameters);
    }

    /**
     * Handle a job failure.
     *
     * @param \WalkerChiu\Site\Events\Registered  $event
     * @param \Exception                          $exception
     * @return void
     */
    public function failed(Registered $event, $exception)
    {
        //
    }
}
