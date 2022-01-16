<?php

namespace WalkerChiu\Site\Events;

use Illuminate\Auth\Events\Login as BaseClass;
use WalkerChiu\Site\Events\EventTrait;

class Login extends BaseClass
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
     * @param Bool                                        $remember
     * @return void
     */
    public function __construct($guard, $user, $remember)
    {
        $this->user     = $user;
        $this->guard    = $guard;
        $this->remember = $remember;

        $this->site = $this->getSite();
    }
}
