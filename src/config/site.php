<?php

/**
 * @license MIT
 * @package WalkerChiu\Site
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Switch association of package to On or Off
    |--------------------------------------------------------------------------
    |
    | When you set someone On:
    |     1. Its Foreign Key Constraints will be created together with data table.
    |     2. You may need to change the corresponding class settings in the config/wk-core.php.
    |
    | When you set someone Off:
    |     1. Association check will not be performed on FormRequest and Observer.
    |     2. Cleaner and Initializer will not handle tasks related to it.
    |
    | Note:
    |     The association still exists, which means you can still access related objects.
    |
    */
    'onoff' => [
        'core-lang_core' => 0,

        'user' => 1,

        'account'            => 1,
        'api'                => 0,
        'currency'           => 0,
        'firewall'           => 0,
        'group'              => 0,
        'mall-cart'          => 0,
        'mall-order'         => 0,
        'mall-shelf'         => 0,
        'mall-tablerate'     => 0,
        'mall-wishlist'      => 0,
        'morph-address'      => 1,
        'morph-board'        => 0,
        'morph-category'     => 1,
        'morph-comment'      => 0,
        'morph-image'        => 1,
        'morph-registration' => 0,
        'morph-tag'          => 0,
        'morph-link'         => 0,
        'newsletter'         => 0,
        'payment'            => 0,
        'role'               => 1,
        'rule'               => 0,
        'rule-hit'           => 0
    ],

    /*
    |--------------------------------------------------------------------------
    | Lang Log
    |--------------------------------------------------------------------------
    |
    | 0: Don't keep data.
    | 1: Keep data.
    |
    */
    'lang_log' => 0,

    /*
    |--------------------------------------------------------------------------
    | Output Data Format from Repository
    |--------------------------------------------------------------------------
    |
    | null:                  Query.
    | query:                 Query.
    | collection:            Query collection.
    | collection_pagination: Query collection with pagination.
    | array:                 Array.
    | array_pagination:      Array with pagination.
    |
    */
    'output_format' => null,

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    */
    'pagination' => [
        'pageName' => 'page',
        'perPage'  => 15
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Delete
    |--------------------------------------------------------------------------
    |
    | 0: Disable.
    | 1: Enable.
    |
    */
    'soft_delete' => 1,

    /*
    |--------------------------------------------------------------------------
    | Editor
    |--------------------------------------------------------------------------
    |
    | Options: Text, HTML, CommonMark, Markdown, WYSIWYG
    |
    */
    'editor' => [
        'layout'  => 'HTML',
        'email'   => 'HTML',
        'shelf'   => 'WYSIWYG',
        'board'   => 'WYSIWYG',
        'group'   => 'WYSIWYG',
        'review'  => 'Text',
        'comment' => 'Text'
    ],

    /*
    |--------------------------------------------------------------------------
    | Command
    |--------------------------------------------------------------------------
    |
    | Location of Commands.
    |
    */
    'command' => [
        'cleaner'     => 'WalkerChiu\Site\Console\Commands\SiteCleaner',
        'initializer' => 'WalkerChiu\Site\Console\Commands\SiteInitializer'
    ],

    /*
    |--------------------------------------------------------------------------
    | Initializer
    |--------------------------------------------------------------------------
    */
    'initializer' => [
        'admin' => [
            'name'     => 'admin',
            'email'    => 'admin@example.com',
            'password' => env('site_ADMIN_PASSWORD', '!QAZ2wsx'),
            'address' => [
                'type'          => 'contact',
                'phone'         => null,
                'email'         => 'admin@example.com',
                'name'          => 'admin',
                'area'          => 'TWN',
                'address_line1' => 'Administrative District',
                'address_line2' => 'Street Address',
            ]
        ],
        'site' => [
            'name'               => env('APP_NAME', 'Example Site'),
            'identifier'         => env('APP_DOMAIN', 'localhost'),
            'language'           => config('wk-core.language'),
            'language_supported' => ['en_us', 'zh_tw'],
            'timezone'           => config('wk-core.timezone'),
            'area_supported'     => ['TWN'],
            'currency_id'        => config('wk-currency.currency_id', null),
            'smtp_host'          => env('MAIL_HOST'),
            'smtp_port'          => env('MAIL_PORT'),
            'smtp_encryption'    => env('MAIL_ENCRYPTION'),
            'smtp_username'      => env('MAIL_USERNAME'),
            'smtp_password'      => env('MAIL_PASSWORD'),
            'address' => [
                'type'          => 'site',
                'phone'         => '(123) 1234-5678',
                'email'         => 'contact@example.com',
                'name'          => 'system',
                'area'          => 'TWN',
                'address_line1' => 'Administrative District',
                'address_line2' => 'Street Address',
                'guide'         => 'Monday - Friday: 9:00 - 19:00'
            ]
        ],
        'categories' => [
            'system' => [
                'icon' => 'fas fa-fw fa-cogs',
                'data' => [
                    'schedule' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'template' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'skin' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'plugin' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'interface' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'backup' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'log' => [
                        'icon' => '',
                        'data' => []
                    ]
                ]
            ],
            'application' => [
                'icon' => 'fas fa-fw fa-store',
                'data' => [
                    'site' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'board' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'layout' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'email' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'category' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'currency' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'tag' => [
                        'icon' => '',
                        'data' => []
                    ]
                ]
            ],
            'member' => [
                'icon' => 'fas fa-fw fa-users',
                'data' => [
                    'member' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'role' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'permission' => [
                        'icon' => '',
                        'data' => []
                    ]
                ]
            ],
            'stock' => [
                'icon' => 'fas fa-fw fa-list',
                'data' => [
                    'product' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'catalog' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'stock' => [
                        'icon' => '',
                        'data' => []
                    ]
                ]
            ],
            'sales' => [
                'icon' => 'fas fa-fw fa-briefcase',
                'data' => [
                    'order' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'payment' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'shipment' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'promotion' => [
                        'icon' => '',
                        'data' => [
                            'coupon' => [
                                'icon' => '',
                                'data' => []
                            ],
                            'point' => [
                                'icon' => '',
                                'data' => []
                            ],
                            'tablerate' => [
                                'icon' => '',
                                'data' => []
                            ]
                        ]
                    ]
                ]
            ],
            'services' => [
                'icon' => 'fas fa-file-signature',
                'data' => [
                    'newsletter' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'message' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'comment' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'report' => [
                        'icon' => '',
                        'data' => []
                    ]
                ]
            ],
            'statistics' => [
                'icon' => 'fas fa-fw fa-chart-area',
                'data' => [
                    'member' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'stock' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'order' => [
                        'icon' => '',
                        'data' => []
                    ],
                    'promotion' => [
                        'icon' => '',
                        'data' => [
                            'coupon' => [
                                'icon' => '',
                                'data' => []
                            ],
                            'point' => [
                                'icon' => '',
                                'data' => []
                            ],
                            'tablerate' => [
                                'icon' => '',
                                'data' => []
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'channels' => [
            'default' => [
                'serial'     => null,
                'order'      => 1,
                'is_enabled' => 1,
                'name'       => 'Shopping Cart'
            ]
        ],
        'email' => [
            'general' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'General notice',
                'subject' => 'General notice'
            ],
            'verifyEmail' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Verify Email Address',
                'subject' => 'Verify Email Address'
            ],
            'emailVerified' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Verified Email Address',
                'subject' => 'Verified Email Address'
            ],
            'registered' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Registered Notice',
                'subject' => 'Welcome to join {{ $site_name }}'
            ],
            'login' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Login Notice',
                'subject' => 'You are login at {{ $login_at }}'
            ],
            'loginFailed' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Login Notice (Failure)',
                'subject' => 'You tried to login at {{ $login_at }}, but failed'
            ],
            'passwordForgot' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Reset Password Notice',
                'subject' => 'Password reset link'
            ],
            'passwordReset' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Reset Password Notice',
                'subject' => 'Password reset successfully'
            ],
            'checkout' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Checkout Notice',
                'subject' => 'You have checkout an order'
            ],
            'order' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Order Notice',
                'subject' => 'You have placed an order'
            ],
            'invoice' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Invoice Notice',
                'subject' => 'Payment Accepted'
            ],
            'preparing' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Prepar Notice',
                'subject' => 'Your order is preparing'
            ],
            'cancel'    => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Cancel Notice',
                'subject' => 'Your order is cancelled'
            ],
            'picked' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Picked Notice',
                'subject' => 'Your items are picked'
            ],
            'reject' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Reject Notice',
                'subject' => 'Your order is rejected'
            ],
            'backorder' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Backorder Notice',
                'subject' => 'Your order is backordered'
            ],
            'shipping' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Shipping Notice',
                'subject' => 'Your items are shipping'
            ],
            'delivered' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Delivered Notice',
                'subject' => 'Your items are delivered'
            ],
            'return' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Return Notice',
                'subject' => 'Something is returned'
            ],
            'confirming' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Confirming Notice',
                'subject' => 'Something is confirming'
            ],
            'confirmed' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Confirmed Notice',
                'subject' => 'A confirmation is completed'
            ],
            'refund' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Refund Notice',
                'subject' => 'Refunding'
            ],
            'refunded' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Refunded Notice',
                'subject' => 'Refund completed'
            ],
            'abort' => [
                'onoff'   => 1,
                'serial'  => null,
                'name'    => 'Abort Notice',
                'subject' => 'Your order is aborted'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Register Event
    |--------------------------------------------------------------------------
    */
    'register_event' => [
        'VerifyEmail'    => 1,
        'EmailVerified'  => 1,
        'PasswordForgot' => 1,
        'PasswordReset'  => 1,
        'Registered'     => 1,
        'Authenticated'  => 1,
        'AuthFailed'     => 1
    ],

    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */
    'client' => [
        'mode' => null,
        'url'  => env('FRONT_URL', 'http://localhost'),
        'link' => [
            'backend'         => 'admin',
            'home'            => 'home',
            'login'           => 'login',
            'email-verify'    => 'email/verify',
            'email-resend'    => 'email/resend',
            'password-forgot' => 'password/forgot',
            'password-reset'  => 'password/reset'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | SMTP
    |--------------------------------------------------------------------------
    */
    'smtp' => [
        'fixed_sender' => [
            'onoff' => 1,
            'email' => env('MAIL_FROM_ADDRESS')
        ],
        'encryption_supported' => ['ssl', 'tls']
    ]
];
