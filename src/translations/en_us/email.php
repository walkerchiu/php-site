<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site: Email
    |--------------------------------------------------------------------------
    |
    */

    'site_id'     => 'Site ID',
    'site_name'   => 'Site Name',
    'type'        => 'Type',
    'serial'      => 'Serial',
    'is_enabled'  => 'Is Enabled',

    'name'        => 'Name',
    'description' => 'Description',
    'subject'     => 'Subject',
    'content'     => 'Content',

    'list'   => 'Email List',
    'create' => 'Create Email',
    'edit'   => 'Edit Email',

    'form' => [
        'email' => [
            'information' => 'Email',
                'basicInfo' => 'Basic info',
                'body'      => 'Body'
        ],
        'shared' => [
            'header' => 'Shared Content'
        ]
    ],

    'delete' => [
        'header' => 'Delete Email',
        'body'   => 'Are you sure you want to delete this email?'
    ],

    'emailType' => [
        'general'        => 'General',
        'verifyEmail'    => 'Verify Email',
        'emailVerified'  => 'Email Verified',
        'registered'     => 'Registered',
        'login'          => 'LoginSuceesfully',
        'loginFailed'    => 'loginFailed',
        'passwordForgot' => 'Forgot Password',
        'passwordReset'  => 'Password Reset',
        'checkout'       => 'Checkout',
        'order'          => 'Order',
        'invoice'        => 'Invoice',
        'preparing'      => 'Preparing',
        'cancel'         => 'Cancel',
        'picked'         => 'Picked',
        'reject'         => 'Reject',
        'backorder'      => 'Backorder',
        'shipping'       => 'Shipping',
        'delivered'      => 'Delivered',
        'return'         => 'Return',
        'confirming'     => 'Confirming',
        'confirmed'      => 'Confirmed',
        'refund'         => 'Refund',
        'refunded'       => 'Refunded',
        'abort'          => 'Abort'
    ]
];
