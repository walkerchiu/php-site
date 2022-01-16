<?php

namespace WalkerChiu\Site\Events;

use Illuminate\Auth\Events\PasswordReset as BaseClass;
use WalkerChiu\Site\Events\EventTrait;

class PasswordForgot extends BaseClass
{
    use EventTrait;

    /**
     * Site.
     *
     * @var Site
     */
    public $site;

    /**
     * Token.
     *
     * @var String
     */
    public $token;

    /**
     * Is Admin.
     *
     * @var Bool
     */
    public $admin;



    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param Bool                                        $admin
     * @return void
     */
    public function __construct($user, $token, $admin)
    {
        $this->user  = $user;
        $this->token = $token;
        $this->admin = $admin;

        $this->site = $this->getSite();
    }
}
