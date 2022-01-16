<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site: Site
    |--------------------------------------------------------------------------
    |
    */

    'type'               => 'Type',
    'serial'             => 'Serial',
    'vat'                => 'VAT',
    'identifier'         => 'Domain Name',
        'identifier_placeholder' => 'Example: example.com',
    'language'           => 'Supported',
    'language_supported' => 'Supported Languages',
    'timezone'           => 'Timezone',
    'area_supported'     => 'Supported Regions',
    'currency_id'        => 'Currency',
    'currency_supported' => 'Supported Currencies',

    'smtp_host'          => 'Host',
    'smtp_port'          => 'Port',
    'smtp_encryption'    => 'Encryption',
    'smtp_username'      => 'Username',
    'smtp_password'      => 'Password',

    'email_theme'        => 'Theme',
    'email_style'        => 'Style',
    'email_header'       => 'Header',
    'email_footer'       => 'Footer',

    'view_template'      => 'Template (View)',
    'email_template'     => 'Template (Email)',
    'skin'               => 'Skin',

    'script_head'        => 'Script in Head',
    'script_footer'      => 'Script in Footer',
    'options'            => 'Options',
    'images'             => 'Images',
    'can_guestOrder'     => 'Guest can order',
    'can_guestComment'   => 'Guest can comment',
    'is_main'            => 'Is Main',
    'is_enabled'         => 'Is Enabled',

    'name'               => 'Name',
    'description'        => 'Description',
    'keywords'           => 'Keywords',
    'remarks'            => 'Remarks',

    'list'   => 'Site List',
    'create' => 'Create Site',
    'edit'   => 'Edit Site',

    'form' => [
        'information' => 'Information',
            'basicInfo'   => 'Basic info',
            'addressInfo' => 'Contact info',

        'icon&logo'   => 'Icon & Logo',

        'link'        => 'Link',
        'smtp'        => 'SMTP',
        'email' => [
            'header'  => 'Email',
            'setting' => 'Template',
            'theme'   => 'Theme',
            'email'   => 'Email'
        ],
        'layout' => [
            'header' => 'Layout',
            'theme'  => 'Theme'
        ]
    ],

    'delete' => [
        'header' => 'Delete Site',
        'body'   => 'Are you sure you want to delete this site?'
    ]
];
