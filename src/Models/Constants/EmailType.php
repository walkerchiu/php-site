<?php

namespace WalkerChiu\Site\Models\Constants;

/**
 * @license MIT
 * @package WalkerChiu\Site
 *
 *
 */

class EmailType
{
    /**
     * @return Array
     */
    public static function getCodes(): array
    {
        $items = [];
        $types = self::all();
        foreach ($types as $code => $type) {
            array_push($items, $code);
        }

        return $items;
    }

    /**
     * @param Bool  $onlyVaild
     * @return Array
     */
    public static function options($onlyVaild = false): array
    {
        $items = $onlyVaild ? [] : ['' => trans('php-core::system.null')];
        $types = self::all();
        foreach ($types as $key => $value) {
            $lang = trans('php-site::email.emailType.'.$key);
            $items = array_merge($items, [$key => $lang]);
        }

        return $items;
    }

    /**
     * @return Array
     */
    public static function all(): array
    {
        return [
            'general'        => 'General notice',
            'verifyEmail'    => 'Verify email address',
            'emailVerified'  => 'Notify when email address is verified',
            'registered'     => 'Verify email when sign up',
            'login'          => 'Notify when sign in suceesfully',
            'loginFailed'    => 'Notify when sign in failed',
            'passwordForgot' => 'Verify email when forgot password',
            'passwordReset'  => 'Notify when password is reset',
            'checkout'       => 'Notify when make a order',
            'order'          => 'Notify when change order state',
            'invoice'        => 'Notify when payment accepted',
            'preparing'      => 'Notify when a order is preparing',
            'cancel'         => 'Notify when a order is cancelled',
            'picked'         => 'Notify when items are picked',
            'reject'         => 'Notify when a order is rejected',
            'backorder'      => 'Notify when a order is backordered',
            'shipping'       => 'Notify when items are shipping',
            'delivered'      => 'Notify when items was delivered',
            'return'         => 'Notify when something is returned',
            'confirming'     => 'Notify when something is confirming',
            'confirmed'      => 'Notify when a confirmation is completed',
            'refund'         => 'Notify on refund',
            'refunded'       => 'Notify when refund is completed',
            'abort'          => 'Notify when a order is aborted'
        ];
    }
}
