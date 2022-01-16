<?php

namespace WalkerChiu\Site\Events;

use Illuminate\Queue\SerializesModels;
use WalkerChiu\Site\Events\EventTrait;

class CurrentDeviceLogout
{
    use EventTrait;

    /**
     * Site.
     *
     * @var Site
     */
    public $site;



    /**
     * Create a new event instance.
     *
     * @param String                                      $guard
     * @param \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($guard, $user)
    {
        $this->user  = $user;
        $this->guard = $guard;

        $this->site = $this->getSite();
    }
}
