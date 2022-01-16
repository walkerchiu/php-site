<?php

namespace WalkerChiu\Site\Events;

use Illuminate\Auth\Events\Attempting as BaseClass;
use WalkerChiu\Site\Events\EventTrait;

class Attempting extends BaseClass
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
     * @param String  $guard
     * @param Array   $credentials
     * @param Bool    $remember
     * @return void
     */
    public function __construct($guard, $credentials, $remember)
    {
        $this->guard       = $guard;
        $this->remember    = $remember;
        $this->credentials = $credentials;

        $this->site = $this->getSite();
    }
}
