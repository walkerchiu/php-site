<?php

namespace WalkerChiu\Site\Events;

use Illuminate\Auth\Events\PasswordReset as BaseClass;
use WalkerChiu\Site\Events\EventTrait;

class PasswordReset extends BaseClass
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
     * @param \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;

        $this->site = $this->getSite();
    }
}
