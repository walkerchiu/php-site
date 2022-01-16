<?php

namespace WalkerChiu\Site\Models\Entities;

use WalkerChiu\Site\Events\Attempting;
use WalkerChiu\Site\Events\Authenticated;
use WalkerChiu\Site\Events\AuthFailed;
use WalkerChiu\Site\Events\EmailVerified;
use WalkerChiu\Site\Events\PasswordForgot;
use WalkerChiu\Site\Events\PasswordReset;
use WalkerChiu\Site\Events\Registered;
use WalkerChiu\Site\Events\VerifyEmail;

trait EmailUserTrait
{
    /**
     * Attempting.
     * 
     * @param $guard
     * @param Bool  $remember
     * @return void
     */
    public function notifyAttempting($guard, bool $remember)
    {
        event(new Attempting($guard, $this, $remember));
    }

    /**
     * Authenticated.
     *
     * @param $guard
     * @return void
     */
    public function notifyAuthenticated($guard)
    {
        event(new Authenticated($guard, $this));
    }

    /**
     * AuthFailed.
     * 
     * @param $guard
     * @param Array  $credentials
     * @return void
     */
    public function notifyAuthFailed($guard, array $credentials)
    {
        event(new AuthFailed($guard, $this, $credentials));
    }

    /**
     * Email Verified.
     * 
     * @return void
     */
    public function notifyEmailVerified()
    {
        event(new EmailVerified($this));
    }

    /**
     * Send the password reset link.
     * 
     * @param String  $token
     * @param Bool    $admin
     * @return void
     */
    public function notifyPasswordLink(string $token, $admin = false)
    {
        event(new PasswordForgot($this, $token, $admin));
    }

    /**
     * PasswordReset.
     * 
     * @return void
     */
    public function notifyPasswordReset()
    {
        event(new PasswordReset($this));
    }

    /**
     * Registered.
     * 
     * @return void
     */
    public function notifyRegistered()
    {
        event(new Registered($this));
    }

    /**
     * Verify Email.
     * 
     * @return void
     */
    public function notifyVerifyEmail()
    {
        event(new VerifyEmail($this));
    }
}
